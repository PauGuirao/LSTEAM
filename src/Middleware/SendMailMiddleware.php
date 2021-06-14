<?php

namespace SallePW\SlimApp\Middleware;

use Slim\Views\Twig;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

final class SendMailMiddleware
{

    public function __construct(){



    }

    public function sendValidationMail(string $email, string $token, Twig $twig, array $errors){

        $mail = new PHPMailer(true);
        $receiver = $email;
        try {
            $mail->isSMTP();
            $mail->Host = 'mail.smtpbucket.com';
            $mail->Port = 8025;

            $mail->setFrom('noreply@lsteam.com', 'Mailer');
            $mail->addAddress($receiver, 'Receiver');

            $mail->isHTML(true);
            $mail->Subject = 'Confirmation mail.';
            $mail->Body    = "<h1>Welcome to LSteam!</h1> Here's your url: <a href = 'http://localhost:8030/activate?token=".$token."'>ACTIVATION CODE</a>";
            $mail->AltBody = "Welcome to LSteam! Here's your url: http://localhost:8030/activate?token=".$token;

            $mail->send();
            $errors['emailSent'] = 'Congratulations! Please check your email and verify your account to before log in';
            return $errors;
        } catch (MailerException $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                return false;
        }

    }

    public function sendActivationMail(string $username, string $email){

        $mail = new PHPMailer(true);
        $receiver = $email;
        try {
            $mail->isSMTP();
            $mail->Host = 'mail.smtpbucket.com';
            $mail->Port = 8025;

            $mail->setFrom('noreply@lsteam.com', 'Mailer');
            $mail->addAddress($receiver, 'Receiver');

            $mail->isHTML(true);
            $mail->Subject = 'Welcome gift!';
            $mail->Body    = "<h1>Thanks for registering into LSteam, ".$username."!</h1> <p>To celebrate your arrival, here you have 50$ to spend in our shop!</p> <a href='http://localhost:8030/login'>LOGIN</a>";
            $mail->AltBody = "Thanks for registering into LSteam, we added 50$ to your wallet! Login with the following link: http://localhost:8030/login";

            $mail->send();
        } catch (MailerException $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    }

}