<?php
declare(strict_types=1);


namespace SallePW\SlimApp\Controller;

use Slim\Views\Twig;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use SallePW\SlimApp\Model\UserRepository;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

session_start();

final class HomeController
{
    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(Twig $twig,UserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function apply(Request $request, Response $response)
    {
        if(isset($_SESSION['user_id'] )){
            $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
            return $this->twig->render(
                $response,'home.twig',
                [
                    'formImg' => $img
                ]
            );
        }else{
            return $this->twig->render($response, 'home.twig');
        } 
    }

    public function goRegister(Request $request, Response $response)
    {
        return $response->withHeader('Location', '/register')->withStatus(200);
    }
}