<?php

namespace App\Tests\Entity;

use App\Entity\Categories;
use Exception;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Validator\ConstraintViolation;

class CategoriesEntityTest extends KernelTestCase
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

    protected function assertHasErrors(Categories $categories, int $number = 0)
    {
        $kernel = self::bootKernel();
        $errors = $kernel->getContainer()->get('validator')->validate($categories);
        $messages = [];
        /** @var ConstraintViolation $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
        }
        $this->assertCount($number, $errors, implode(', ', $messages));
    }

    protected function getEntity(): Categories
    {
        return (new Categories())
            ->setValue('Lorem Ipsum')
            ->setCss('Lorem Ipsum')
            ->setColor('Lorem Ipsum');
    }

    public function testValidEntity()
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInstanceOfEntity()
    {
        $this->assertInstanceOf(Categories::class, $this->getEntity());
    }

    public function testHasAttributesEntity()
    {
        $this->assertObjectHasAttribute('value', $this->getEntity());
        $this->assertObjectHasAttribute('css', $this->getEntity());
        $this->assertObjectHasAttribute('color', $this->getEntity());
    }

    public function testGetId()
    {
        $user = $this->entityManager->getRepository(Categories::class)->findOneBy(['value' => 'e-commerce']);
        $this->assertEquals($user->getId(), 2);
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