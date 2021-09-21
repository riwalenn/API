<?php

namespace App\Controller;

use App\Entity\Users;

class UserStateController
{
    public function __invoke(Users $data): Users
    {
        return $data->setState(true);
    }
}