<?php

namespace App\Tests;

use App\Entity\Users;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait NeedLogin
{
    Public function login(KernelBrowser $client, Users $users)
    {
        $session = $client->getContainer()->get('session');

        $token = new UsernamePasswordToken($users, null, 'main', $users->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $client->getCookieJar()->set($cookie);
    }
}