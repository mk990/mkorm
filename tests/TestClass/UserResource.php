<?php

namespace MkOrm\Test\TestClass;

use MkOrm\Resource\Resource;

class UserResource extends Resource
{
    public $data;

    public function __construct(Users $user)
    {
        $name = $this->getName();
        $link = "/$name/" . $user->getId();
        $this->data = [
            "type"          => "$name",
            "id"            => $user->getId(),
            "attributes"    => [
                'name'       => $user->getName(),
                'pass'       => $user->getPassword(),
                "created_at" => $user->getCreatedAt(),
            ],
            "relationships" => [],
            "links"         => [
                "self" => $link,
            ]
        ];
    }
}
