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
            ->setEntityLabelInPlural('Images')
            ->setSearchFields(['id', 'name']);
    }

    public function configureFields(string $pageName): iterable
    {
        $imageFile = ImageField::new('imageFile')
        // ->setBasePath($this->getParameter('app.path.product_images'))->onlyOnIndex()
        // ->setFormType(ImageType::class)
        ;
        $product = AssociationField::new('product');
        $tint = AssociationField::new('tint');
        $id = IntegerField::new('id', 'ID');
        $name = AvatarField::new('name')
        ->setTemplatePath('admin/field/image.html.twig');
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

    public function configureActions(Actions $actions): Actions
{
    return $actions
        // ...
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER)
    ;
}
}
