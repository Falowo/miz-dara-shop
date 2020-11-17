<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Purchase;
use App\Form\ContactType;
use App\Repository\PurchaseRepository;
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
     * @Route("/{id}", name="contact_index", defaults={"id": null})
     */
    public function index(
        Request $request,
        ?Purchase $purchase,
        MailerService $mailerService, 
        PurchaseRepository $purchaseRepository
    ): Response {
        $contact = new Contact();
        if ($purchase){
            $contact->setPurchase($purchase);
        }
        if ($user = $this->getUser()) {
            $contact
                ->setUser($user)
                ->setFirstName($user->getFirstName())
                ->setEmail($user->getEmail());
        }
        $form = $this->createForm(ContactType::class, $contact);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mailerService->sendContactEmail($contact);
            if(!($contact->getPurchase())){
                return $this->redirectToRoute('contact_index');
            }else{
                return $this->redirectToRoute('purchase_detail', [
                    'id'=>$contact->getPurchase()->getId()
                    ]);
            }
        }

        return $this->render('contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'contact' => $contact,
            'form' => $form->createView()
        ]);
    }
}
