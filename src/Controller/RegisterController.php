<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;


class RegisterController
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
    )
    {
        $this->entityManager = $entityManager;
    }



    public function register(Request $request)
    {
        //on récupère le contenu de l'objet JSON
        $content = json_decode($request->getContent(), true);

        //Creation d'un nouvel utilisateur
        $user = new User();

        //on crée un formulaire symfony d'enregistrement de user tel que cela a été
        //défini dans la classe Usertype du package form
        //$form = $this->createForm(UserType::class, $user);
        $form = $this->createFrom(UserType::class, $user);
        //on insère les données du formulaire react dans le formulaire symfony
        //si le nom des champs ne correspondent pas on aura une erreur
        $form->submit($content);

        //on check la validité de chaque champ contenu dans le formulaire
        //on laisse la main à l'entity user pour le login et l'email
        //par contre on prend la main dans le formulaire sur les contraintes de validation sur le champ plainPassword
        //afin de s'assurer que le mot de passe du formulaire react se conforme bien aux contraintes de validité (voir dans UserType)
        if (!$form->isValid()) {
            $errors = $this->getErrors($form);
            throw new BadRequestHttpException(json_encode($errors));
            //si on détecte une contrainte de validation non respectée, on lance une reponse JSON vers le front
//            return $this->json($errors, 400);
        }

        //Ici intervient le registerSubscriber qui est un doctrine subscriber
        //et qui va encoder le mot de passe, créé le token pour envoyer la confirmation
        //et envoyer l'email pour l'activation du compte

        //sinon on fait persister le nouveau user créé
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