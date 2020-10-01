<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryEmbeddableType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Category')
            ->setEntityLabelInPlural('Categories')
            ->setSearchFields(['name']);
    }

    public function configureFields(string $pageName): iterable
    {
        $panel1 = FormField::addPanel();
        $name = TextField::new('name');
        $parent = AssociationField::new('parent');
        $panel2 = FormField::addPanel('Categories');
        $categories = CollectionField::new('categories')
            ->allowAdd()
            ->allowDelete()
            ->setEntryType(CategoryEmbeddableType::class);
        $id = IntegerField::new('id', 'ID');
        $hasParent = BooleanField::new('hasParent');
        $theme = IntegerField::new('theme');
        $products = AssociationField::new('products');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $hasParent, $parent, $categories, $products];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $hasParent, $theme, $parent, $categories, $products];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $name, $parent, $panel2, $categories];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel1, $name, $parent, $panel2, $categories];
        }
    }
}
