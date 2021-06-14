<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model\Repository;

use PDO;
use SallePW\SlimApp\Model\Game;
use SallePW\SlimApp\Model\GameRepository;

final class DecoratorGameRepository implements GameRepository
{

    private array $games;

    private GameRepository $repository;

    public function __construct()
    {
        $this->games = array();
    }

    public function Save(int $game_id, String $title, float $price, String $thumbnail): void
    {



    }

    public function writeIntoCache(array $games): void
    {

        $this->games = $games;

        $cacheString = "";
        
        for($i = 0; $i < count($this->games); $i++){

            $cacheString .= $this->games[$i]->gameID;
            $cacheString .= PHP_EOL;
            $cacheString .= $this->games[$i]->title;
            $cacheString .= PHP_EOL;
            $cacheString .= $this->games[$i]->normalPrice;
            $cacheString .= PHP_EOL;
            $cacheString .= $this->games[$i]->thumb;
            $cacheString .= PHP_EOL;

        }

        $_SESSION['cache'] = $cacheString;

    }

    public function loadFromCache(): void
    {

        $cacheString = $_SESSION['cache'];
        $cacheArray = explode(PHP_EOL, $cacheString);
        
        for($j = 0; $j < count($cacheArray) - 1; $j += 4){
            
            $g = array("gameID" => $cacheArray[$j], "title" => $cacheArray[$j + 1], "normalPrice" => $cacheArray[$j + 2], "thumb" => $cacheArray[$j + 3]);
            array_push($this->games, $g);

        }

    }

    public function cacheExists(): bool
    {

        return !empty($_SESSION['cache']);

    }

    public function getGame(int $game_id): array
    {

        return $this->games;

    }

}