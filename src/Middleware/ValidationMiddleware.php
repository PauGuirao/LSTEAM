<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use DateTime;
use DateInterval;

final class ValidationMiddleware
{
    public function __construct(
    ) {}

    public function validateUsername(string $username):bool
    {
        $error = null;
        if(empty($username)){
            $error = 'Please insert username!';
            return false;
        }elseif(!ctype_alnum($username)){
            $error = 'Please insert valid (numbers and letters) username';
            return false;
        }
        return true;
    }

    public function validateMail(string $email):bool
    {
        $error = null;
        if(empty($email)){
            $error = 'Please insert email!';
            return false;
        }elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
            $error = sprintf('The email is not valid!', $email);
            return false;
        }elseif (!preg_match('|@salle.url.edu$|', $email))
        {
            $error = sprintf('The email is not valid!', $email);
            return false;
        }
        return true;
    }

    public function validatePassword(string $pass1):bool
    {
        $error = null;
        $number = preg_match('@[0-9]@', $pass1);
        $lowercase = preg_match('@[a-z]@', $pass1);
        $uppercase = preg_match('@[A-Z]@', $pass1);
        if(empty($pass1)){
            $error = 'Please insert password';
            return false;
        }elseif (!$lowercase || !$number || strlen($pass1) <= 6 || !$uppercase) {
            $error = sprintf('The password is not valid', $pass1);
            return false;
        }
        return true;
    }

    public function validateMatch(string $pass1,string $pass2):bool
    {
        $error = null;
        if($pass1 != $pass2){
            $error = 'Passwords do not match';
            return false;
        }
        return true;
    }

    public function validateDate(string $date):bool
    {
        $error = null;
        //$date is in yyyy-mm-dd, we need to change the format
        if (preg_match("/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((19|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((19|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((19|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/",$date)) {
            $checkDate = str_replace('/', '-', $date);  
            $newDate = date("Y-m-d", strtotime($checkDate));
            //Check if user is 18 or over years old
            $birthday = new DateTime($newDate);
            $birthday->add(new DateInterval("P18Y"));
            if($birthday >= new DateTime()){
                $error = 'You must be over 18.';
                return false;
            }
        }else{
            return false;
        }
        return true;
    }

    public function validatePhone(string $phone):bool
    {
        if(!empty($phone)){
            if(!preg_match("/^(\+34|0034|34)?[6|7|9][0-9]{8}$/", $phone)){
                $error = 'Please insert valid spanish phone number';
                return false;
                
            }
        }
        return true;
    }
}