<?php
declare(strict_types=1);

namespace SallePW\SlimApp\Model;

use DateTime;

final class User
{
    private int $id;
    private string $username;
    private string $email;
    private string $password;
    private string $birthdate;
    private string $phone;
    private string $token;

    public function __construct(
        string $username,
        string $email,
        string $password,
        string $birthdate,
        string $phone,
        string $token
    ) {
        $this->username = $username;
        $this->email = $email;
        $this->password = $password;
        $this->birthdate = $birthdate;
        $this->phone = $phone;
        $this->token = $token;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function birthdate(): string
    {
        return $this->birthdate;
    }
    public function phone(): string
    {
        return $this->phone;
    }
    public function token(): string
    {
        return $this->token;
    }

}