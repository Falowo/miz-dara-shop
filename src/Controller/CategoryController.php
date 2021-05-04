<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\SearchBarType;
use App\Service\Filter\FilterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/{id}/{slug}", requirements={"id": "\d+"}, name="category")
     * @param Category $category
     * @param $id
     * @return Response
     */
    public function index(Category $category, Request $request, FilterService $filterService, $id)
    {
        $order=['p.id', 'DESC'];

          if(!$category->getSizes()>0){
              $filterService->setNgetAllSizesByCategory($category);
            }  
        
        $form = $this->createForm(SearchBarType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $order = $category->getOrderby();
        }
          $products = $filterService->paginate($id, $order, $request);

        if (!is_null($category->getParent())) {
            $parent = $category->getParent();
        } else {
            $parent = $category;
        }

        $ancestors = [];

        for ($i = 0; $i <= 16; $i++) {
            if ($i === 0) {
                if ($ancestors[$i] = $category->getParent()) {
                    while (!is_null($ancestors[$i]->getParent())) {
                        $ancestors[$i] = $ancestors[$i]->getParent();
                    }
                } else {
                    $ancestors[$i] = null;
                }
            } else {
                if ($ancestors[$i - 1] && $category->getParent() !== $ancestors[$i - 1]) {
                    $ancestors[$i] = $category->getParent();
                    while ($ancestors[$i]->getParent() !== $ancestors[$i - 1]) {
                        $ancestors[$i] = $ancestors[$i]->getParent();
                    }
                } else {
                    $ancestors[$i] = null;
                }
            }
        }


        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'products' => $products,
            'category' => $category,
            'parent' => $parent,
            'ancestors' => $ancestors,
            'form' => $form->createView(),
        ]);
    }
}
