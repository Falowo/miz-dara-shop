<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\PurchaseRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;



/**
 * @Route("/address")
 */
class AddressController extends AbstractController
{

    /**
     * @Route("/new/{case}/{edit}", name="address_new", defaults={"case": "new", "edit":false}) )
     */
    public function new(
        Request $request,
        CartService $cartService,
        PurchaseRepository $purchaseRepository,
        string $case,
        bool $edit
    ) {
        
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $user = $this->getUser();
       
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
            return $this->redirectToRoute('cart_transport', ['edit'=>$edit]);
        }

        return $this->render('address/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    
}
