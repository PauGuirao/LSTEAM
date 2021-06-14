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

final class MyGamesController
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
    
    public function showMyGames(Request $request, Response $response)
    {
        if(isset( $_SESSION['user_id'] )){
            $rowUserGames = $this->userGameRepository->getGames(intval($_SESSION['user_id']));
            $rowProfile = $this->userRepository->searchId(intval($_SESSION['user_id']));

            $result = array();
            for($j = 0; $j < count($rowUserGames); $j++){

                array_push($result, $this->gameRepository->getGame($rowUserGames[$j]->game_id()));

            }
            if($rowProfile){
                $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
                return $this->twig->render(
                    $response,
                    'MyGames.twig',
                    [
                        'formImg' => $img,
                        'formData' => $rowProfile,
                        'formMethod' => "POST",
                        'json' => $result
                    ]
                );
            }

            return $this->twig->render($response, 'MyGames.twig', ['json' => $json]); 
            echo($_SESSION['user_id']);
            
            
            
        }else{
            return $response->withHeader('Location', '/login')->withStatus(200);
        }
    }

    public function apply(Request $request, Response $response): Response
    {
        return $response->withStatus(201);
    }
}