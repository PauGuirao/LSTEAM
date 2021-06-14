<?php
declare(strict_types=1);
namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

use Exception;
use SallePW\SlimApp\Middleware\ValidationMiddleware;
use SallePW\SlimApp\Model\FriendRequest;
use SallePW\SlimApp\Model\UserRepository;

session_start();

final class FriendRequestsController
{

    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(Twig $twig, UserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }

    public function showFriendRequests(Request $request, Response $response)
    {

        if(isset($_SESSION['user_id'])){
            $row = $this->userRepository->searchId(intval($_SESSION['user_id']));
            $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
            if(isset($row)){
                $json = $this->userRepository->getFriendRequests($row->username);

                return $this->twig->render(
                    $response,
                    'friendRequests.twig',
                    [
                        'formImg' => $img,
                        'formData' => $json,
                        'formMethod' => "POST"
                    ]
                );
            }else{

                return $this->twig->render(
                    $response,
                    'friendRequests.twig',
                    [
                        'formImg' => $img,
                        'formData' => array(),
                        'formMethod' => "POST"
                    ]
                );

            }
        }else{

            //Redirect to login
            return $response->withHeader('Location', '/login')->withStatus(200);

        }

    }
    public function showRequestForm(Request $request, Response $response){
        if(isset($_SESSION['user_id'])){
            $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
            return $this->twig->render(
                $response,
                'sendFriendRequest.twig',
                [
                    'formImg' => $img
                ]
            );
        }
        
    }
    public function sendFriendRequest(Request $request, Response $response): Response
    {
        $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));

        try {
            $data = $request->getParsedBody();

            $errors = [];
            $friendUsername = $data['username'];

            $validation = new ValidationMiddleware();
            if(!$validation->validateUsername($friendUsername)){$errors['username'] = 'Please insert valid (numbers and letters) username';}

            if (empty($errors)) {
                $user = $this->userRepository->searchUsername($friendUsername);
                
                //Check if the user is in the datebase
                if($user){
                    //Check if the user has an active account
                    if($user->status == 'Active'){
                        //Check if the user is already a friend
                        $row = $this->userRepository->searchId(intval($_SESSION['user_id']));
                        if(!$this->userRepository->searchFriend($row->username,$friendUsername)){
                            //Check if the user has already send a request to that user
                            if(!$this->userRepository->searchDeclinedRequest($row->username,$friendUsername)){
                                if(!$this->userRepository->searchFriendRequest($row->username,$friendUsername)){
                                    //Send friend request
                                    $this->userRepository->addFriendRequest($row->username,$friendUsername);
                                    //Create warning for the user
                                    $warning = "Request Correctly Sent!";
                                    return $this->twig->render(
                                        $response,
                                        'sendFriendRequest.twig',
                                        [
                                            'formImg' => $img,
                                            'formErrors' => $errors,
                                            'formData' => $data,
                                            'formMethod' => "POST",
                                            'formWarning' => $warning
                                        ]
                                    );
    
                                }else{
                                    $errors['username'] = 'This user has your request already';
                                }
                            }else{
                                $errors['username'] = 'This user has declined the request already';
                            }
                               
                        }else{
                            $errors['username'] = 'This user is already your friend';
                        }
                    }else{
                        $errors['username'] = 'This user doesnt have an active account';
                    }
                }else{
                    $errors['username'] = 'This user doesnt exists'; 
                }
            }else{
                return $this->twig->render(
                        $response,
                        'sendFriendRequest.twig',
                        [
                            'formImg' => $img,
                            'formErrors' => $errors,
                            'formData' => $data,
                            'formMethod' => "POST"
                        ]
                );
            }

            
            
        } catch (Exception $exception) {
            
        }
        return $this->twig->render(
            $response,
            'sendFriendRequest.twig',
            [
                'formImg' => $img,
                'formErrors' => $errors,
                'formData' => $data,
                'formMethod' => "POST"
            ]
        );
    }
    public function acceptFriendRequest(Request $request, Response $response){

        $request_id = intval(substr($_SERVER['REQUEST_URI'], 28));
        $friendRequest = $this->userRepository->getFriendRequest($request_id);

        $user = $this->userRepository->searchId(intval($_SESSION['user_id']));
        
        if($friendRequest){
            if($friendRequest->user_receiver() == $user->username){
                $correcte = 1;
                $this->userRepository->addFriend($friendRequest->user_receiver(), $friendRequest->user_sender(), date('d/m/Y'));
            }else{
                $correcte = -1;
            }
        }else{
            $correcte = 0;
        }

        return $response->withHeader('Location', '/user/friendRequests')->withStatus(200);
    }

    public function declineFriendRequest(Request $request, Response $response){

        $request_id = intval(substr($_SERVER['REQUEST_URI'], 29));
        $friendRequest = $this->userRepository->getFriendRequest($request_id);

        $user = $this->userRepository->searchId(intval($_SESSION['user_id']));
        if($friendRequest){
            
            if($friendRequest->user_receiver() == $user->username){
                $correcte = 1;
                $this->userRepository->declineFriend($friendRequest->user_receiver(), $friendRequest->user_sender());
            }else{
                $correcte = -1;
            }
        }else{
            $correcte = 0;
        }
        
        return $response->withHeader('Location', '/user/friendRequests')->withStatus(200);
    }

}