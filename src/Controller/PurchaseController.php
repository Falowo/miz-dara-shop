<?php

namespace App\Controller;

use App\Entity\Purchase;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/purchase")
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("/", name="purchase_list")
     */
    public function index()
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        return $this->render('purchase/index.html.twig', [
            'controller_name' => 'PurchaseController',
        ]);
    }
    /**
     * @Route("/detail/{id}/{detail}", name="purchase_detail", defaults={"detail": "Your Purchase Detail"})
     */
    public function detail(
        Purchase $purchase,
        CartService $cartService,
        ?string $detail
    ) {
        
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_REMEMBERED');
        if ($purchase && $purchase->getUser() === $this->getUser() || $this->isGranted('ROLE_ADMIN')) {
            $cartService->setImages($purchase);

            return $this->render('purchase/detail.html.twig', [
                'purchase' => $purchase,
                'detail' => $detail
            ]);
        }
    }
}
