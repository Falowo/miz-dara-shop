<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\SearchBarType;
use App\Repository\ProductRepository;
use App\Service\Filter\FilterService;
use Knp\Component\Pager\PaginatorInterface;
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
    public function index(Category $category, ProductRepository $repository, PaginatorInterface $paginator, Request $request, FilterService $filterService, $id)
    {

          if(!$category->getSizes()>0){
              $filterService->setNgetAllSizesByCategory($category);
            }  
        
        // $form = $this->createForm(SearchBarType::class, $category);

        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
          
        // }


        $products = $paginator->paginate(
            $repository->findAllByCategoryQuery($id), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );

        






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
            // 'form' => $form->createView(),
        ]);
    }
}
