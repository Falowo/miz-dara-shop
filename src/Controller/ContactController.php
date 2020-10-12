<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\Mailer\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/contact")
 */
class ContactController extends AbstractController
{
    /**
     * @Route("/", name="contact_index")
     */
    public function index(Request $request, MailerService $mailerService): Response
    {
        $contact = new Contact();
        if($user = $this->getUser()){
            $contact
            ->setFirstName($user->getFirstName())
            ->setEmail($user->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $mailerService->sendContactEmail($contact);
            $this->addFlash('success', 'Your message has been successfully sent');
        }


        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form->createView()
        ]);
    }

    

    
}
