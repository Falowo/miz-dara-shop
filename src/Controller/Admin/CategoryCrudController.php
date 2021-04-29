<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryEmbeddableType;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
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
            ->setSearchFields(['name', 'parent']);
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
            ->setEntryType(CategoryEmbeddableType::class)
            ->setTemplatePath('admin/field/categories.html.twig')
            ;
        $id = IntegerField::new('id', 'ID');
        $hasParent = BooleanField::new('hasParent');
        $theme = IntegerField::new('theme');
        $products = AssociationField::new('products');

        if (Crud::PAGE_INDEX === $pageName) {
            return [$id, $name, $parent, $products];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            return [$id, $name, $hasParent, $parent, $categories, $products, $theme];
        } elseif (Crud::PAGE_NEW === $pageName) {
            return [$panel1, $name, $parent, $panel2, $categories];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            return [$panel1, $name, $parent, $panel2, $categories];
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
