<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model\Repository;

use PDO;
use SallePW\SlimApp\Model\UserGame;
use SallePW\SlimApp\Model\UserGameRepository;

final class MySQLUserGameRepository implements UserGameRepository
{

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(int $u_id, int $game_id): void
    {
        $query = <<<'QUERY'
        INSERT INTO userGame(u_id, game_id)
        VALUES(:u_id, :game_id)
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('u_id', $u_id, PDO::PARAM_INT);
        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);
        $statement->execute();

        $row =  $statement->fetch(PDO::FETCH_OBJ);  
    }

    public function ownsGame(int $u_id, int $game_id): bool
    {

        $query = <<<'QUERY'
            SELECT * FROM userGame WHERE u_id = :u_id AND game_id = :game_id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('u_id', $u_id, PDO::PARAM_INT);
        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);

        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);  
        if($row){

            return true;

        }else{

            return false;

        }

    }

    public function getGames(int $u_id)
    {

        $query = <<<'QUERY'
        SELECT * FROM userGame WHERE u_id = :u_id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('u_id', $u_id, PDO::PARAM_INT);

        $statement->execute();
        $row =  $statement->fetchAll(PDO::FETCH_OBJ);
        $result = array();
        for($j = 0; $j < count($row); $j++){

            array_push($result, new UserGame(intval($row[$j]->u_id), intval($row[$j]->game_id)));

        }
        
        return $result;

    }

    public function addWishlist(int $u_id, int $game_id): void
    {
        $query = <<<'QUERY'
        INSERT INTO wishlist(u_id, game_id)
        VALUES(:u_id, :game_id)
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('u_id', $u_id, PDO::PARAM_INT);
        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);
        $statement->execute();

        $row =  $statement->fetch(PDO::FETCH_OBJ);  
    }

    public function inWishlist(int $u_id, int $game_id): bool
    {

        $query = <<<'QUERY'
            SELECT * FROM wishlist WHERE u_id = :u_id AND game_id = :game_id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('u_id', $u_id, PDO::PARAM_INT);
        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);

        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);  
        if($row){
            return true;
        }else{

            return false;
        }
    }

    public function deleteGameWishlist(int $u_id, int $game_id):void
    {

        $query = <<<'QUERY'
            DELETE FROM wishlist WHERE u_id = :u_id AND game_id = :game_id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('u_id', $u_id, PDO::PARAM_INT);
        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);

        $statement->execute();
    }

    public function getWishlist(int $u_id)
    {
        $query = <<<'QUERY'
        SELECT * FROM wishlist WHERE u_id = :u_id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('u_id', $u_id, PDO::PARAM_INT);

        $statement->execute();
        $row =  $statement->fetchAll(PDO::FETCH_OBJ);
        $result = array();
        for($j = 0; $j < count($row); $j++){

            array_push($result, new UserGame(intval($row[$j]->u_id), intval($row[$j]->game_id)));

        }
        
        return $result;
    }

    public function getWishlistGame(int $u_id, int $game_id)
    {

        $query = <<<'QUERY'
            SELECT * FROM wishlist WHERE u_id = :u_id AND game_id = :game_id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('u_id', $u_id, PDO::PARAM_INT);
        $statement->bindParam('game_id', $game_id, PDO::PARAM_INT);

        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);  
        return $row;
    }

}