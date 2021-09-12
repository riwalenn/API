<?php

namespace App\Tests\Entity;

use App\Entity\Categories;
use App\Entity\Posts;
use App\Entity\Users;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Validator\ConstraintViolation;

class PostsEntityTest extends KernelTestCase
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

    protected function assertHasErrors(Posts $posts, int $number = 0)
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

    protected function getEntity(): Posts
    {
        return (new Posts())
            ->setAuthor(new Users())
            ->setTitle('simply dummy text of the printing')
            ->setKicker('Lorem Ipsum is simply dummy text of the printing and typesetting industry.')
            ->setContent('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi laoreet diam et enim dictum scelerisque pharetra ac dui. Integer id scelerisque dui. Vestibulum placerat nisi vel ligula finibus gravida. Donec lobortis nulla vitae ipsum venenatis luctus. Morbi commodo fermentum lectus, nec mattis ipsum maximus nec. Sed ultrices lobortis velit sit amet luctus. Sed sapien neque, faucibus at metus quis, sodales euismod diam.')
            ->setState(1)
            ->setCategory(new Categories())
            ->setCreatedAt(new \DateTime())
            ->setModifiedAt(new \DateTime());
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInstanceOfEntity()
    {
        $this->assertInstanceOf(Posts::class, $this->getEntity());
    }

    public function testHasAttributesEntity()
    {
        $this->assertObjectHasAttribute('author', $this->getEntity());
        $this->assertObjectHasAttribute('title', $this->getEntity());
        $this->assertObjectHasAttribute('kicker', $this->getEntity());
        $this->assertObjectHasAttribute('content', $this->getEntity());
        $this->assertObjectHasAttribute('state', $this->getEntity());
        $this->assertObjectHasAttribute('category', $this->getEntity());
        $this->assertObjectHasAttribute('created_at', $this->getEntity());
        $this->assertObjectHasAttribute('modified_at', $this->getEntity());
    }

    public function testGetId()
    {
        $post = $this->entityManager->getRepository(Posts::class)->findOneBy(['id' => 1]);
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