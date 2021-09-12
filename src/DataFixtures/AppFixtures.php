<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;
    private $manager;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->loadUsers();
        $this->loadAdminRole();
        $this->loadUserRole();

        $manager->flush();
    }

    public function loadUsers()
    {
        $faker = Factory::create('fr_FR');
        for ($i = 1; $i < 5; $i++) {
            $user = new Users();
            $username = $faker->userName;
            $user->setUsername($username)
                ->setEmail($faker->email)
                ->setPassword($this->passwordEncoder->encodePassword($user, $username))
                ->setRoles(['ROLE_USER'])
                ->setState($faker->boolean)
                ->setCreatedAt(new \DateTime())
                ->setModifiedAt(new \DateTime());
            $this->manager->persist($user);
        }
    }

    public function loadAdminRole()
    {
        $user = new Users();
        $user->setUsername('admin')
                ->setEmail('admin@gmail.com')
                ->setPassword($this->passwordEncoder->encodePassword($user, "admin"))
                ->setRoles(['ROLE_ADMIN'])
                ->setState(1)
                ->setCreatedAt(new \DateTime())
                ->setModifiedAt(new \DateTime());
        $this->manager->persist($user);
    }

    public function loadUserRole()
    {
        $user = new Users();
        $user->setUsername('user')
            ->setEmail('user@gmail.com')
            ->setPassword($this->passwordEncoder->encodePassword($user, "user"))
            ->setRoles(['ROLE_USER'])
            ->setState(1)
            ->setCreatedAt(new \DateTime())
            ->setModifiedAt(new \DateTime());
        $this->manager->persist($user);
    }
}
