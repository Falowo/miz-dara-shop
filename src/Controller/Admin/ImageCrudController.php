<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
        $imageFile = ImageField::new('imageFile');
        $product = AssociationField::new('product');
        $tint = AssociationField::new('tint');
        $id = IntegerField::new('id', 'ID');
        $name = TextField::new('name');
        
        $imageIndex = AvatarField::new('imageFile')
            ->setTemplatePath('admin/field/image_index.html.twig')
            ;
        $imageDetail = AvatarField::new('imageFile')
            ->setTemplatePath('admin/field/image_detail.html.twig');
        $updatedAt = DateTimeField::new('updated_at');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $imageIndex, $updatedAt, $product, $tint];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $updatedAt, $product, $tint, $imageDetail];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$imageFile, $product, $tint];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$name, $imageFile, $product, $tint];
        }
    }

    public function configureActions(Actions $actions): Actions
{
    return $actions
        // ...
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
    ;
}
}
