<?php

namespace App\DataFixtures;

use App\Entity\Author;
use App\Entity\Framework;
use App\Entity\Level;
use App\Entity\Program;
use App\Entity\Ressource;
use App\Entity\TopicFramework;
use App\Entity\TopicProgrammingLanguage;
use App\Security\TokenGenerator;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;
use App\Entity\User;

class AppFixtures extends Fixture
{
    private $encoder;
    /**
     * @var TokenGenerator
     */
    private $tokenGenerator;

    public function __construct(UserPasswordEncoderInterface $encoder, TokenGenerator $tokenGenerator)
    {
        $this->encoder = $encoder;
        $this->tokenGenerator = $tokenGenerator;
    }

    public function load(ObjectManager $manager) :void
    {
        $faker = Faker\Factory::create('fr_FR');
        $users = [];

        $admin = new User();
        $admin->setLogin('maxime')
            ->setEmail('maxime@gmail.com')
            ->setPassword($this->encoder->encodePassword(
                $admin,
                'maxime1234'
            ))
            ->setForgotPasswordToken($this->tokenGenerator->getRandomToken())
            ->setRoles(['ROLE_ADMIN'])
            ->setEnabledAccount(true);

        $manager->persist($admin);

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $login = $faker->userName;
            $user->setEmail($faker->email)
                ->setLogin($login)
                ->setPlainPassword('pass_' . $login)
                ->setForgotPasswordToken($this->tokenGenerator->getRandomToken())
                ->setRoles(['ROLE_USER'])
                ->setProfilePic($faker->imageUrl(150, 150));

            if ($i === 2 || $i === 6) {
                $user->setEnabledAccount(false);
                $user->setTokenConfirmation($this->tokenGenerator->getRandomToken());
            } else {
                $user->setEnabledAccount(true);
            }

            $manager->persist($user);
            $users[] = $user;
        }


        $authors = ['Fabien Potencier', 'Damien Terro', 'Maxime Renaud'];
        $websites = ['https://symfony.com/', 'https://DamienTerro.com', 'https://MaximeRenaud.com'];
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
              try {
                  $this->getDataPhp($manager, $program, $author, $level, $users);
              } catch (Exception $e) {
                  echo 'Exception reçue : ',  $e->getMessage(), "\n";
              }
          }

          if($i === 1){
              try {
                  $this->getDataJavascript($manager, $program, $author, $level, $users);
              } catch (Exception $e) {
                  echo 'Exception reçue : ',  $e->getMessage(), "\n";
              }
          }

          if($i === 2){
              try {
                  $this->getDataJava($manager, $program, $author, $level, $users);
              } catch (Exception $e) {
                  echo 'Exception reçue : ',  $e->getMessage(), "\n";
              }
          }
      }
    $manager->flush();
  }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @param Author $author
     * @param Level $level
     * @param $users
     * @throws Exception
     */
    public function getDataPhp(ObjectManager $manager, Program $program, Author $author, Level $level, $users): void
    {
        $symfony = $this->getFramework(
            $manager,
            $program,
            'Symfony',
            'https://symfony.com/doc/current/index.html'
        );

        $phpTopic = $this->getProgrammingTopic($manager, $program);

        $symfonyTopic = $this->getFrameworkTopic($manager, $symfony);

        $this->getResource(
            $manager,
            $author,
            $level,
            $phpTopic,
            'French',
            'Découvrez les tableaux en PHP',
            'https://symfony.com/doc/current/index.html',
            $users[random_int(0,9)]
        );

        $this->getFrameworkResource(
            $manager,
            $author,
            $level,
            $symfonyTopic,
            'French',
            'Découvrez Symfony',
            'https://symfony.com/doc/current/index.html',
            $users[random_int(0,9)]
        );
    }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @param Author $author
     * @param Level $level
     * @param $users
     * @throws Exception
     */
    public function getDataJavascript(ObjectManager $manager, Program $program, Author $author, Level $level, $users): void
    {
        $react = $this->getFramework(
            $manager,
            $program,
            'React',
            'https://react.com/doc/current/index.html'
        );

        $javascriptTopic = $this->getProgrammingTopic($manager, $program);

        $reactTopic = $this->getFrameworkTopic($manager, $react);

        $this->getResource(
            $manager,
            $author,
            $level,
            $javascriptTopic,
            'French',
            'Découvrez les tableaux en javascript',
            'https://react.com/doc/current/index.html',
            $users[random_int(0,9)]
        );

        $this->getFrameworkResource(
            $manager,
            $author,
            $level,
            $reactTopic,
            'French',
            'Découvrez React',
            'https://react.com/doc/current/index.html',
            $users[random_int(0,9)]
        );
    }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @param Author $author
     * @param Level $level
     * @param $users
     * @throws Exception
     */
    public function getDataJava(ObjectManager $manager, Program $program, Author $author, Level $level, $users): void
    {
        $spring = $this->getFramework(
            $manager,
            $program,
            'Spring',
            'https://spring.com/doc/current/index.html'
        );

        $javaTopic = $this->getProgrammingTopic($manager, $program);

        $springTopic = $this->getFrameworkTopic($manager, $spring);

        $this->getResource(
            $manager,
            $author,
            $level,
            $javaTopic,
            'French',
            'Découvrez les tableaux en Java',
            'https://java.com/doc/current/index.html',
            $users[random_int(0,9)]
        );

        $this->getFrameworkResource(
            $manager,
            $author,
            $level,
            $springTopic,
            'French',
            'Decouvrez Spring',
            'https://spring.com/doc/current/index.html',
            $users[random_int(0,9)]
        );
    }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @return TopicProgrammingLanguage
     */
    public function getProgrammingTopic(ObjectManager $manager, Program $program): TopicProgrammingLanguage
    {
        $topic = new TopicProgrammingLanguage();
        $topic->setProgrammingLanguage($program);
        $manager->persist($topic);
        return $topic;
    }

    /**
     * @param ObjectManager $manager
     * @param Framework $framework
     * @return TopicFramework
     */
    public function getFrameworkTopic(ObjectManager $manager, Framework $framework): TopicFramework
    {
        $frameworkTopic = new TopicFramework();
        $frameworkTopic->setFramework($framework);
        $manager->persist($frameworkTopic);
        return $frameworkTopic;
    }

    /**
     * @param ObjectManager $manager
     * @param Program $program
     * @param $name
     * @param $url
     * @return Framework
     */
    public function getFramework(ObjectManager $manager, Program $program, $name, $url): Framework
    {
        $framework = new Framework();
        $framework->setName($name)
            ->setDocUrl($url)
            ->setProgram($program);
        $manager->persist($framework);
        return $framework;
    }

    /**
     * @param ObjectManager $manager
     * @param Author $author
     * @param Level $level
     * @param TopicProgrammingLanguage $programmingLanguage
     * @param $language
     * @param $name
     * @param $url
     * @param $users
     */
    public function getResource(
        ObjectManager $manager,
        Author $author,
        Level $level,
        TopicProgrammingLanguage $programmingLanguage,
        $language,
        $name,
        $url,
        $users
    ): void
    {
        $resource = new Ressource();
        $resource->setAuthor($author)
            ->setLanguage($language)
            ->setLevel($level)
            ->setName($name)
            ->setUrl($url)
            ->setTopic($programmingLanguage)
            ->setUser($users);
        $manager->persist($resource);
    }

    /**
     * @param ObjectManager $manager
     * @param Author $author
     * @param Level $level
     * @param TopicFramework $framework
     * @param $language
     * @param $name
     * @param $url
     * @param $users
     */
    public function getFrameworkResource(
        ObjectManager $manager,
        Author $author,
        Level $level,
        TopicFramework $framework,
        $language,
        $name,
        $url,
        $users
    ): void
    {
        $resource = new Ressource();
        $resource->setAuthor($author)
            ->setLanguage($language)
            ->setLevel($level)
            ->setName($name)
            ->setUrl($url)
            ->setTopic($framework)
            ->setUser($users);
        $manager->persist($resource);
    }
  
}
