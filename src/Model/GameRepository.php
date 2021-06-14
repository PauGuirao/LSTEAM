<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

interface GameRepository
{

    /**
     * Summary: Add a game to the database.
     * @params: Game: game
     */
    public function Save(int $game_id, String $title, float $price, String $thumbnail): void;
    /**
     * Summary: Get a game from the database via its ID.
     * @params: int: game_id
     */
    public function getGame(int $game_id);

}