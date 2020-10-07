<?php

namespace App\DataFixtures;

use App\Entity\MicroPost;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 10; $i++) {
            $microPost = new MicroPost;
            $microPost->setText('Some random text ' . mt_rand(0, 100));
            $microPost->setTime(new DateTime(date('Y-m-d', time() + mt_rand(-500000, 500000))));

            $manager->persist($microPost);
        }

        $manager->flush();
    }
}
