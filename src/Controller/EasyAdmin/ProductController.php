<?php

namespace App\Controller\EasyAdmin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class ProductController extends EasyAdminController
{
    protected function persistEntity($entity)
    {
        if ($entity->getCategories()) {
            foreach ($entity->getCategories() as $category) {
                if($category->getParent()){
                    $entity->addCategory($category->getParent());
                }
            }
        }
        $entity->setHasStock(null);
        parent::persistEntity($entity);
    }

    public function updateEntity($entity)
    {
       
            $entity->setHasStock(null);
            
        if ($entity->getCategories()) {
            foreach ($entity->getCategories() as $category) {
                if($category->getParent()){
                    $entity->addCategory($category->getParent());
                }
               
            }
        }
        parent::updateEntity($entity); 
    }

   

}
