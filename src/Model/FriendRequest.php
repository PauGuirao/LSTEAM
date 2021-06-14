<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

final class FriendRequest
{

    private int $request_id;
    private string $user_sender;
    private string $user_receiver;
    private int $declined;

    public function __construct(
        int $request_id,
        string $user_sender,
        string $user_receiver,
        int $declined
        
    ){

        $this->request_id = $request_id;
        $this->user_sender = $user_sender;
        $this->user_receiver = $user_receiver;
        $this->declined = $declined;

    }

    public function request_id(): int
    {

        return $this->request_id;

    }

    public function user_sender(): string
    {

        return $this->user_sender;

    }

    public function user_receiver(): string
    {

        return $this->user_receiver;

    }

    public function declined(): int
    {

        return $this->declined;

    }

}