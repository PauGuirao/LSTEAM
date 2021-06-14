<?php
declare(strict_types=1);
namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

use SallePW\SlimApp\Middleware\ValidationMiddleware;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;

session_start();

final class LoginController
{

    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(Twig $twig, UserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }
    
    //Function that shows the login form
    public function showLoginForm(Request $request, Response $response)
    {
        return $this->twig->render($response, 'login.twig');
    }

    //Function that checks if the credentials are valid and redirect user to the store
    public function apply(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            //Get field from the form data
            $email = $data['mail'];
            $pass1 = $data['password'];
            $errors = [];

            //Initialize ValidationMiddleware
            $validation = new ValidationMiddleware();

            //Check password errors
            if(!$validation->validatePassword($pass1)){$errors['password1'] = sprintf('The password is not valid', $pass1);}

            if (empty($errors)) {
                $row = $this->userRepository->searchUser($data['mail'],$data['password']);
                //Check if the datebase returns a user
                if($row){
                    //Check if the user is active
                    if($row->status == 'Active'){
                        //Add user to the session ans send user to the store
                        $_SESSION['user_id'] = $row->id;
                        return $response->withHeader('Location', '/store')->withStatus(200); 
                    }else{
                        //Return user to login form and show errors
                        $errors['found'] = 'Please verify your account to log in. Check your email';
                        return $this->twig->render(
                            $response,
                            'login.twig',
                            [
                                'formErrors' => $errors,
                                'formData' => $data,
                                'formMethod' => "POST"
                            ]
                        );
                    }      
                }else{
                    //Return user to login form and show errors
                    $errors['found'] = 'User not found';
                    return $this->twig->render(
                        $response,
                        'login.twig',
                        [
                            'formErrors' => $errors,
                            'formData' => $data,
                            'formMethod' => "POST"
                        ]
                    );
                }
            }else{
                return $this->twig->render(
                        $response,
                        'login.twig',
                        [
                            'formErrors' => $errors,
                            'formData' => $data,
                            'formMethod' => "POST"
                        ]
                );
            }

            
            
        } catch (Exception $exception) {
            // You could render a .twig template here to show the error
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }

        return $response->withStatus(201);
    }

    //Function to logout the user
    public function logout(Request $request, Response $response): Response
    {
        unset($_SESSION["user_id"]);
        return $response->withHeader('Location', '/login')->withStatus(200);
    }
}