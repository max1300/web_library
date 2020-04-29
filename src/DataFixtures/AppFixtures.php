<?php

namespace App\DataFixtures;

<<<<<<< HEAD
use App\Entity\Framework;
use App\Entity\Level;
use App\Entity\Program;
=======
use App\Entity\Author;
use App\Entity\Framework;
use App\Entity\Level;
use App\Entity\Program;
use App\Entity\Ressource;
use App\Entity\TopicFramework;
use App\Entity\TopicProgrammingLanguage;
>>>>>>> bfe2bf0a5c4036119db48dc3ea48b0b8b00e041a
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Length;

class AppFixtures extends Fixture
{
<<<<<<< HEAD
    public function load(ObjectManager $manager)
    {

       /*  $faker = \Faker\Factory::create();*/
        $manager->flush();
    }
=======
  public function load(ObjectManager $manager)
  {
    $fabPot = new Author();
    $fabPot->setName('Fabien Potencier')
      ->setWebsite('https://symfony.com/');
    $manager->persist($fabPot);

    $intermediate = new Level();
    $intermediate->setName('intermédiaire');
    $manager->persist($intermediate);

    $php = new Program();
    $php->setName('PHP');
    $manager->persist($php);

    $symfony = new Framework();
    $symfony->setName('Symfony')
      ->setDocUrl('https://symfony.com/doc/current/index.html')
      ->setProgram($php);
    $manager->persist($symfony);

    $phpTopic = new TopicProgrammingLanguage();
    $phpTopic->setProgrammingLanguage($php);
    $manager->persist($phpTopic);

    $symfonyTopic = new TopicFramework();
    $symfonyTopic->setFramework($symfony);
    $manager->persist($symfonyTopic);

    $tutoPhp = new Ressource();
    $tutoPhp->setAuthor($fabPot)
      ->setLanguage('fr')
      ->setLevel($intermediate)
      ->setName('Découvrez les tableaux en PHP')
      ->setUrl('https://symfony.com/doc/current/index.html')
      ->setTopic($phpTopic);
    $manager->persist($tutoPhp);

    $tutoSymfony = new Ressource();
    $tutoSymfony->setAuthor($fabPot)
      ->setLanguage('fr')
      ->setLevel($intermediate)
      ->setName('Découvrez Symfony')
      ->setUrl('https://symfony.com/doc/current/index.html')
      ->setTopic($symfonyTopic);
    $manager->persist($tutoSymfony);

    $manager->flush();
  }
>>>>>>> bfe2bf0a5c4036119db48dc3ea48b0b8b00e041a
}