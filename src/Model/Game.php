<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

final class Game
{

    private int $game_id;
    private string $title;
    private float $price;
    private string $thumbnail;

    public function __construct(
        int $game_id,
        string $title,
        float $price,
        string $thumbnail
    ){

        $this->game_id = $game_id;
        $this->title = $title;
        $this->price = $price;
        $this->thumbnail = $thumbnail;

    }

    public function game_id(): int
    {
        return $this->game_id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function thumbnail(): string
    {
        return $this->thumbnail;
    }

}