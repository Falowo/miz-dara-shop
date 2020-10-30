<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\PurchaseLine;
use App\Form\PurchaseLineType;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package App\Controller
 * @Route("/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/{id}/{slug}", requirements={"id": "\d+"}, name="product_add")
     * @param Product $product
     * @param Request $request
     * @param CartService $cartService
     * @return Response
     */
    public function add(Product $product, Request $request, CartService $cartService)
    {

            $product->setHasStock(null);
            $this->getDoctrine()->getManager()->persist($product);
            $this->getDoctrine()->getManager()->flush();

        $purchaseLine = new PurchaseLine();
        $purchaseLine->setProduct($product);

        $form = $this->createForm(PurchaseLineType::class, $purchaseLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if(
                !is_null($purchaseLine->getProduct()) &&
                !is_null($purchaseLine->getSize()) &&
                !is_null($purchaseLine->getTint())
               
            ){
                if (is_null($purchaseLine->getQuantity())){
                    $purchaseLine->setQuantity(1);
                }

                $cartService->add($purchaseLine);
                return $this->redirectToRoute('cart_index');

            }


        }

        return $this->render('product/add.html.twig', [
            'controller_name' => 'ProductController',
            'product' => $product,
            'purchaseLine' => $purchaseLine,
            'form' => $form->createView()
        ]);
    }


}
