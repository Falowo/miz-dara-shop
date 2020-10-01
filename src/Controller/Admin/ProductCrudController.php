<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ImageEmbeddableType;
use App\Form\StockEmbeddableType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AvatarField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;

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
        $mainImageFile = ImageField::new('mainImageFile');
        $images = CollectionField::new('images')
            ->setTemplatePath('admin/field/images.html.twig')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(ImageEmbeddableType::class);
        $panel3 = FormField::addPanel('Stocks');
        $stocks = CollectionField::new('stocks')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(StockEmbeddableType::class);
        $mainImage = AvatarField::new('mainImage')->setTemplatePath('admin/field/mainImage.html.twig');
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
            return [$panel1, $name, $info, $tags, $price, $discountPrice, $categories, $panel2, $mainImage, $mainImageFile, $images, $panel3, $stocks];
        }
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // ...
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_EDIT, Action::SAVE_AND_ADD_ANOTHER);
    }

    // public function createEntity(string $entityFqcn)
    // {
    //     $product = new Product();
    //     $product->????();

    //     return $product;
    // }
}
