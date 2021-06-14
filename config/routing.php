<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

//Controllers
use SallePW\SlimApp\Controller\HomeController;
use SallePW\SlimApp\Controller\VisitsController;
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
//Middlewares
use SallePW\SlimApp\Middleware\BeforeMiddleware;
//use SallePW\SlimApp\Middleware\StartSessionMiddleware;




$app->get('/', HomeController::class . ':apply')->setName('home');

$app->post('/', HomeController::class . ':goRegister')->setName('goRegister');


$app->get('/visits',VisitsController::class . ":showVisits")->setName('visits');

$app->get('/cookies',VisitsController::class . ":showAdvice")->setName('cookies');


$app->get('/register',RegisterController::class . ":showRegisterForm")->setName('register');

$app->post('/register',RegisterController::class . ":apply")->setName('create-user');


$app->get('/login',LoginController::class . ":showLoginForm")->setName('login');

$app->post('/login',LoginController::class . ":apply")->setName('search-user');

$app->post('/logout',LoginController::class . ":logout")->setName('logout');

$app->get('/store',StoreController::class . ":showStore")->setName('store');

$app->post('/store/buy/{gameId}',StoreController::class . ":buyGame")->setName('buy');

$app->get('/user/myGames',MyGamesController::class . ":showMyGames")->setName('myGames');

$app->get('/activate',ActivateController::class . ":showActivate")->setName('activate');


$app->get('/profile',ProfileController::class . ":showProfile")->setName('profile');

$app->post('/profile',ProfileController::class . ":updateProfile")->setName('upload');

$app->get('/profile/changePassword',ProfileController::class . ":showPasswordForm")->setName('password');

$app->post('/profile/changePassword',ProfileController::class . ":changePassword")->setName('change');


$app->get('/user/wallet',WalletController::class . ":showWallet")->setName('wallet');

$app->post('/user/wallet',WalletController::class . ":updateWallet")->setName('updateWallet');

//Wishlist

$app->get('/user/wishlist',WishlistController::class . ":showWishlist")->setName('wishlist');

$app->get('/user/wishlist/{gameId}',WishlistController::class . ":showWishlistGame")->setName('showGame');

$app->post('/user/wishlist/{gameId}',WishlistController::class . ":addGame")->setName('addGame');

$app->post('/user/wishlist/delete/{gameId}',WishlistController::class . ":deleteGame")->setName('deleteGame');

//Friends

$app->get('/user/friends',FriendsController::class . ":showFriends")->setName('friends');

$app->get('/user/friendRequests',FriendRequestsController::class . ":showFriendRequests")->setName('friendRequests');

$app->get('/user/friendRequests/send',FriendRequestsController::class . ":showRequestForm")->setName('showForm');

$app->post('/user/friendRequests/send',FriendRequestsController::class . ":sendFriendRequest")->setName('sendForm');

$app->post('/user/friendRequests/accept/{requestId}',FriendRequestsController::class . ":acceptFriendRequest")->setName('accept');

$app->post('/user/friendRequests/decline/{requestId}',FriendRequestsController::class . ":declineFriendRequest")->setName('decline');


