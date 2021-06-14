<?php
declare(strict_types=1);
namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use GuzzleHttp\Psr7\Request as Guzzle;
use GuzzleHttp\Client;


use DateTime;
use Exception;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Model\Game;
use SallePW\SlimApp\Model\GameRepository;
use SallePW\SlimApp\Model\UserGame;
use SallePW\SlimApp\Model\UserGameRepository;

session_start();

final class WishlistController
{

    private Twig $twig;
    private UserRepository $userRepository;
    private GameRepository $gameRepository;
    private UserGameRepository $userGameRepository;

    public function __construct(Twig $twig, UserRepository $userRepository, GameRepository $gameRepository, UserGameRepository $userGameRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->gameRepository = $gameRepository;
        $this->userGameRepository = $userGameRepository;
    }
    
    public function showWishlist(Request $request, Response $response)
    {   
        if(isset( $_SESSION['user_id'] )){
            $rowUserGames = $this->userGameRepository->getWishlist(intval($_SESSION['user_id']));
            $rowProfile = $this->userRepository->searchId(intval($_SESSION['user_id']));

            $result = array();
            for($j = 0; $j < count($rowUserGames); $j++){
                array_push($result, $this->gameRepository->getGame($rowUserGames[$j]->game_id()));
            }

            if($rowProfile){
                $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
                return $this->twig->render(
                    $response,
                    'wishlist.twig',
                    [
                        'formImg' => $img,
                        'formData' => $rowProfile,
                        'formMethod' => "POST",
                        'json' => $result
                    ]
                );
            }
        }
    }

    public function addGame(Request $request, Response $response){

        //$gameId = $request->attributes->get('gameId');
        $game_id = intval(substr($_SERVER['REQUEST_URI'], 15));
        //Check if the user owns the game
        if(!$this->userGameRepository->ownsGame(intval($_SESSION['user_id']), $game_id)){
            //Check if the game is already in the wishlist
            if(!$this->userGameRepository->inWishlist(intval($_SESSION['user_id']), $game_id)){                
                $this->userGameRepository->addWishlist(intval($_SESSION['user_id']), $game_id);
                $_SESSION['notification'] = "Congratulations, game correctly added to wishlist!";
            }else{
                $_SESSION['notification'] = "Error, this game is already in the wishlist";
            }
        }else{
            $_SESSION['notification'] = "Error, this game in in the library!";
        }

        header("Location: /store");
        var_dump(headers_sent());
        return $response->withHeader('Location', '/store')->withStatus(300); 
    }

    public function showWishlistGame(Request $request, Response $response){
        if(isset($_SESSION['user_id'])){
            $game_id = intval(substr($_SERVER['REQUEST_URI'], 15));
            $row = $this->userGameRepository->getWishlistGame(intval($_SESSION['user_id']), $game_id);  
            if($row){
                $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
                $game = $this->gameRepository->getGame(intval($row->game_id));
                return $this->twig->render(
                    $response,
                    'wishlistGame.twig',
                    [
                        'formImg' => $img,
                        'formData' => $game
                    ]
                );
            } 
        } 
    }

    public function deleteGame(Request $request, Response $response){
        if(isset($_SESSION['user_id'])){
            $game_id = intval(substr($_SERVER['REQUEST_URI'], 22));
            $this->userGameRepository->deleteGameWishlist(intval($_SESSION['user_id']), $game_id);

            header("Location: /user/wishlist");
            var_dump(headers_sent());
            return $response->withHeader('Location', '/user/wishlist')->withStatus(300); 
        }  
    }

    
    
}