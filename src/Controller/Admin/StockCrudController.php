<?php

namespace App\Controller\Admin;

use App\Entity\Stock;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class StockCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Stock::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Stock')
            ->setEntityLabelInPlural('Stock')
            ->setSearchFields(['id', 'quantity']);
    }

    public function configureFields(string $pageName): iterable
    {
        $product = AssociationField::new('product');
        $size = AssociationField::new('size');
        $tint = AssociationField::new('tint');
        $quantity = IntegerField::new('quantity');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $quantity, $product, $size, $tint];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $quantity, $product, $size, $tint];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$product, $size, $tint, $quantity];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$product, $size, $tint, $quantity];
        }
    }
}
