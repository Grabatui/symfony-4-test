<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use App\Entity\User;
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
        $this->manager->persist($this->makeAdminUser());
        $this->manager->persist($this->makeJohnDoeUser());

        for ($i = 0; $i < 10; $i++) {
            $user = $this->makeFakeUser();

            $this->manager->persist($user);

            $this->addReference($i . '_user', $user);
        }
    }

    private function makeAdminUser(): User
    {
        $user = new User;
        $user->setUsername('admin');
        $user->setEmail('admin@admin.com');
        $user->setFullName('Admin admin');
        $user->setPassword($this->encoder->encodePassword($user, 'admin123'));
        $user->setRoles([User::ROLE_ADMIN]);

        return $user;
    }

    private function makeJohnDoeUser(): User
    {
        $user = new User;
        $user->setUsername('john_doe');
        $user->setEmail('john@doe.com');
        $user->setFullName('John Doe');
        $user->setPassword($this->encoder->encodePassword($user, 'john123'));
        $user->setRoles([User::ROLE_USER]);

        $this->addReference('john_doe', $user);

        return $user;
    }

    private function makeFakeUser(): User
    {
        $user = new User;

        $username = $this->faker->userName;

        $user->setUsername($username);
        $user->setEmail($this->faker->email);
        $user->setFullName($this->faker->name);
        $user->setPassword($this->encoder->encodePassword($user, $this->faker->password));
        $user->setRoles([User::ROLE_USER]);

        return $user;
    }

    private function createMicroPosts()
    {
        for ($i = 0; $i < 10; $i++) {
            $microPost = new MicroPost;
            $microPost->setText($this->faker->sentence($this->faker->numberBetween(3, 6)));
            $microPost->setTime($this->faker->dateTimeBetween('-1 year'));

            $microPost->setUser($this->chooseMicroPostUser());

            $this->manager->persist($microPost);
        }
    }

    private function chooseMicroPostUser(): User
    {
        $chosenUserIndex = $this->faker->numberBetween(0, 10);

        $referenceCode = ($chosenUserIndex === 10) ? 'john_doe' : $chosenUserIndex . '_user';

        /** @var User $user */
        $user = $this->getReference($referenceCode);

        return $user;
    }
}
