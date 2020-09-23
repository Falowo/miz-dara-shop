<?php

namespace App\Controller\Admin;

use App\Entity\Transport;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class TransportCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Transport::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Transport')
            ->setEntityLabelInPlural('Transport')
            ->setSearchFields(['id', 'name', 'speed', 'defaultAmountByKm', 'maxDaysByKm']);
    }

    public function configureFields(string $pageName): iterable
    {
        $name = TextField::new('name');
        $speed = TextField::new('speed');
        $maxDaysByKm = NumberField::new('maxDaysByKm');
        $defaultAmountByKm = IntegerField::new('defaultAmountByKm');
        $id = IntegerField::new('id', 'ID');
        $deliveryFeess = AssociationField::new('deliveryFeess');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $speed, $defaultAmountByKm, $maxDaysByKm, $deliveryFeess];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $speed, $defaultAmountByKm, $maxDaysByKm, $deliveryFeess];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$name, $speed, $maxDaysByKm, $defaultAmountByKm];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $speed, $maxDaysByKm, $defaultAmountByKm];
        }
    }
}
