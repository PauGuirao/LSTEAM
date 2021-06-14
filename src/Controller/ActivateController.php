<?php
declare(strict_types=1);
namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

use SallePW\SlimApp\Middleware\SendMailMiddleware;

use DateTime;
use Exception;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;


session_start();

final class ActivateController
{

    private Twig $twig;
    private UserRepository $userRepository;
    private SendMailMiddleware $mailMiddleware;

    public function __construct(Twig $twig, UserRepository $userRepository, SendMailMiddleware $mailMiddleware)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
        $this->mailMiddleware = $mailMiddleware;
    }
    
    public function showActivate(Request $request, Response $response)
    {
        //If there is a token set and the user is inactive, we update him, else show error
        if(isset($_GET['token'])) {
            $token = $_GET['token'];
            $row = $this->userRepository->checkToken($token);
            if($row){
                //Update the user and send another email
                $this->userRepository->updateStatus($token);
                $money = 50;
                $this->userRepository->addMoney(intval($row->id),$money);
                $email = $row->email;
                $username = $row->username;
                //Send a mail with the 50 dolar credit
                
                $this->mailMiddleware->sendActivationMail($username, $email);

                //Show the correct view
                return $this->twig->render($response, 'activate.twig',
                [
                    'tokenError' => false,
                ]);
                
            }else{
                return $this->twig->render($response, 'activate.twig',
                [
                    'tokenError' => true,
                ]);
            }
        }else{
             //Redirect to login
             return $response->withHeader('Location', '/login')->withStatus(200);
        }
    }



}