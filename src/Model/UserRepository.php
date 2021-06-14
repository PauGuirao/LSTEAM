<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

interface UserRepository
{
    /**
     * Summary: Saves user into the database
     * @params: User
     */
    public function save(User $user);

    public function addProfileImage(string $username);

    public function searchProfileImage(int $user_id);

    /**
     * Summary: Searches user into the database with a certain mail
     * @params: string: mail
     */
    public function searchUserMail(string $user);

    /**
     * Summary: Searches user into the database with a certain username
     * @params: string: username
     */
    public function searchUsername(string $user);

    /**
     * Summary: Searches into the database if the token its been already used
     * @params: string: token
     */

    public function searchId(int $id);

    /**
     * Summary: Searches into the database if the token its been already used
     * @params: string: token
     */ 
    public function checkToken(string $token);

    /**
     * Summary: Updates the user status
     * @params: string: token
     */
    public function updateStatus(string $token);

    /**
     * Summary: Searches user into the database
     * @params: string: mail
     */
    public function searchUser(string $mail,string $password);

    /**
     * Summary: Searches into the database if the token its been already used
     * @params: string: token
     */ 

    public function searchUserPassword(int $id, string $oldPass);

    public function updatePassword(int $id, string $newPass);

    public function updateUserImage(int $id,string $uuid);

    public function updateUserPhone(int $id,string $phone);

    public function addMoney(int $id,float $money);

    public function checkMoney(int $id);

    public function getUserImage(int $id);

    public function getFriends(string $username);

    public function searchFriend(string $myUsername,string $friendsUsername):bool;

    public function searchFriendRequest(string $myUsername,string $friendsUsername):bool;

    public function searchDeclinedRequest(string $myUsername,string $friendsUsername):bool;

    public function addFriendRequest(string $myUsername,string $friendsUsername);

    public function getFriendRequests(string $username);

    public function addFriend(string $myUsername,string $friendsUsername,string $accept_date);

    public function declineFriend(string $myUsername,string $friendsUsername);

    public function getFriendRequest(int $request_id);

}