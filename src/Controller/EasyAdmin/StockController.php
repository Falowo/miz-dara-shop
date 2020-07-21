<?php

namespace App\Controller\EasyAdmin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController;

class StockController extends EasyAdminController
{
    protected function persistEntity($entity)
    {
        if($product = $entity->getProduct()){

            $product->addStock($entity);
            $product->setHasStock(null);
        }
        parent::persistEntity($entity);
    }


    public function updateEntity($entity)
    {
        if (method_exists($entity, 'setHasStock')) {
            $entity->getProduct()->setHasStock(null);
        }

        parent::updateEntity($entity);
    }

    public function removeEntity($entity)
    {
        if ($product = $entity->getProduct()) {
            $product->removeStock($entity);
        }

        parent::removeEntity($entity);
        
    }
}
