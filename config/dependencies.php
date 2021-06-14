<?php
declare(strict_types=1);

use DI\Container;
use Slim\Views\Twig;
use Slim\Flash\Messages;

//Afegir els controladors
use SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\VisitsController;
use SallePW\SlimApp\Controller\CookieMonsterController;
use SallePW\SlimApp\Controller\RegisterController;
use SallePW\SlimApp\Controller\LoginController;
use SallePW\SlimApp\Controller\StoreController;
use SallePW\SlimApp\Controller\ActivateController;
use SallePW\SlimApp\Controller\ProfileController;
use SallePW\SlimApp\Controller\MyGamesController;
use SallePW\SlimApp\Controller\WalletController;
use SallePW\SlimApp\Controller\WishlistController;
use SallePW\SlimApp\Controller\FriendsController;
use SallePW\SlimApp\Controller\FriendRequestsController;
use SallePW\SlimApp\Controller\FlashController;

use SallePW\SlimApp\Middleware\SendMailMiddleware;

use SallePW\SlimApp\Model\Repository\PDOSingleton;
use SallePW\SlimApp\Model\Repository\MySQLUserRepository;
use SallePW\SlimApp\Model\Repository\MySQLGameRepository;
use SallePW\SlimApp\Model\Repository\MySQLUserGameRepository;

use SallePW\SlimApp\Model\Repository\DecoratorGameRepository;

use SallePW\SlimApp\Model\UserRepository;
use SallePW\SlimApp\Model\GameRepository;
use SallePW\SlimApp\Model\UserGameRepository;
use Psr\Container\ContainerInterface;

$container = new Container();

$container->set(
    'view',
    function () {
        return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
    }
);

$container->set(
    'flash',
    function () {
        return new Messages();
    }
);

$container->set(
    FlashController::class,
    function (Container $c) {
        $controller = new FlashController($c->get("view"), $c->get("flash"));
        return $controller;
    }
);


$container->set(
    VisitsController::class,
    function (ContainerInterface $c) {
        $controller = new VisitsController($c->get("view"));
        return $controller;
    }
);

$container->set(
    CookieMonsterController::class,
    function (ContainerInterface $c) {
        $controller = new CookieMonsterController($c->get("view"));
        return $controller;
    }
);

$container->set('db', function () {
    return PDOSingleton::getInstance(
        $_ENV['MYSQL_ROOT_USER'],
        $_ENV['MYSQL_ROOT_PASSWORD'],
        $_ENV['MYSQL_HOST'],
        $_ENV['MYSQL_PORT'],
        $_ENV['MYSQL_DATABASE']
    );
});

$container->set(UserRepository::class, function (ContainerInterface $container) {
    return new MySQLUserRepository($container->get('db'));
});

$container->set(GameRepository::class, function (ContainerInterface $container) {
    return new MySQLGameRepository($container->get('db'));
});

$container->set(UserGameRepository::class, function (ContainerInterface $container) {
    return new MySQLUserGameRepository($container->get('db'));
});

$container->set(
    HomeController::class,
    function (ContainerInterface $c) {
        $controller = new HomeController($c->get("view"),$c->get(UserRepository::class));
        return $controller;
    }
);

$container->set(
    RegisterController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new RegisterController($c->get("view"), $c->get(UserRepository::class), $c->get(SendMailMiddleware::class)); // Not SURE
        return $controller;
    }
);

$container->set(
    LoginController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new LoginController($c->get("view"), $c->get(UserRepository::class)); // Not SURE
        return $controller;
    }
);

$container->set(
    StoreController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new StoreController($c->get("view"), $c->get(UserRepository::class), $c->get(GameRepository::class), $c->get(UserGameRepository::class), $c->get(DecoratorGameRepository::class)); // Not SURE
        return $controller;
    }
);

$container->set(
    MyGamesController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new MyGamesController($c->get("view"), $c->get(UserRepository::class), $c->get(GameRepository::class), $c->get(UserGameRepository::class)); // Not SURE
        return $controller;
    }
);

$container->set(
    ActivateController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new ActivateController($c->get("view"), $c->get(UserRepository::class), $c->get(SendMailMiddleware::class)); // Not SURE
        return $controller;
    }
);

$container->set(
    ProfileController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new ProfileController($c->get("view"), $c->get(UserRepository::class)); // Not SURE
        return $controller;
    }
);

$container->set(
    WalletController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new WalletController($c->get("view"), $c->get(UserRepository::class)); // Not SURE
        return $controller;
    }
);

$container->set(
    WishlistController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new WishlistController($c->get("view"), $c->get(UserRepository::class), $c->get(GameRepository::class), $c->get(UserGameRepository::class)); // Not SURE
        return $controller;
    }
);

$container->set(
    FriendsController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new FriendsController($c->get("view"), $c->get(UserRepository::class)); // Not SURE
        return $controller;
    }
);

$container->set(
    FriendRequestsController::class,
    function (ContainerInterface $c) {
        //LAST LINE OF EXECUTION
        $controller = new FriendRequestsController($c->get("view"), $c->get(UserRepository::class)); // Not SURE
        return $controller;
    }
);
