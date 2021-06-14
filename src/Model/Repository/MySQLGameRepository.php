<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model\Repository;

use PDO;
use SallePW\SlimApp\Model\Game;
use SallePW\SlimApp\Model\GameRepository;

final class MySQLGameRepository implements GameRepository
{

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function Save(int $game_id, String $title, float $price, String $thumbnail): void
    {
        $query = <<<'QUERY'
        INSERT INTO game(game_id, title, price, thumbnail)
        VALUES(:game_id, :title, :price, :thumbnail)
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);
        $statement->bindParam('title', $title, PDO::PARAM_STR);
        $statement->bindParam('price', $price, PDO::PARAM_STR);
        $statement->bindParam('thumbnail', $thumbnail, PDO::PARAM_STR);
        $statement->execute();
    }

    public function getGame(int $game_id)
    {

        $query = <<<'QUERY'
            SELECT * FROM game WHERE game_id = :game_id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('game_id', $game_id, PDO::PARAM_STR);

        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);
        if($row){
            $result = new Game(intval($row->game_id), $row->title, floatval($row->price), $row->thumbnail);
            return $result;    
        }else{
            return false;
        }

    }

}