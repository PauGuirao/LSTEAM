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

final class ProfileController
{

    private Twig $twig;
    private UserRepository $userRepository;

    // We use this const to define the extensions that we are going to allow
    private const ALLOWED_EXTENSIONS = ['jpg', 'png', 'pdf'];

    public function __construct(Twig $twig, UserRepository $userRepository)
    {
        $this->twig = $twig;
        $this->userRepository = $userRepository;
    }
    
    public function showProfile(Request $request, Response $response)
    {
        
        if(isset( $_SESSION['user_id'] )){
            $row = $this->userRepository->searchId(intval($_SESSION['user_id']));
            if($row){
                $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
                return $this->twig->render(
                    $response,
                    'profile.twig',
                    [
                        'formImg' => $img,
                        'formData' => $row,
                        'formMethod' => "POST"
                    ]
                );
            }
        }else{
            return $response->withHeader('Location', '/login')->withStatus(200);
        }

    }

    //Function that validates and updates the phone and profile images
    public function updateProfile(Request $request, Response $response): Response
    {
        //Check if the submit button has been pressed
        if(isset($_POST['submit'])){
            $errors = [];
            $warnings = [];
            //Get the phone info
            $phone = $_POST['phone'];
            //Check if phone has errors
            if(!empty($phone)){
                if(!preg_match("/^(\+34|0034|34)?[6|7|9][0-9]{8}$/", $phone)){
                    $errors['phone'] = 'Please insert valid spanish phone number';
                }
            }

            //Check if a file has been submited
            if(!$_FILES['file']['error'] == 4){
                //Check the file and info and phone info
                $file = $_FILES['file'];
                $fileName = $file['name'];
                $fileTmpName = $file['tmp_name'];
                $fileSize = $file['size'];
                $fileError = $file['error'];
                $fileType = $file['type'];
                $fileExt = explode('.',$fileName);
                $fileActualExt = strtolower(end($fileExt));
                $allowed = array('jpg','png');

                //Check if the image extension is allowed
                if(in_array($fileActualExt,$allowed)){
                    if($fileError === 0){
                        //Check if the file size is less than 1MB
                        if($fileSize < 1000000){
                            //Check if the file is less or equal to 500x500
                            list($width, $height, $type, $attr) = getimagesize($fileTmpName);
                            if($width <= 500 && $height <= 500){
                                //Generate UUID and rename the new profile image
                                $uuid = Uuid::uuid4();
                                $fileNameNew = "image_".$uuid->toString().".".$fileActualExt;
                                //Move the file to uploads folder
                                move_uploaded_file($fileTmpName,"uploads/".$fileNameNew);
                                //Update the database
                                if(empty($errors)){
                                    //Get old profile image
                                    $image = $this->userRepository->getUserImage(intval($_SESSION['user_id']));
                                    //Update phone and image
                                    $this->userRepository->updateUserImage(intval($_SESSION['user_id']),$uuid->toString());
                                    $this->userRepository->updateUserPhone(intval($_SESSION['user_id']),$phone);
                                    $warnings['updated'] = 'Phone and profile image updated!';

                                    //Delete profile image if is not the default
                                    if($image){
                                        if($image->uuid != '12345678abcdefgh'){
                                            if(file_exists('uploads/image_'.$image->uuid.'.png')){
                                                unlink('uploads/image_'.$image->uuid.'.png');
                                            }else{
                                                unlink('uploads/image_'.$image->uuid.'.jpg');
                                            }
                                            
                                        } 
                                    }
                                }
                                //return $response->withHeader('Location', '/profile')->withStatus(200);
                            }else{
                                $errors['image'] = 'Files must be equal or less than 500x500';
                            }
                            
                        }else{
                            $errors['image'] = 'Only files less than 1MB are accepted';
                        }
                    }
                }else{
                    $errors['image'] = 'Only .png or .jpg are allowed!';
                }

            }else{
                //Check only the phone info
                if(empty($errors)){
                    //Update phone
                    $this->userRepository->updateUserPhone(intval($_SESSION['user_id']),$phone);
                    $warnings['updated'] = 'Data correctly updated!';
                }
            }

            //Display the actualized profile 
            $row = $this->userRepository->searchId(intval($_SESSION['user_id']));
            if($row){
                $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
                return $this->twig->render(
                    $response,
                    'profile.twig',
                    [
                        'formWarning' => $warnings,
                        'formImg' => $img,
                        'formErrors' => $errors,
                        'formData' => $row,
                        'formMethod' => "POST"
                    ]
                );
            }
        }


    }

    public function showPasswordForm(Request $request, Response $response)
    {
        if(isset( $_SESSION['user_id'] )){
            $row = $this->userRepository->searchId(intval($_SESSION['user_id']));
            if($row){
                $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
                return $this->twig->render(
                    $response,
                    'changePassword.twig',
                    [
                        'formImg' => $img
                    ]
                );
            }
        }else{
            return $response->withHeader('Location', '/login')->withStatus(200);
        }
        
    }

    public function changePassword(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();

            $oldPass = $data['oldPassword'];
            $newPass = $data['newPassword'];
            $confirmPass = $data['confirmPassword'];
            $errors = [];

            //Check password errors
            $number = preg_match('@[0-9]@', $newPass);
            $lowercase = preg_match('@[a-z]@', $newPass);
            $uppercase = preg_match('@[A-Z]@', $newPass);
            if(empty($newPass)){
                $errors['newPassword'] = 'Please insert password';
            }elseif (!$lowercase || !$number || strlen($newPass) <= 6 || !$uppercase) {
                $errors['newPassword'] = sprintf('The password is not valid', $newPass);
            }
            if($newPass != $confirmPass){
                $errors['confirmPassword'] = 'Passwords do not match';
            }
            if($oldPass == $newPass){
                $errors['newPassword'] = 'New password is same as old password';
            }

            $img = $this->userRepository->searchProfileImage(intval($_SESSION['user_id']));
            if (empty($errors)) {
                //Search old password
                $row = $this->userRepository->searchUserPassword(intval($_SESSION['user_id']),$oldPass);
                if($row){
                    //Usuari amb aquesta contrasenya existeix
                    $newPass = password_hash($newPass,PASSWORD_DEFAULT);
                    $this->userRepository->updatePassword(intval($_SESSION['user_id']),$newPass);  
                    $errors['updated'] = 'Password updated correctly!';
                    return $this->twig->render(
                        $response,
                        'changePassword.twig',
                        [
                            'formErrors' => $errors,
                            'formData' => $data,
                            'formMethod' => "POST",
                            'formImg' => $img
                        ]
                    );
                }else{
                    $errors['oldPassword'] = 'This password is incorrent';
                    return $this->twig->render(
                        $response,
                        'changePassword.twig',
                        [
                            'formErrors' => $errors,
                            'formData' => $data,
                            'formMethod' => "POST",
                            'formImg' => $img
                        ]
                    );
                }
            }else{
                return $this->twig->render(
                        $response,
                        'changePassword.twig',
                        [
                            'formErrors' => $errors,
                            'formData' => $data,
                            'formMethod' => "POST",
                            'formImg' => $img
                        ]
                );
            }

            
            
        } catch (Exception $exception) {
            // You could render a .twig template here to show the error
            $response->getBody()
                ->write('Unexpected error: ' . $exception->getMessage());
            return $response->withStatus(500);
        }
    }

    private function isValidFormat(string $extension): bool
    {
        return in_array($extension, self::ALLOWED_EXTENSIONS, true);
    }


}