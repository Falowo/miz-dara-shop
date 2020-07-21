<?php

namespace App\Controller\EasyAdmin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class CategoryController extends EasyAdminController
{
    protected function persistEntity($entity)
    {
        $entity
            ->setHasParent()
            ;
        
        if($entity->getHasParent()){
            $entity->getParent()->addCategory($entity);
        }

        if( count($entity->getCategories()) >0 ){
            foreach($entity->getCategories() as $category){
                $category->setParent($entity);
                
            }
        }

        
        parent::persistEntity($entity);
    }
}
