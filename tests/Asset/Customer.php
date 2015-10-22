<?php
namespace Kanti\Test\Asset;

class Customer implements \JsonSerializable
{

    private $name;
    private $email;

    public function __construct($name, $email)
    {
        $this->name = $name;
        $this->email = $email;
    }

    public function jsonSerialize()
    {
        return [
            'customer' => [
                'name' => $this->name,
                'email' => $this->email
            ]
        ];
    }
}
