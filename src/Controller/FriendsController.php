<?php
declare(strict_types=1);
namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

use DateTime;
use Exception;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;


session_start();

final class FriendsController
{

    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(Twig $twig, UserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }
    
    public function showFriends(Request $request, Response $response)
    {
        //If there is a token set and the user is inactive, we update him, else show error
        if(isset($_SESSION['user_id'])){
            $row = $this->userRepository->searchId(intval($_SESSION['user_id']));
            if($row){
                $friends = $this->userRepository->getFriends($row->username);
                $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
                return $this->twig->render(
                    $response,
                    'friends.twig',
                    [
                        'formImg' => $img,
                        'formData' => $friends,
                        'formMethod' => "POST"
                    ]
                );
            }
             
        }else{
             //Redirect to login
             return $response->withHeader('Location', '/login')->withStatus(200);
        }

    }



}