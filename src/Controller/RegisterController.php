<?php

namespace App\Controller;

use App\Entity\User;
use App\Event\UserRegisteredEvent;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RegisterController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    //pour modifier la base de donnée
    private $entityManager;


    /**
     * RegisterController constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }



    public function register(Request $request, EventDispatcherInterface $dispatcher)
    {
        //on récupère le contenu de l'objet JSON
        $content = json_decode($request->getContent(), true);

        //Creation d'un nouvel utilisateur
        $user = new User();

        //on crée un formulaire symfony d'enregistrement de user tel que cela a été
        //défini dans la classe Usertype du package form
        $form = $this->createForm(UserType::class, $user);
        //on insère les données du formulaire react dans le formulaire symfony
        //si le nom des champs ne correspondent pas on aura une erreur
        $form->submit($content);

        //on check la validité de chaque champ contenu dans le formulaire
        //on laisse la main à l'entity user pour le login et l'email
        //par contre on prend la main dans le formulaire sur les contraintes de validation sur le champ plainPassword
        //afin de s'assurer que le mot de passe du formulaire react se conforme bien aux contraintes de validité (voir dans UserType)
        if (!$form->isValid()) {
            $errors = $this->getErrors($form);
            return new Response(json_encode($errors), Response::HTTP_BAD_REQUEST);
            //si on détecte une contrainte de validation non respectée, on lance une reponse JSON vers le front
        }

        //Ici on crée un nouvel Event Personnalisé
        //qui prend en paramètres un objet User ensuite 
        //cet evenément sera dispatcher au Suscriber
        //c'est à qu'il écoutera cet event en particulier  
        $event = new UserRegisteredEvent($user);
        $dispatcher->dispatch($event, UserRegisteredEvent::NAME);
        //on fait persister le nouveau user créé
        $this->entityManager->persist($user);
        //puis on l'enregistre en BDD
        $this->entityManager->flush();

        return new Response(sprintf('User %s successfully created', $user->getUsername()));
    }

    private function getErrors(FormInterface $form): array
    {
        $errors = [];
        $formErrors = $form->getErrors(true);

        foreach ($formErrors as $error) {
            $field = $error->getOrigin()->getName();
            $message = $error->getMessage();

            $errors[$field] = $message;
        }

        return $errors;
    }
}
