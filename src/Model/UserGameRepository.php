<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

interface UserGameRepository
{

    /**
     * Summary: Add a user-game relation to the database.
     * @params: UserGame: userGame
     */
    public function Save(int $u_id, int $game_id): void;

    /**
     * Summary: Checks if there's a certain game in the database.
     * @params: int:u_id, int:game_id
     */

    public function ownsGame(int $u_id, int $game_id): bool;
    
    /**
     * Summary: Return all games taget user owns.
     * @params: int:u_id
     */

    public function getGames(int $u_id);

    /**
     * Summary: Add games to wishlist
     * @params: int:u_id
     */


    public function addWishlist(int $u_id, int $game_id): void;

    public function inWishlist(int $u_id, int $game_id): bool;

    public function getWishlist(int $u_id);

    public function deleteGameWishlist(int $u_id, int $game_id):void;

    public function getWishlistGame(int $u_id, int $game_id);

}