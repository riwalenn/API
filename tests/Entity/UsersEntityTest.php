<?php

namespace App\Tests\Entity;

use App\Entity\Users;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Validator\ConstraintViolation;

class UsersEntityTest extends KernelTestCase
{
    private $entityManager;

    /**
     * @throws Exception
     */
    protected function setCommand($string): int
    {
        $kernel = static::createKernel(['APP_ENV' => 'test']);
        $application = new Application($kernel);
        $application->setAutoExit(false);
        return $application->run(new StringInput(sprintf('%s --quiet', $string)));
    }

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        $this->setCommand('doctrine:database:drop --force');
        $this->setCommand('doctrine:database:create');
        $this->setCommand('doctrine:schema:create');
        $this->setCommand('doctrine:fixtures:load');
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    protected function assertHasErrors(Users $user, int $number = 0)
    {
        $kernel = self::bootKernel();
        $errors = $kernel->getContainer()->get('validator')->validate($user);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    protected function getEntity(): Users
    {
        return (new Users())
            ->setUsername('test')
            ->setEmail('test@gmail.com')
            ->setPassword('FM<gbO!SI)FD?ASy5"')
            ->setRoles(array(Users::ROLE_USER))
            ->setState(0)
            ->setCreatedAt(new \DateTime('now'))
            ->setModifiedAt(new \DateTime('now'));
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInstanceOfEntity()
    {
        $this->assertInstanceOf(Users::class, $this->getEntity());
    }

    public function testHasAttributesEntity()
    {
        $this->assertObjectHasAttribute('username', $this->getEntity());
        $this->assertObjectHasAttribute('email', $this->getEntity());
        $this->assertObjectHasAttribute('password', $this->getEntity());
        $this->assertObjectHasAttribute('roles', $this->getEntity());
        $this->assertObjectHasAttribute('state', $this->getEntity());
        $this->assertObjectHasAttribute('created_at', $this->getEntity());
        $this->assertObjectHasAttribute('modified_at', $this->getEntity());
    }

    public function testInvalidBlankUsername()
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 1);
    }

    public function testInvalidBlankEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail(''), 1);
    }

    public function testInvalidEmail()
    {
        $this->assertHasErrors($this->getEntity()->setEmail('@gmail.com'), 1);
    }

    public function testInvalidBlankPassword()
    {
        $this->assertHasErrors($this->getEntity()->setPassword(''), 1);
    }

    public function testTypeArrayRoles()
    {
        $this->assertIsArray($this->getEntity()->getRoles());
    }

    public function testGetId()
    {
        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['username' => 'user']);
        $this->assertEquals($user->getId(), 6);
    }

    public function testUniqueUsername()
    {
        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['username' => 'user']);
        $this->assertFalse($user->getUsername() !== "user", "Cet username existe déjà.");
    }

    public function testUniqueEmail()
    {
        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['email' => 'user@gmail.com']);
        $this->assertFalse($user->getEmail() !== "user@gmail.com", "L'email existe déjà.");
    }

    /**
     * @throws Exception
     */
    protected function tearDown(): void
    {
        $this->setCommand('doctrine:database:drop --force');

        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}