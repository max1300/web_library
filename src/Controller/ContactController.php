<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Contact;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Mail\SymfonyMailer;
use Symfony\Component\HttpFoundation\Response;

class ContactController extends AbstractController
{

    /**
     * @var SymfonyMailer
     */
    private $mailer;


    /**
     * ContactController constructor.
     * @param SymfonyMailer $mailer
     */
    public function __construct(SymfonyMailer $mailer)
    {
        $this->mailer = $mailer;
    }


    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return JsonResponse|Response
     */
    public function SendMessage(Request $request, ValidatorInterface $validator)
    { 
       //Creation de l'objet
        $contact = new Contact();

       //Récuperer les valeurs json et les insérer dans le new contact que je viens de créer
       //je reçoi un tableau avec l'objet json
        $data =json_decode($request -> getContent(), true);
        //Récupérer les champs
        //En créant l'entité contact je vais devoir faire : 
        $contact -> setName($data['name']);
        $contact-> setEmail($data['email']);
        $contact-> setSubject($data['subject']);
        $contact-> setMessage($data['message']);

        //we you need to customize the errors with symfony validator; 
        $nameError = $validator->validateProperty($contact, 'name');
        $emailError = $validator->validateProperty($contact, 'email');
        $subjectError = $validator->validateProperty($contact, 'subject');
        $messageError = $validator->validateProperty($contact, 'message');

        $formErrors = [];
        if(count($nameError) > 0) {
            $formErrors['nameError'] = $nameError[0]->getMessage();
        }
        if(count($emailError) > 0) {
            $formErrors['emailError'] = $emailError[0]->getMessage();
        }
        if(count($subjectError) > 0) {
            $formErrors['subjectError'] = $subjectError[0]->getMessage();
        }   
        if(count($messageError) > 0) {
            $formErrors['messageError'] = $messageError[0]->getMessage();
        }         
        if($formErrors) {
            return new JsonResponse($formErrors);
        }

        //Sending mail if the contact form does not contain errors
        if ($formErrors === null)
        {
            $this->mailer->sendContactMessage($contact);
            return new Response('OK');
        }
    }
}