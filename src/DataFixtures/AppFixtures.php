<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private ObjectManager $manager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private UserPasswordEncoderInterface $encoder;
    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->createUsers();
        $this->createMicroPosts();

        $manager->flush();
    }

    private function createUsers()
    {
        $this->manager->persist($this->makeJohnDoeUser());

        for ($i = 0; $i < 10; $i++) {
            $this->manager->persist($this->makeFakeUser());
        }
    }

    private function makeJohnDoeUser(): User
    {
        $user = new User;
        $user->setUsername('john_doe');
        $user->setEmail('john@doe.com');
        $user->setFullName('John Doe');
        $user->setPassword($this->encoder->encodePassword($user, 'john123'));

        return $user;
    }

    private function makeFakeUser(): User
    {
        $user = new User;

        $user->setUsername($this->faker->userName);
        $user->setEmail($this->faker->email);
        $user->setFullName($this->faker->name);
        $user->setPassword($this->encoder->encodePassword($user, $this->faker->password));

        return $user;
    }

    private function createMicroPosts()
    {
        for ($i = 0; $i < 10; $i++) {
            $microPost = new MicroPost;
            $microPost->setText($this->faker->sentence($this->faker->numberBetween(3, 6)));
            $microPost->setTime($this->faker->dateTimeBetween('-1 year'));

            $this->manager->persist($microPost);
        }
    }
}
