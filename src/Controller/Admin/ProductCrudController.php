<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Product')
            ->setEntityLabelInPlural('Product')
            ->setSearchFields(['id', 'name', 'info', 'price', 'discountPrice', 'mainImage']);
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel();
        $name = TextField::new('name');
        $info = TextField::new('info');
        $tags = AssociationField::new('tags');
        $price = IntegerField::new('price');
        $discountPrice = IntegerField::new('discountPrice');
        $categories = AssociationField::new('categories');
        $panel2 = FormField::addPanel('images');
        $mainImageFile = Field::new('mainImageFile');
        $images = AssociationField::new('images')->setTemplatePath('easy_admin/images.html.twig');
        $panel3 = FormField::addPanel('Stocks');
        $stocks = AssociationField::new('stocks');
        $mainImage = ImageField::new('mainImage');
        $id = IntegerField::new('id', 'ID');
        $creationDate = DateTimeField::new('creationDate');
        $hasStock = Field::new('hasStock');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $info, $price, $discountPrice, $creationDate, $hasStock];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$name, $info, $tags, $categories, $stocks, $mainImage, $images];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $name, $info, $tags, $price, $discountPrice, $categories, $panel2, $mainImageFile, $images, $panel3, $stocks];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel1, $name, $info, $tags, $price, $discountPrice, $categories, $panel2, $mainImageFile, $images, $panel3, $stocks];
        }
    }
}
