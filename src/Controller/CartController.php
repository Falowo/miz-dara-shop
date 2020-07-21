<?php

namespace App\Controller;

use App\Entity\DeliveryFees;
use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use App\Entity\User;
use App\Repository\ImageRepository;
use App\Repository\StockRepository;
use App\Service\Cart\CartService;
use App\Service\Locale\LocaleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class CartController
 * @package App\Controller
 * @Route("/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="cart_index")
     * @param SessionInterface $session
     * @param CartService $cartService
     * @return Response
     */
    public function index(CartService $cartService)
    {
        if ($purchase = $cartService->getPurchase()) {
            
            if($purchase->getAddress() && !$purchase->getDeliveryFees()){
                return $this->redirectToRoute('cart_transport');
            }

            $total = $cartService->getTotalPurchaseLines($purchase);
        } else {
            $purchase = new Purchase();
            $total = 0;
        }

        return $this->render('cart/index.html.twig', [
            'purchase' => $purchase,
            'total' => $total
        ]);
    }


    /**
     *
     * @Route("/{id}", name="purchaseLine_delete", methods={"DELETE"})
     * @param Request $request
     * @param PurchaseLine $purchaseLine
     * @return RedirectResponse
     */
    public function delete(Request $request, PurchaseLine $purchaseLine)
    {


        if ($this->isCsrfTokenValid('delete' . $purchaseLine->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($purchaseLine);
            $entityManager->flush();
        }

        return $this->redirectToRoute("cart_index");
    }

    /**
     * @param CartService $cartService
     * @param EntityManagerInterface $manager
     * @Route("/validate", name="cart_validate")
     */
    public function validate(CartService $cartService, EntityManagerInterface $manager)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        /**
         * @var Purchase $purchase
         */
        $purchase = $cartService->getPurchase();

        if ($purchase && count($purchase->getPurchaseLines()) > 0) {

            if($purchase->getAddress()){
                return $this->redirectToRoute('cart_transport');
            }

            /**
             * @var User $user
             */
            $user = $this->getUser();
            

            if (!$user->getAddress()) {
                return $this->redirectToRoute('address_new');
            } else {
                $purchase->setUser($user);
                $manager->flush();
            }

           
            $total = $cartService->getTotalPurchase($purchase);
            

            return $this->render('cart/validate.html.twig', [
                'purchase' => $purchase,
                'total' => $total
            ]);
        } else {
            return $this->redirectToRoute('app_index');
        }
    }



    /**
     * @Route("/transport/{edit}", name="cart_transport",defaults={"edit"="0"}, requirements={"edit"="0|1"})
     */
    public function transport(CartService $cartService, LocaleService $localeService, bool $edit)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $purchase = $cartService->getPurchase();
        $user = $this->getUser();
        if ($purchase) {
            if($purchase->getDeliveryFees() && $edit===false){
                return $this->redirectToRoute('cart_index');
            }
            if (!$purchase->getAddress() && $user->getAddress()) {
                $purchase->setAddress($user->getAddress());
                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }
            $deliveryFeess = $cartService->getDeliveryFeess($purchase);
            
            foreach ($deliveryFeess as $deliveryFees) {
                $deliveryFees->setDeliveryPrice($cartService->getDeliveryPrice($deliveryFees, $purchase));
                if (!$deliveryFees->getMaxDays()) {
                    $deliveryFees->setMaxDays($deliveryFees->getTransport()->getMaxDaysByKm() * $localeService->getDistanceFromIlobuInKm($purchase->getAddress()));
                }
            }
        } else {
            return $this->redirectToRoute('app_index');
        }



        return $this->render('cart/transport.html.twig', [
            'deliveryFeess' => $deliveryFeess,
            'purchase' => $purchase
        ]);
    }

    /**
     * Undocumented function
     *
     * @Route("/complete/{id}", name="cart_setDeliveryFees", requirements={"id"="\d+"})
     */
    public function setDeliveryFees(
        DeliveryFees $deliveryFees,
        CartService $cartService,
        EntityManagerInterface $em,
        LocaleService $localeService
    ) {
        if($deliveryFees){
            $purchase = $cartService->getPurchase();
            if ($deliveryFees->getMaxDays()) {
                $maxDays = $deliveryFees->getMaxDays();
            } else {
                $maxDays = $deliveryFees->getTransport()->getMaxDaysByKm() *
                    $localeService->getDistanceFromIlobuInKm($purchase->getAddress());
            }
            $purchase->setDeliveryFees($deliveryFees);
            $purchase->setMaxDays($maxDays);
            $purchase->setDeliveryPrice($cartService->getDeliveryPrice($deliveryFees, $purchase));
            $em->flush();
        }
        return $this->redirectToRoute('cart_index');
    }

    /**
     * Undocumented function
     *
     * @param CartService $cartService
     * @param StockRepository $stockRepository
     * @return Response
     * @Route("/pay", name="cart_pay")
     */
    public function pay(CartService $cartService, StockRepository $stockRepository, SessionInterface $session)
    {

        $purchase = $cartService->getPurchase();
        if($purchase){

            $purchase->setPaid(true);
            foreach ($purchase->getPurchaseLines() as $purchaseLine) {
                $stock = $purchaseLine->getStock();
                $stock->setQuantity($stock->getQuantity() - $purchaseLine->getQuantity());
            }
            $total = $purchase->getTotalPurchaseLines() + $purchase->getDeliveryPrice();
            $em = $this->getDoctrine()->getManager();
            $em->flush();
    
            $session->remove('purchaseId');
            
            return $this->render('cart/paid.html.twig', [
                'purchase' => $purchase,
                'total' => $total             
                ]);
            }
           
        return $this->redirectToRoute('app_index');
    }

    /**
     * @param SessionInterface $session
     * @param CartService $cartService
     * @return Response
     */
    public function cart(CartService $cartService, ImageRepository $imageRepository)
    {
        if ($cartService->getPurchase()) {
            /**
             * @var Purchase $purchase
             */
            $purchase = $cartService->getPurchase();

            
        
            $total = $cartService->getTotalPurchaseLines($purchase);
            if ($purchase->getTotalPurchaseLines() !== $total)
                $purchase->setTotalPurchaseLines($total);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            if ($purchase->getDeliveryPrice()) {
                $total += $purchase->getDeliveryPrice();
            }
            foreach($purchase->getPurchaseLines() as $purchaseLine){
                $image = $imageRepository->findOneBy([
                        'product'=>$purchaseLine->getProduct(),
                        'tint'=>$purchaseLine->getTint()
                        ]);
                   if(!$image){
                       $image = $purchaseLine->getProduct()->getMainImage();
                   }

                $purchaseLine->setImage($image);
            }

        } else {
            $purchase = new Purchase();
            $total = 0;
        }

        

        return $this->render('cart/_cart.html.twig', [
            'purchase' => $purchase,
            'total' => $total            
        ]);
    }
    
    public function link(CartService $cartService)
    {
        $purchase = $cartService->getPurchase();

        
        return $this->render('cart/_link.html.twig', [
            'purchase' => $purchase,
            
        ]);
    }
}
