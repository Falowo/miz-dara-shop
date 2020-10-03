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
        $maxDays = IntegerField::new('maxDays')->setHelp('Overrides the field daysByKm in Transport Services');
        $ngCity = AssociationField::new('ngCity')->setHelp('ONE and only ONE field ngCity OR ngState OR country OR continent must be completed');
        $ngState = AssociationField::new('ngState')->setHelp('ONE and only ONE field ngCity OR ngState OR country OR continent must be completed');
        $country = AssociationField::new('country')->setHelp('ONE and only ONE field ngCity OR ngState OR country OR continent must be completed');
        $continent = AssociationField::new('continent')->setHelp('ONE and only ONE field ngCity OR ngState OR country OR continent must be completed');
        $fixedAmount = IntegerField::new('fixedAmount')->setHelp('in Naira');
        $amountByKm = IntegerField::new('amountByKm')->setHelp('in Naira');
        $percentOfRawPrice = IntegerField::new('percentOfRawPrice')->setHelp('eg: for 20% write 20');
        $freeForMoreThan = IntegerField::new('freeForMoreThan')->setHelp('in Naira');
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
