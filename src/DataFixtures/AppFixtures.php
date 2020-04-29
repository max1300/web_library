<?php

namespace App\DataFixtures;

use App\Entity\Framework;
use App\Entity\Level;
use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Length;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {

       /*  $faker = \Faker\Factory::create();*/
        $manager->flush();
    }
}
