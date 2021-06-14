<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

use Exception;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;


use SallePW\SlimApp\Middleware\ValidationMiddleware;
use SallePW\SlimApp\Middleware\SendMailMiddleware;


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;


final class RegisterController
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
    
    public function showRegisterForm(Request $request, Response $response)
    {
        return $this->twig->render($response, 'register.twig');
    }

    public function apply(Request $request, Response $response): Response
    {
        try {

            $data = $request->getParsedBody();

            //Initialize errors array
            $errors = [];

            //Variables procedents del submit del registre
            $username = $data['username'];
            $email = $data['mail'];
            $pass1 = $data['password1'];
            $pass2 = $data['password2'];
            $date = $data['birthdate'];
            $phone = $data['phone'];

            //Validation init
            $validation = new ValidationMiddleware();

            //Username errors
            if(!$validation->validateUsername($username)){$errors['username'] = 'Please insert valid (numbers and letters) username';}

            if(empty($errors)){
                //Check if username already exists
                $row = $this->userRepository->searchUsername($username);
                if($row){
                    $errors['username'] = 'This username is already in use!';
                }
            }

            //Mail errors
            if(!$validation->validateMail($email)){$errors['mail'] = sprintf('The email is not valid!', $email);}

            if(empty($errors)){
                //Check if mail already exists
                $row = $this->userRepository->searchUserMail($email);
                if($row){
                    $errors['mail'] = 'User already exists!';
                }
            }
            
            //Password errors
            if(!$validation->validatePassword($pass1)){$errors['password1'] = sprintf('The password is not valid', $pass1);}
            if(!$validation->validateMatch($pass1,$pass2)){$errors['match'] = 'Passwords do not match';}

            //Date errors
            if(!$validation->validateDate($date)){$errors['birthdate'] = 'You must be over 18.';}

            //Phone errors
            if(!$validation->validatePhone($phone)){ $errors['phone'] = 'Please insert valid spanish phone number';}

            //Make sure that errors are empty
            if (!empty($errors)) {
                return $this->twig->render(
                    $response,
                    'register.twig',
                    [
                        'formErrors' => $errors,
                        'formData' => $data,
                        'formMethod' => "POST"
                    ]
                );
            }else{
                //Hash the password
                $data['password1'] = password_hash($data['password1'],PASSWORD_DEFAULT);
                //Create the token
                $token = bin2hex(random_bytes(10));
                //Create the user
                $user = new User(
                    $data['username'] ?? '',
                    $data['mail'] ?? '',
                    $data['password1'] ?? '',
                    $data['birthdate'] ?? '',
                    $data['phone'] ?? '',
                    $token ?? ''
                );
                $this->userRepository->save($user);
                $this->userRepository->addProfileImage($data['username']);


                $done = $this->mailMiddleware->sendValidationMail($email, $token, $this->twig, $errors);

                if($done != false){
                    $errors = $done;
                }

                if($done){
                    return $this->twig->render(
                        $response,
                        'register.twig',
                        [
                            'formErrors' => $errors,
                            'formData' => $data,
                            'formMethod' => "POST"
                        ]
                    );

                }else{

                }

                return $response->withHeader('Location', '/login')->withStatus(200);
            }
            

        } catch (Exception $exception) {
            // You could render a .twig template here to show the error
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }

        return $response->withHeader('Location', '/login')->withStatus(200);
    }

}