<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Image::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Image')
            ->setEntityLabelInPlural('Image')
            ->setSearchFields(['id', 'name']);
    }

    public function configureFields(string $pageName): iterable
    {
        $imageFile = Field::new('imageFile');
        $product = AssociationField::new('product');
        $tint = AssociationField::new('tint');
        $id = IntegerField::new('id', 'ID');
        $name = TextField::new('name');
        $updatedAt = DateTimeField::new('updated_at');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $updatedAt, $product, $tint];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $updatedAt, $product, $tint];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$imageFile, $product, $tint];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$imageFile, $product, $tint];
        }
    }
}
