<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use App\Entity\FavoritesPosts;
use App\Entity\Posts;
use App\Repository\CategoriesRepository;
use App\Repository\UsersRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker\Factory;

class AppPostsFixtures extends Fixture implements DependentFixtureInterface
{
    private $manager;
    private $userRepository;
    private $categoriesRepository;

    public function __construct(UsersRepository $usersRepository, CategoriesRepository $categoriesRepository)
    {
        $this->userRepository = $usersRepository;
        $this->categoriesRepository = $categoriesRepository;
        $this->faker = Factory::create('fr_FR');
    }

    /**
     * @inheritDoc
     */
    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $users = $this->userRepository->findAll();

        $category = new Categories();
        $category->setValue('graphismes, design')
                    ->setCss('fas fa-fill-drip')
                    ->setColor('#15aabf');
        $this->manager->persist($category);
        $this->manager->flush();

        $category2 = new Categories();
        $category2->setValue('e-commerce')
            ->setCss('far fa-credit-card')
            ->setColor('#be4bdb');
        $this->manager->persist($category2);
        $this->manager->flush();

        $category3 = new Categories();
        $category3->setValue('social media')
            ->setCss('fas fa-user-friends')
            ->setColor('#4c6ef5');
        $this->manager->persist($category3);
        $this->manager->flush();

        $array = [$category->getId(), $category2->getId(), $category3->getId()];

        foreach ($users as $user) {
            for ($i = 1; $i < 3; $i++) {
                $category = array_rand(array_flip($array));
                $id_category = $this->categoriesRepository->findOneBy(['id' => $category]);
                $post = new Posts();
                $post->setTitle($this->faker->sentence(6, true))
                    ->setKicker($this->faker->sentence(9, true))
                    ->setContent($this->faker->paragraph(2))
                    ->setAuthor($user)
                    ->setCategory($id_category)
                    ->setCreatedAt(new \DateTime())
                    ->setModifiedAt(new \DateTime())
                    ->setState($this->faker->boolean);

                $this->manager->persist($post);

                for ($j = 1; $j < 3; $j++) {
                    $favoris = new FavoritesPosts();
                    $favoris->setPost($post)
                        ->setUser($user);
                    $this->manager->persist($favoris);
                }
            }
        }

        $this->manager->flush();
    }

    public function getDependencies(): array
    {
        return [
          AppFixtures::class,
        ];
    }
}