<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

final class UserGame
{

    private int $u_id;
    private int $game_id;

    public function __construct(
        int $u_id,
        int $game_id
    ){

        $this->u_id = $u_id;
        $this->game_id = $game_id;

    }

    public function u_id(): int
    {
        return $this->u_id;
    }

    public function game_id(): int
    {
        return $this->game_id;
    }

}