<?php
declare(strict_types=1);
namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\UploadedFileInterface;
use Slim\Views\Twig;

use DateTime;
use Exception;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\UserRepository;
use Ramsey\Uuid\Uuid;

session_start();

final class WalletController
{

    private Twig $twig;
    private UserRepository $userRepository;

    public function __construct(Twig $twig, UserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }
    
    public function showWallet(Request $request, Response $response)
    {
        if(isset($_SESSION['user_id'])){
            $row = $this->userRepository->searchId(intval($_SESSION['user_id']));
            if($row){
                $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
                $errors = [];
                return $this->twig->render(
                    $response,
                    'wallet.twig',
                    [
                        'formImg' => $img,
                        'errors' => $errors,
                        'data' => $row
                    ]
                );
            }
        }else{
            return $this->twig->render($response, 'login.twig');
        }

    }

    public function updateWallet(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            $money = $data['amount'];
            $validMoney = str_replace(',','.',$money);

            $errors = [];
            $warnings = [];
            if(is_numeric($validMoney)){
                
                if($validMoney > 0){
                    $this->userRepository->addMoney(intval($_SESSION['user_id']),floatval($validMoney));
                    $warnings['added'] = 'Money added correctly!';
                }else{
                    $errors['zero'] = 'Quantity must be greater than 0!';
                }
            }else{
                $errors['zero'] = 'Please insert valid number!';
            }
            

            $row = $this->userRepository->searchId(intval($_SESSION['user_id']));
            if($row){
                $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
                return $this->twig->render(
                    $response,
                    'wallet.twig',
                    [
                        'formImg' => $img,
                        'warnings' => $warnings,
                        'errors' => $errors,
                        'data' => $row
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
}