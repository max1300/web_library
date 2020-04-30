<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Framework;
use App\Entity\Level;
use App\Entity\Program;
use App\Entity\Ressource;
use App\Entity\TopicFramework;
use App\Entity\TopicProgrammingLanguage;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Validator\Constraints\Length;

class AppFixtures extends Fixture
{
  public function load(ObjectManager $manager)
  {

    $authors = ['Fabien Potencier', 'Damien Terro', 'Maxime Renaud'];
    $websites = ['https://symfony.com/', 'https://Damien Terro.com', 'https://Maxime Renaud.com'];
    $levels = ['Débutant', 'Intermédiaire', 'Confirmé'];
    $programs = ['PHP', 'JAVASCRIPT', 'JAVA'];

    for($i = 0; $i < 3; $i++) {
      $author = new Author();
      $author->setName($authors[$i])
             ->setWebsite($websites[$i]);
      $manager->persist($author);


      $level = new Level();
      $level->setName($levels[$i]);
      $manager->persist($level);


      $program = new Program();
      $program->setName($programs[$i]);
      $manager->persist($program);

      if($i === 0){
        $symfony = new Framework();
        $symfony->setName('Symfony')
                ->setDocUrl('https://symfony.com/doc/current/index.html')
                ->setProgram($program);
        $manager->persist($symfony);

        $phpTopic = new TopicProgrammingLanguage();
        $phpTopic->setProgrammingLanguage($program);
        $manager->persist($phpTopic);

        $symfonyTopic = new TopicFramework();
        $symfonyTopic->setFramework($symfony);
        $manager->persist($symfonyTopic);

        $tutoPhp = new Ressource();
        $tutoPhp->setAuthor($author)
          ->setLanguage('fr')
          ->setLevel($level)
          ->setName('Découvrez les tableaux en PHP')
          ->setUrl('https://symfony.com/doc/current/index.html')
          ->setTopic($phpTopic);
        $manager->persist($tutoPhp);

        $tutoSymfony = new Ressource();
        $tutoSymfony->setAuthor($author)
          ->setLanguage('fr')
          ->setLevel($level)
          ->setName('Découvrez Symfony')
          ->setUrl('https://symfony.com/doc/current/index.html')
          ->setTopic($symfonyTopic);
        $manager->persist($tutoSymfony);

      }

      if($i === 1){
        $react = new Framework();
        $react->setName('React')
                ->setDocUrl('https://react.com/doc/current/index.html')
                ->setProgram($program);
        $manager->persist($react);

        $javascriptTopic = new TopicProgrammingLanguage();
        $javascriptTopic->setProgrammingLanguage($program);
        $manager->persist($javascriptTopic);

        $reactTopic = new TopicFramework();
        $reactTopic->setFramework($react);
        $manager->persist($reactTopic);

        $tutoJavascript = new Ressource();
        $tutoJavascript->setAuthor($author)
          ->setLanguage('fr')
          ->setLevel($level)
          ->setName('Découvrez les tableaux en javascript')
          ->setUrl('https://react.com/doc/current/index.html')
          ->setTopic($javascriptTopic);
        $manager->persist($tutoJavascript);

        $tutoReact = new Ressource();
        $tutoReact->setAuthor($author)
          ->setLanguage('fr')
          ->setLevel($level)
          ->setName('Découvrez React')
          ->setUrl('https://react.com/doc/current/index.html')
          ->setTopic($reactTopic);
        $manager->persist($tutoReact);
      }

      if($i === 2){
        $spring = new Framework();
        $spring->setName('Spring')
                ->setDocUrl('https://spring.com/doc/current/index.html')
                ->setProgram($program);
        $manager->persist($spring);

        $javaTopic = new TopicProgrammingLanguage();
        $javaTopic->setProgrammingLanguage($program);
        $manager->persist($javaTopic);

        $springTopic = new TopicFramework();
        $springTopic->setFramework($spring);
        $manager->persist($springTopic);

        $tutoJava = new Ressource();
        $tutoJava->setAuthor($author)
          ->setLanguage('fr')
          ->setLevel($level)
          ->setName('Découvrez les tableaux en Java')
          ->setUrl('https://java.com/doc/current/index.html')
          ->setTopic($javaTopic);
        $manager->persist($tutoJava);

        $tutoSpring = new Ressource();
        $tutoSpring->setAuthor($author)
          ->setLanguage('fr')
          ->setLevel($level)
          ->setName('Découvrez Spring')
          ->setUrl('https://spring.com/doc/current/index.html')
          ->setTopic($springTopic);
        $manager->persist($tutoSpring);
      }
      
      
    }
    $manager->flush();
  }

    
}
