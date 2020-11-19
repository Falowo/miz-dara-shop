<?php

namespace App\Controller;

use App\Entity\DeliveryFees;
use App\Entity\Image;
use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use App\Entity\User;
use App\Repository\ImageRepository;
use App\Repository\StockRepository;
use App\Service\Cart\CartService;
use App\Service\Locale\LocaleService;
use App\Service\Mailer\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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

            if ($purchase->getAddress() && !$purchase->getDeliveryFees()) {
                return $this->redirectToRoute('cart_transport');
            }
        } else {
            $purchase = new Purchase();
        }

        return $this->render('cart/index.html.twig', [
            'purchase' => $purchase,

        ]);
    }
    /**
     *
     * @Route("/minus/{id}", name="cart_minus")
     * @param PurchaseLine $purchaseLine
     * @return RedirectResponse
     */
    public function minus(
        PurchaseLine $purchaseLine,
        CartService $cartService,
        FlashBagInterface $flashBagInterface
    ) {
        $em = $this->getDoctrine()->getManager();
        $q = $purchaseLine->getQuantity();
        if ($q > 0) {
            $q--;
            $purchaseLine->setQuantity($q);
            // $em->persist($purchaseLine());
            $em->flush();
        }

        $purchase = $cartService->getPurchase();
        if ($purchase->getDeliveryFees()) {

            $flashBagInterface->add('error', 'Your delivery service has been deleted because you modified your cart');
            return $this->redirectToRoute('cart_transport', ['edit' => true]);
        }

        return $this->redirectToRoute("cart_index");
    }

    /**
     *
     * @Route("/plus/{id}", name="cart_plus")
     * @param PurchaseLine $purchaseLine
     * @return RedirectResponse
     */

    public function plus(
        PurchaseLine $purchaseLine,
        CartService $cartService,
        FlashBagInterface $flashBagInterface
    ) {
        $em = $this->getDoctrine()->getManager();
        $s = $purchaseLine->getProduct()->getStock($purchaseLine->getSize(), $purchaseLine->getTint());
        $q = $purchaseLine->getQuantity();

        if ($s > $q) {
            $q++;
            $purchaseLine->setQuantity($q);
            // $em->persist($purchaseLine());
            $em->flush();
        }
        $purchase = $cartService->getPurchase();
        if ($purchase->getDeliveryFees()) {

            $flashBagInterface->add('error', 'Your delivery service has been deleted because you modified your cart');
            return $this->redirectToRoute('cart_transport', ['edit' => true]);
        }

        return $this->redirectToRoute("cart_index");
    }


    /**
     *
     * @Route("/{id}", name="purchaseLine_delete", methods={"DELETE"})
     * @param Request $request
     * @param PurchaseLine $purchaseLine
     * @return RedirectResponse
     */
    public function delete(
        Request $request,
        PurchaseLine $purchaseLine,
        CartService $cartService,
        FlashBagInterface $flashBagInterface
    ) {


        if ($this->isCsrfTokenValid('delete' . $purchaseLine->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($purchaseLine);
            $entityManager->flush();
        }

        $purchase = $cartService->getPurchase();
        if ($purchase->getDeliveryFees()) {
            $flashBagInterface->add('error', 'Your delivery service has been deleted because you modified your cart');

            return $this->redirectToRoute('cart_transport', ['edit' => true]);
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

            if ($purchase->getAddress()) {
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




            return $this->render('cart/validate.html.twig', [
                'purchase' => $purchase
            ]);
        } else {
            return $this->redirectToRoute('app_index');
        }
    }



    /**
     * @Route("/transport/{edit}", name="cart_transport",defaults={"edit"=false})
     */
    public function transport(CartService $cartService, LocaleService $localeService, bool $edit, Request $request)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        $purchase = $cartService->getPurchase();
        $user = $this->getUser();
        if ($purchase) {
            if ($purchase->getDeliveryFees() && $edit === false) {
                return $this->redirectToRoute('cart_index');
            } else ($purchase->setDeliveryFees(null));
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
     *
     * @Route("/complete/{id}", name="cart_setDeliveryFees", requirements={"id"="\d+"})
     */
    public function setDeliveryFees(
        DeliveryFees $deliveryFees,
        CartService $cartService,
        EntityManagerInterface $em
    ) {
        if ($deliveryFees) {
            $purchase = $cartService->getPurchase();

            $purchase
                ->setDeliveryFees($deliveryFees)
                ->setMaxDays($cartService->getMaxDays($purchase))
                ->setDeliveryPrice($cartService->getDeliveryPrice($deliveryFees, $purchase));
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
    public function pay(
        CartService $cartService,
        MailerService $mailerService,
        SessionInterface $session
    ) {

        $purchase = $cartService->getPurchase();
        if ($purchase) {

            $purchase
                ->setPaid(true)
                ->setDeliveryPrice($cartService->getDeliveryPrice($purchase->getDeliveryFees(), $purchase))
                ->setTotalPurchaseLines($cartService->getTotalPurchaseLines($purchase))
                ->setTotal($cartService->getTotalPurchase($purchase))
                ->setMaxDays($cartService->getMaxDays($purchase));                 
                $cartService->setImages($purchase);
            foreach ($purchase->getPurchaseLines() as $purchaseLine) {
                $stock = $purchaseLine->getStock();
                $stock->setQuantity($stock->getQuantity() - $purchaseLine->getQuantity());
                $purchaseLine->setPrice($cartService->getPurchaseLinePrice($purchaseLine));
            }
            $em = $this->getDoctrine()->getManager();
            $em->flush();


            $mailerService->sendPurchasePaymentConfirmation($this->getUser());


            $session->remove('purchaseId');

            return $this->render('cart/paid.html.twig', [
                'purchase' => $purchase
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
        if ($purchase = $cartService->getPurchase()) {

                $cartService->setImages($purchase);
           
            if (!($purchase->getPaid())) {
                $purchase->setTotal($cartService->getTotalPurchase($purchase));
                $em = $this->getDoctrine()->getManager();
                $em->flush();
            }
        }else {
                $purchase = new Purchase();
        }



        return $this->render('cart/_cart.html.twig', [
            'purchase' => $purchase,
        ]);
    }

    public function link(CartService $cartService)
    {
        $purchase = $cartService->getPurchase();


        return $this->render('cart/_link.html.twig', [
            'purchase' => $purchase,

        ]);
    }

    public function showPurchaseAddress(CartService $cartService)
    {
        if ($purchase = $cartService->getPurchase()) {
            return $this->render('cart/_address.html.twig', [
                'purchase' => $purchase
            ]);
        }
    }
}
