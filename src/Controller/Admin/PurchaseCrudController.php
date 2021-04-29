<?php

namespace App\Controller\Admin;

use App\Entity\Purchase;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
            ->setSearchFields(['id', 'user', 'deliveryPrice', 'maxDays', 'totalPurchaseLines']);
    }

    public function configureFields(string $pageName): iterable
    {
        $paid = BooleanField::new('paid');
        $status = AssociationField::new('status');
        $id = IntegerField::new('id', 'ID');
        $purchaseDate = DateTimeField::new('purchaseDate');
        $deliveryFees = AssociationField::new('deliveryFees');
        $purchaseLines = AssociationField::new('purchaseLines')
            ->setTemplatePath('admin/field/purchase_lines.html.twig');
        $user = AssociationField::new('user')
            ->setTemplatePath('admin/field/user.html.twig');
        $address = AssociationField::new('address')
            ->setTemplatePath('admin/field/address.html.twig');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $user, $purchaseDate, $status, $paid];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $user, $purchaseDate, $deliveryFees, $paid, $status, $address, $purchaseLines];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$paid, $status];
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER);
    }
}
