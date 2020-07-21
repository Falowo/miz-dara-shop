<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    public function index(Category $category, ProductRepository $repository, PaginatorInterface $paginator, Request $request, EntityManagerInterface $em, $id)
    {

        $selectedCategory = $category;

        $products = $paginator->paginate(
            $repository->findAllByCategoryQuery($id), /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            12 /*limit per page*/
        );

        


        if (!is_null($category->getParent())) {

            while (!is_null($category->getParent()->getParent())) {

                $category = $category->getParent();
            }
        }


        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'products' => $products,
            'selectedCategory'=>$selectedCategory,
            'category'=>$category
        ]);
    }
}
