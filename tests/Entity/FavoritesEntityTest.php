<?php

namespace App\Tests\Entity;

use App\Entity\FavoritesPosts;
use App\Entity\Posts;
use App\Entity\Users;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Validator\ConstraintViolation;

class FavoritesEntityTest extends KernelTestCase
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

    protected function assertHasErrors(FavoritesPosts $posts, int $number = 0)
    {
        $kernel = self::bootKernel();
        $errors = $kernel->getContainer()->get('validator')->validate($posts);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    protected function getEntity():FavoritesPosts
    {
        return (new FavoritesPosts())
            ->setUser(new Users())
            ->setPost(new Posts());
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInstanceOfEntity()
    {
        $this->assertInstanceOf(FavoritesPosts::class, $this->getEntity());
    }

    public function testHasAttributesEntity()
    {
        $this->assertObjectHasAttribute('User', $this->getEntity());
        $this->assertObjectHasAttribute('Post', $this->getEntity());
    }

    public function testGetId()
    {
        $post = $this->entityManager->getRepository(FavoritesPosts::class)->findOneBy(['id' => 1]);
        $this->assertEquals($post->getId(), 1);
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