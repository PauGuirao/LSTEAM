<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model\Repository;

use PDO;
use SallePW\SlimApp\Model\User;
use SallePW\SlimApp\Model\FriendRequest;
use SallePW\SlimApp\Model\UserRepository;

final class MysqlUserRepository implements UserRepository
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private PDOSingleton $database;

    public function __construct(PDOSingleton $database)
    {
        $this->database = $database;
    }

    public function save(User $user)
    {
        $query = <<<'QUERY'
        INSERT INTO user(username,email, password, birthdate,phone,money,token)
        VALUES(:username, :email, :password, :birthdate, :phone, :money, :token)
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);

        $username = $user->username();
        $email = $user->email();
        $password = $user->password();
        $birthdate = $user->birthdate();
        $phone = $user->phone();
        $token = $user->token();
        $money = 0;

        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('email', $email, PDO::PARAM_STR);
        $statement->bindParam('password', $password, PDO::PARAM_STR);
        $statement->bindParam('birthdate', $birthdate, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);
        $statement->bindParam('token', $token, PDO::PARAM_STR);
        $statement->bindParam('money', $money, PDO::PARAM_STR);

        $statement->execute();
    }

    public function addProfileImage(string $username){
        $query = <<<'QUERY'
        SELECT * FROM user WHERE username = :username
        QUERY;
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);

        if($row){
            $query = <<<'QUERY'
            INSERT INTO profileimg(user_id)
            VALUES(:id)
            QUERY;
            $statement = $this->database->connection()->prepare($query);
            $statement->bindParam('id', $row->id, PDO::PARAM_STR);
            $statement->execute();
        }
        
    }

    public function searchProfileImage(int $user_id){
        $query = <<<'QUERY'
        SELECT * FROM profileimg WHERE user_id = :user_id
        QUERY;
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('user_id', $user_id, PDO::PARAM_STR);
        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);

        return $row;
        
    }

    public function searchUserMail(string $email){
        $query = <<<'QUERY'
            SELECT * FROM user WHERE email = :email
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);  
        return $row;
    }
    
    public function searchUsername(string $username){
        $query = <<<'QUERY'
            SELECT * FROM user WHERE username = :username
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('username', $username, PDO::PARAM_STR);

        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);  
        return $row;
    }

    public function searchId(int $id){
        $query = <<<'QUERY'
            SELECT * FROM user WHERE id = :id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);  
        return $row;
    }

    public function checkToken(string $token){
        $status = 'Inactive';
        $query = <<<'QUERY'
            SELECT * FROM user WHERE token = :token AND status = :status
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('token', $token, PDO::PARAM_STR);
        $statement->bindParam('status', $status, PDO::PARAM_STR);

        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);
        return $row;
    }
    public function updateStatus(string $token){
        $status = 'Active';
        $query = <<<'QUERY'
            UPDATE user SET status = :status WHERE token = :token
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('token', $token, PDO::PARAM_STR);
        $statement->bindParam('status', $status, PDO::PARAM_STR);

        $statement->execute();
    }

    public function searchUser(string $email,string $password){
        $query = <<<'QUERY'
            SELECT * FROM user WHERE (email = :email OR username = :email)
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('email', $email, PDO::PARAM_STR);

        $statement->execute();

        $row =  $statement->fetch(PDO::FETCH_OBJ);  

        if($row){
            $hashedPassword = $row->password;
            if(password_verify($password,$hashedPassword)){
                return $row;
            }else{
                return false;
            }

        }else{
            return false;
        }
    }
    public function searchUserPassword(int $id, string $oldPass){
        $query = <<<'QUERY'
            SELECT * FROM user WHERE id = :id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $row =  $statement->fetch(PDO::FETCH_OBJ);  

        if($row){
            $hashedPassword = $row->password;
            if(password_verify($oldPass,$hashedPassword)){
                return $row;
            }else{
                return false;
            }

        }else{
            return false;
        }
    }

    public function updatePassword(int $id, string $newPass){
        $query = <<<'QUERY'
            UPDATE user SET password = :newPass WHERE id = :id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('newPass', $newPass, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateUserImage(int $id,string $uuid){
        $query = <<<'QUERY'
            UPDATE profileimg SET uuid = :uuid WHERE user_id = :id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('uuid', $uuid, PDO::PARAM_STR);

        $statement->execute();
    }

    public function updateUserPhone(int $id,string $phone){
        $query = <<<'QUERY'
            UPDATE user SET phone = :phone WHERE id = :id
        QUERY;
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('id', $id, PDO::PARAM_STR);
        $statement->bindParam('phone', $phone, PDO::PARAM_STR);
        $statement->execute();
    }

    public function getUserImage(int $id){
        $query = <<<'QUERY'
            SELECT * FROM profileimg WHERE user_id = :id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();
        $row =  $statement->fetch(PDO::FETCH_OBJ);  
        return $row;
    }

    public function addMoney(int $id,float $money){

        $query = <<<'QUERY'
            SELECT * FROM user WHERE id = :id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $row =  $statement->fetch(PDO::FETCH_OBJ);  
        
        if($row){
            $money += $row->money;
            $query = <<<'QUERY'
            UPDATE user SET money = :money WHERE id = :id
            QUERY;

            $statement = $this->database->connection()->prepare($query);
            $statement->bindParam('id', $id, PDO::PARAM_STR);
            $statement->bindParam('money', $money, PDO::PARAM_STR);
            $statement->execute();

        }else{
            return false;
        }
        
    }

    public function checkMoney(int $id){

        $query = <<<'QUERY'
            SELECT * FROM user WHERE id = :id
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('id', $id, PDO::PARAM_STR);

        $statement->execute();

        $row =  $statement->fetch(PDO::FETCH_OBJ);

        return $row->money;

    }

    public function getFriends(string $username){
        $query = <<<'QUERY'
            SELECT * FROM friend WHERE username_1 = :username OR username_2 = :username 
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('username', $username, PDO::PARAM_STR);

        $statement->execute();
        $row =  $statement->fetchAll(PDO::FETCH_OBJ);

        $friends = array();
        if($row){
            for($j = 0; $j < count($row); $j++){
                $friend = [];
                if($row[$j]->username_1 != $username){
                    $friend['name'] = $row[$j]->username_1;
                }else{
                    $friend['name'] = $row[$j]->username_2;
                }
                $friend['date'] = $row[$j]->accept_date;
                array_push($friends, $friend);
            } 
        }

        return $friends;
    }


    public function searchFriend(string $myUsername, string $friendUsername):bool {
        $query = <<<'QUERY'
            SELECT * FROM friend WHERE username_1 = :myUsername OR username_2 = :myUsername 
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('myUsername', $myUsername, PDO::PARAM_STR);
        $statement->execute();
        $row =  $statement->fetchAll(PDO::FETCH_OBJ);
        
        if($row){
            for($j = 0; $j < count($row); $j++){
                if($row[$j]->username_1 == $myUsername){
                    if($row[$j]->username_2 == $friendUsername){
                        return true;
                    }
                }else{
                    if($row[$j]->username_2 == $myUsername){
                        if($row[$j]->username_1 == $friendUsername){
                            return true;
                        }
                    }
                }
            } 
        }

        return false;
    }


    public function getFriendRequests(string $username){
        $state = 'Pending';
        $query = <<<'QUERY'
        SELECT * FROM friendRequest WHERE user_receiver = :username AND state = :state
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('username', $username, PDO::PARAM_STR);
        $statement->bindParam('state', $state, PDO::PARAM_STR);


        $statement->execute();
        $row =  $statement->fetchAll(PDO::FETCH_OBJ);

        $friendRequests = array();
        if($row){
            for($j = 0; $j < count($row); $j++){
                    $friendRequest = new FriendRequest(intval($row[$j]->request_id), $row[$j]->user_sender, $row[$j]->user_receiver, intval($row[$j]->state));
                    array_push($friendRequests, $friendRequest);
            } 
        }

        return $friendRequests;
    }

    public function searchFriendRequest(string $myUsername,string $friendsUsername):bool{
        $query = <<<'QUERY'
            SELECT * FROM friendRequest WHERE user_sender = :myUsername AND user_receiver = :friendsUsername 
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('myUsername', $myUsername, PDO::PARAM_STR);
        $statement->bindParam('friendsUsername', $friendsUsername, PDO::PARAM_STR);
        $statement->execute();
        $row =  $statement->fetchAll(PDO::FETCH_OBJ);
        
        if($row){
            return true;
        }
        return false;
    }

    public function searchDeclinedRequest(string $myUsername,string $friendsUsername):bool{
        $state = 'Declined';
        $query = <<<'QUERY'
            SELECT * FROM friendRequest WHERE user_sender = :myUsername AND user_receiver = :friendsUsername AND state = :state
        QUERY;
        $statement = $this->database->connection()->prepare($query);

        $statement->bindParam('myUsername', $myUsername, PDO::PARAM_STR);
        $statement->bindParam('friendsUsername', $friendsUsername, PDO::PARAM_STR);
        $statement->bindParam('state', $state, PDO::PARAM_STR);
        $statement->execute();
        $row =  $statement->fetchAll(PDO::FETCH_OBJ);
        
        if($row){
            return true;
        }
        return false;
    }

    public function addFriendRequest(string $myUsername,string $friendsUsername){
        $state = 'Pending';
        $query = <<<'QUERY'
        INSERT INTO friendRequest(user_sender,user_receiver,state)
        VALUES(:myUsername, :friendsUsername, :state)
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('myUsername', $myUsername, PDO::PARAM_STR);
        $statement->bindParam('friendsUsername', $friendsUsername, PDO::PARAM_STR);
        $statement->bindParam('state', $state, PDO::PARAM_STR);

        $statement->execute();
    }

    public function addFriend(string $myUsername,string $friendsUsername,string $accept_date){
        //We need to update the friendRequest
        $state = 'Accepted';
        $query = <<<'QUERY'
        UPDATE friendRequest SET state = :state WHERE user_receiver = :myUsername AND user_sender = :friendsUsername 
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('myUsername', $myUsername, PDO::PARAM_STR);
        $statement->bindParam('friendsUsername', $friendsUsername, PDO::PARAM_STR);
        $statement->bindParam('state', $state, PDO::PARAM_STR);

        $statement->execute();

        //Now we need to add the friend to the new friend to the friend list
        $query = <<<'QUERY'
        INSERT INTO friend(username_1,username_2,accept_date) VALUES(:myUsername,:friendsUsername,:accept_date)
        QUERY;
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('myUsername', $myUsername, PDO::PARAM_STR);
        $statement->bindParam('friendsUsername', $friendsUsername, PDO::PARAM_STR);
        $statement->bindParam('accept_date', $accept_date, PDO::PARAM_STR);

        $statement->execute();
    }

    public function declineFriend(string $myUsername,string $friendsUsername){
        //We need to update the friendRequest
        $state = 'Declined';
        $query = <<<'QUERY'
        UPDATE friendRequest SET state = :state WHERE user_receiver = :myUsername AND user_sender = :friendsUsername 
        QUERY;
        
        $statement = $this->database->connection()->prepare($query);
        $statement->bindParam('myUsername', $myUsername, PDO::PARAM_STR);
        $statement->bindParam('friendsUsername', $friendsUsername, PDO::PARAM_STR);
        $statement->bindParam('state', $state, PDO::PARAM_STR);

        $statement->execute();
    }

    public function getFriendRequest(int $request_id){
        $state = 'Pending';
        $query = <<<'QUERY'
        SELECT * FROM friendRequest WHERE request_id = :request_id AND state = 'Pending'
        QUERY;
        $statement = $this->database->connection()->prepare($query);
    
        $statement->bindParam('request_id', $request_id, PDO::PARAM_INT);
    
    
        $statement->execute();
        $row =  $statement->fetchAll(PDO::FETCH_OBJ);

        if($row){

            $friendRequest = new FriendRequest(intval($row[0]->request_id), $row[0]->user_sender, $row[0]->user_receiver, intval($row[0]->state));
    
            return $friendRequest;

        }else{

            return false;

        }
    }

}