<?php

namespace App\Controller\Admin;

use App\Admin\Field\VichImageField;
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
            ->setSearchFields(['id', 'name', 'info', 'price', 'discountPrice', 'mainImage', 'lowStock']);
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
        $panel2 = FormField::addPanel('stocks');
        $mainImageFile = VichImageField::new('mainImageFile');
        $imagesDetail = AvatarField::new('images')
            ->setTemplatePath('admin/field/images_detail.html.twig');            
        $images = CollectionField::new('images')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(ImageEmbeddableType::class)
            ;
        $panel3 = FormField::addPanel('images');
        $stocks = CollectionField::new('stocks')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(StockEmbeddableType::class)
            ->setTemplatePath('admin/field/stocks.html.twig')
            ;
            
        $mainImage = TextField::new('mainImage');
        $mainImageIndex = AvatarField::new('mainImage')
            ->setTemplatePath('admin/field/main_image_index.html.twig');
        $mainImageDetail = AvatarField::new('mainImage')
            ->setTemplatePath('admin/field/main_image_detail.html.twig');
        $id = IntegerField::new('id', 'ID');
        $creationDate = DateTimeField::new('creationDate');
        $hasStock = Field::new('hasStock');
        $lowStock = Field::new('lowStock');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $mainImageIndex, $hasStock, $lowStock];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [
                $name, 
                $creationDate, 
                $info, 
                $tags, 
                $price, 
                $discountPrice, 
                $categories, 
                $stocks, 
                $mainImageDetail, 
                $imagesDetail];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [
                $panel1, 
                $name, 
                $info,                 
                $tags, 
                $price, 
                $discountPrice, 
                $categories, 
                $panel2, 
                $stocks,
                $panel3, 
                $mainImageFile, 
                $images 
            ];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [
                $panel1, 
                $name, 
                $info, 
                $tags, 
                $price, 
                $discountPrice, 
                $categories, 
                $panel2, 
                $stocks,
                $panel3, 
                $mainImage, 
                $mainImageFile, 
                $images 
            ];
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
