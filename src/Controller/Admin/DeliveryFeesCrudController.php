<?php

namespace App\Controller\Admin;

use App\Entity\DeliveryFees;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;

class DeliveryFeesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return DeliveryFees::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('DeliveryFees')
            ->setEntityLabelInPlural('DeliveryFees')
            ->setSearchFields(['id', 'fixedAmount', 'freeForMoreThan', 'maxDays', 'amountByKm', 'percentOfRawPrice']);
    }

    public function configureFields(string $pageName): iterable
    {
        $transport = AssociationField::new('transport');
        $maxDays = IntegerField::new('maxDays');
        $ngCity = AssociationField::new('ngCity');
        $ngState = AssociationField::new('ngState');
        $country = AssociationField::new('country');
        $continent = AssociationField::new('continent');
        $fixedAmount = IntegerField::new('fixedAmount');
        $amountByKm = IntegerField::new('amountByKm');
        $percentOfRawPrice = IntegerField::new('percentOfRawPrice');
        $freeForMoreThan = IntegerField::new('freeForMoreThan');
        $id = IntegerField::new('id', 'ID');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $transport, $ngCity, $ngState, $country, $continent];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $transport, $maxDays, $ngCity, $ngState, $country, $continent, $fixedAmount, $amountByKm, $percentOfRawPrice, $freeForMoreThan];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$transport, $maxDays, $ngCity, $ngState, $country, $continent, $fixedAmount, $amountByKm, $percentOfRawPrice, $freeForMoreThan];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$transport, $maxDays, $ngCity, $ngState, $country, $continent, $fixedAmount, $amountByKm, $percentOfRawPrice, $freeForMoreThan];
        }
    }
}
