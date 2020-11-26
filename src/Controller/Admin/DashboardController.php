<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\DeliveryFees;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\Size;
use App\Entity\Status;
use App\Entity\Stock;
use App\Entity\Tag;
use App\Entity\Tint;
use App\Entity\Transport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="dashboard_index")
     */
    public function index(): Response
    {
         

        

        return $this->render('admin/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle(' Miz Dara Shop');
    }



    public function configureCrud(): Crud
    {
        return Crud::new();
    }

    public function configureMenuItems(): iterable
    {
        $submenu1 = [
            MenuItem::linkToCrud('Size', 'fas fa-pen', Size::class),
            MenuItem::linkToCrud('Colors', 'fas fa-pen', Tint::class),
            MenuItem::linkToCrud('tags', 'fas fa-pen', Tag::class),
            MenuItem::linkToCrud('Categories', 'fas fa-pen', Category::class),
        ];

        $submenu2 = [
            MenuItem::linkToCrud('Images', 'fas fa-camera', Image::class),
            MenuItem::linkToCrud('Stocks', 'fas fa-pen', Stock::class),
            MenuItem::linkToCrud('Products', 'fas fa-sun', Product::class),
        ];

        $submenu3 = [
            MenuItem::linkToCrud('Transport services', 'fas fa-pen', Transport::class),
            MenuItem::linkToCrud('Delivery Fees', 'fas fa-pen', DeliveryFees::class),
            MenuItem::linkToCrud('Purchase Status Entries', 'fas fa-pen', Status::class),
            MenuItem::linkToCrud('Purchases', 'far fa-money-bill-alt', Purchase::class),
        ];

        yield MenuItem::subMenu('Product Attributs (set 1st)', 'fas fa-pen')->setSubItems($submenu1);
        yield MenuItem::subMenu('Products', 'fas fa-sun')->setSubItems($submenu2);
        yield MenuItem::subMenu('Purchases', 'far fa-money-bill-alt')->setSubItems($submenu3);
    }


    /**
     *
     * @return Response
     * @Route("/admin/category", name="categoryCC")
     */
    public function categoryCC(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(CategoryCrudController::class)->generateUrl());
    }


    /**
     *
     * @return Response
     * @Route("/admin/size", name="sizeCC")
     */
    public function sizeCC(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(SizeCrudController::class)->generateUrl());
    }

    /**
     *
     * @return Response
     * @Route("/admin/tint", name="tintCC")
     */
    public function tintCC(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(TintCrudController::class)->generateUrl());
    }
    /**
     *
     * @return Response
     * @Route("/admin/tag", name="tagCC")
     */
    public function tagCC(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(TagCrudController::class)->generateUrl());
    }

    

    /**
     *
     * @return Response
     * @Route("/admin/product", name="productCC")
     */
    public function productCC(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(ProductCrudController::class)->generateUrl());
    }

    /**
     *
     * @return Response
     * @Route("/admin/image", name="imageCC")
     */
    public function imageCC(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(ImageCrudController::class)->generateUrl());
    } 

    /**
     *
     * @return Response
     * @Route("/admin/stock", name="stockCC")
     */
    public function stockCC(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(StockCrudController::class)->generateUrl());
    }

    /**
     *
     * @return Response
     * @Route("/admin/deliveryFees", name="deliveryFeesCC")
     */
    public function deliveryFees(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(DeliveryFeesCrudController::class)->generateUrl());
    }

    /**
     *
     * @return Response
     * @Route("/admin/transport", name="transportCC")
     */
    public function transport(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(TransportCrudController::class)->generateUrl());
    }

    /**
     *
     * @return Response
     * @Route("/admin/status", name="statusCC")
     */
    public function status(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(StatusCrudController::class)->generateUrl());
    }

    /**
     *
     * @return Response
     * @Route("/admin/purchase", name="purchaseCC")
     */
    public function purchase(): Response
    {
        $routeBuilder = $this->get(CrudUrlGenerator::class)->build();
    
        return $this->redirect($routeBuilder->setController(PurchaseCrudController::class)->generateUrl());
    }


}

