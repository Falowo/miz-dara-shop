<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\PurchaseRepository;
use App\Service\Cart\CartService;
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
        FlashBagInterface $flashBagInterface
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

            if($case==='user_address'){
                if($previousAddress = $user->getAddress()){
                    if (count($previousAddress->getPurchases())===0){
                        $removePreviousAddress = true;
                    }
                }
                $user->setAddress($address);
                if(isset($removePreviousAddress)){
                    $em->remove($previousAddress);
                }

                $em->persist($address);
                $em->flush();


                $flashBagInterface->add('success', 'Your address has been successfuly changed, but your former purchases delivery addresses remain unchanged');
            return $this->redirectToRoute('address_user');

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
            if($purchase->getDeliveryFees()){
                $edit=true;
            }else{
                $edit = false;
            }

            return $this->redirectToRoute('cart_transport', ['edit'=>$edit]);
        }

        return $this->render('address/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user", name="address_user") )
     */
    public function user()
    {
        return $this->render('address/user.html.twig');
    }
}
