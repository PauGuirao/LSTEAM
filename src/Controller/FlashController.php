<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Controller;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Flash\Messages;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

final class FlashController
{

    public function __construct(
        private Twig $twig,
        private Messages $flash)
    {
    }

    public function createFlashMessage(string $message)
    {
        $_SESSION['notification'] = $message;
    }
}