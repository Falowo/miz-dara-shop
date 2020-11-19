<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\PurchaseLine;
use App\Form\PurchaseLineType;
use App\Repository\StockRepository;
use App\Service\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
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
    public function add(
        Product $product,
        Request $request,
        CartService $cartService,
        StockRepository $stockRepository,
        FlashBagInterface $flashBagInterface
    ) {

        
        $product->setHasStock(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        if ($product->getHasStock() === false) {
            return $this->redirectToRoute('app_index');
        }
        $purchaseLine = new PurchaseLine();
        $purchaseLine->setProduct($product);;
        $sizes = [];
        if (count($product->getStocks()) === 1) {
            $stock = $stockRepository->findOneBy(['product' => $product]);
            $sizes[] = $stock->getSize();

            $purchaseLine
                ->setSize($stock->getSize())
                ->setTint($stock->getTint());
        } elseif (count($stocks = $product->getStocks()) > 1) {
            $tints = [];
            foreach ($stocks as $stock) {
                if ($stock->getQuantity() > 0) {
                    if (!(in_array($size = $stock->getSize(), $sizes))) {
                        $sizes[] = $size;
                    }
                    if (!(array_search($tint = $stock->getTint(), $tints))) {
                        $tints[] = $tint;
                    }
                }
            }

            if (count($sizes) === 1) {
                $purchaseLine->setSize($sizes[0]);
            }
            if (count($tints) === 1) {
                $purchaseLine->setTint($tints[0]);
            }
        }
        $tints = [];
        foreach ($sizes  as $size) {
            $stocks = $stockRepository->findBy(['product' => $product, 'size' => $size]);
            $tints[$size->getName()] = [];
            foreach ($stocks as $stock) {
                if ($stock->getQuantity() > 0) {
                    if (!in_array($tint = $stock->getTint(),  $tints[$size->getName()])) {
                        $tints[$size->getName()][] = $tint;
                    }
                }
            }
        }






        $form = $this->createForm(PurchaseLineType::class, $purchaseLine);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (
                !is_null($purchaseLine->getProduct()) &&
                !is_null($purchaseLine->getSize()) &&
                !is_null($purchaseLine->getTint())

            ) {
                if (is_null($purchaseLine->getQuantity())) {
                    $purchaseLine->setQuantity(1);
                }

                $cartService->add($purchaseLine);
                $purchase = $cartService->getPurchase();
                if ($purchase->getDeliveryFees()) {
                    $flashBagInterface->add('error', 'Your delivery service has been deleted because you modified your cart');
                    return $this->redirectToRoute('cart_transport', ['edit' => true]);
                }
                return $this->redirectToRoute('cart_index');
            }
        }

        return $this->render('product/add2.html.twig', [
            'controller_name' => 'ProductController',
            'product' => $product,
            'purchaseLine' => $purchaseLine,
            'form' => $form->createView(),
            'tints' => $tints
        ]);
        // return $this->render('product/add.html.twig', [
        //     'controller_name' => 'ProductController',
        //     'product' => $product,
        //     'purchaseLine' => $purchaseLine,
        //     'form' => $form->createView(),
        //     'tints' => $tints
        // ]);
    }
}
