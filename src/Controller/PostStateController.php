<?php

namespace App\Controller;

use App\Entity\Posts;

class PostStateController
{

    public function __invoke(Posts $data): Posts
    {
        return $data->setState(true);
    }
}