<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\PurchaseRepository;
use App\Service\Cart\CartService;
use App\Service\Mailer\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/address")
 */
class AddressController extends AbstractController
{

    /**
     * @Route("/new/{case}", name="address_new", defaults={"case": "new"}) )
     */
    public function new(
        Request $request,
        CartService $cartService,
        PurchaseRepository $purchaseRepository,
        string $case,
        MailerService $mailerService,
        FlashBagInterface $flashBagInterface
    ) {

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $user = $this->getUser();
        if (!($user->getConfirmedEMail())){
            $flashBagInterface->add('danger', 'You must confirm your email before you can continue a new confirmation email has been sent to you');
            $mailerService->sendSignUpEmail($user);

            return $this->redirectToRoute('cart_index');
        }
        $address = new Address();
        $form = $this->createForm(AddressType::class, $address);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            if (!$address->getLastName()) {
                $address
                    ->setFirstName($user->getFirstName())
                    ->setLastName($user->getLastName());
                }
            if ($case === 'modify' && $user->getAddress()) {
                $previousAddress = $user->getAddress();
                $user->setAddress($address);
                if($previousAddress){
                    $previousPurchases = $purchaseRepository->findBy(['address' => $previousAddress->getId()]);
                    if (count($previousPurchases) === 0) {
                        $em->remove($previousAddress);
                    }
                }
            }
            elseif (!$user->getAddress()) {
                $user->setAddress($address);
            }
            if ($purchase = $cartService->getPurchase()) {
                $purchase->setAddress($address);
            }
            $em->persist($address);
            $em->flush();
            return $this->redirectToRoute('cart_transport');
        }

        return $this->render('address/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    
}
