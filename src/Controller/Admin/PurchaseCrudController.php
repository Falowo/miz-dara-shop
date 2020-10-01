<?php

namespace App\Controller\Admin;

use App\Entity\Purchase;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class PurchaseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Purchase::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Purchase')
            ->setEntityLabelInPlural('Purchases')
            ->setSearchFields(['id', 'deliveryPrice', 'maxDays', 'totalPurchaseLines']);
    }

    public function configureFields(string $pageName): iterable
    {
        $paid = BooleanField::new('paid');
        $status = AssociationField::new('status');
        $id = IntegerField::new('id', 'ID');
        $purchaseDate = DateTimeField::new('purchaseDate');
        $deliveryFees = AssociationField::new('deliveryFees');
        $purchaseLines = AssociationField::new('purchaseLines')->setTemplatePath('easy_admin/purchaseLines.html.twig');
        $user = AssociationField::new('user')->setTemplatePath('easy_admin/user.html.twig');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $purchaseDate, $status, $paid];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $purchaseDate, $deliveryFees, $paid, $status, $purchaseLines, $user];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$paid, $status];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$paid, $status];
        }
    }
}
