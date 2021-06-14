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
use SallePW\SlimApp\Model\Repository\DecoratorGameRepository;

session_start();

final class StoreController
{

    private Twig $twig;
    private UserRepository $userRepository;
    private GameRepository $gameRepository;
    private UserGameRepository $userGameRepository;
    private DecoratorGameRepository $decoratorGameRepository;

    public function __construct(Twig $twig, UserRepository $userRepository, GameRepository $gameRepository, UserGameRepository $userGameRepository, DecoratorGameRepository $decoratorGameRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->gameRepository = $gameRepository;
        $this->userGameRepository = $userGameRepository;
        $this->decoratorGameRepository = $decoratorGameRepository;
    }
    
    public function showStore(Request $request, Response $response)
    {
        //Check if the user is logged in
        if(isset($_SESSION['user_id'] )){
            if($this->decoratorGameRepository->cacheExists()){
                $this->decoratorGameRepository->loadFromCache();
                $json = $this->decoratorGameRepository->getGame(0);
            }else{
            //Get the API data
                $json = $this->getAPIData();
            //Save the deals in the database
                $this->saveAPIData($json);
                $this->decoratorGameRepository->writeIntoCache($json);
            }

            //Get the user profile image 
            $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
            //Check if there are notifications to show to the user!
            if(isset($_SESSION['notification'])){
                //If the are notifiactions, we show them
                $noti = $_SESSION['notification'];
                unset($_SESSION['notification']);
                return $this->twig->render(
                    $response,'store.twig',
                    [
                        'formImg' => $img,
                        'formMethod' => "POST",
                        'json' => $json,
                        'formNotifications' => $noti
                    ]
                );
            }else{
                //There are no notifications to show
                return $this->twig->render(
                    $response,'store.twig',
                    [
                        'formImg' => $img,
                        'formMethod' => "POST",
                        'json' => $json
                    ]
                );
            }

        }else{
            return $response->withHeader('Location', '/login')->withStatus(200);
        }
    }

    public function buyGame(Request $request, Response $response){
        //Get the game id from the URL 
        $game_id = intval(substr($_SERVER['REQUEST_URI'], 11));

        //Get the price of the game with the Id
        $p = $this->gameRepository->getGame($game_id)->price();

        //Check if the user has already has the game
        if(!$this->userGameRepository->ownsGame(intval($_SESSION['user_id']), $game_id)){
            //Check if the user has the game in wishlist
            if($this->userGameRepository->inWishlist(intval($_SESSION['user_id']), $game_id)){
                //We want to remove the game from wishlist
                $this->userGameRepository->deleteGameWishlist(intval($_SESSION['user_id']), $game_id);
            }
            //Check if the user has enough money
            if($this->userRepository->checkMoney(intval($_SESSION['user_id'])) >= $p){

                $this->userGameRepository->Save(intval($_SESSION['user_id']), $game_id);
                $this->userRepository->addMoney(intval($_SESSION['user_id']) ,$p*(-1));
                $this->createFlashMessage("Congrats! Game has been added to your library");

            }else{
                $this->createFlashMessage("Error! Not enough credit");
            }

        }else{
            $this->createFlashMessage("Error! Game is already buyed!");
        }
        //Check if we buy the game from the store endpoint or from wishlist endpoint 
        if($_SERVER['HTTP_REFERER'] != 'http://localhost:8030/user/wishlist'){
            header("Location: /store");
            var_dump(headers_sent());
            return $response->withHeader('Location', '/store')->withStatus(300); 
        }else{
            //If we came from wishlist show wishlist
            header("Location: /user/wishlist");
            var_dump(headers_sent());
            return $response->withHeader('Location', '/user/wishlist')->withStatus(300); 
        }
    }

    public function apply(Request $request, Response $response): Response
    {
        return $response->withStatus(201);
    }

    public function getAPIData(){
        //Create the client with the url to request
        $client = new Client(['base_uri' => 'https://www.cheapshark.com/api/1.0/deals','timeout'  => 10.0,]);

        //GET the data
        $r = $client->request('GET', '');
        $json = json_decode($r->getBody()->getContents());
        return $json;
    }

    public function saveAPIData($json){
        for($j = 0; $j < count($json); $j++){
            if(!$this->gameRepository->getGame(intval($json[$j]->gameID))){
                $this->gameRepository->Save(intval($json[$j]->gameID), $json[$j]->title, floatval($json[$j]->normalPrice), $json[$j]->thumb);
            }
        }
    }

    public function createFlashMessage(string $message)
    {
        $_SESSION['notification'] = $message;
    }
}