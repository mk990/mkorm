<?php

namespace MkOrm\Test\TestClass;

use MkOrm\Resource\Resource;

class UsersResource extends Resource
{
    public $data;
    public $links;
    public $meta;

    public function __construct($results)
    {
        if (!empty($results['data'])) {
            $this->links($results['info']);
            $this->meta($results['info']);
            $results = $results['data'];
        }

        foreach ($results as $result) {
            $object = new UserResource($result);
            $this->data[] = $object->data;
        }
    }
}
