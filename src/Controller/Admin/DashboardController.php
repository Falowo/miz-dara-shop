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
        return parent::index();
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
}
