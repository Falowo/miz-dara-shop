<?php

namespace App\EventSubscriber;

use App\Entity\Category;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Stock;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityBuildEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    /**
     * Undocumented variable
     *
     * @var EntityManagerInterface
     */
    private $em;

    private $session;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     *
     * @var ProductRepository
     */
    private $productRepository;

    public function __construct(
        EntityManagerInterface $em,
        CacheManager $cacheManager,
        UploaderHelper $uploaderHelper,
        SessionInterface $session,
        ProductRepository $productRepository
    ) {
        $this->em = $em;
        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
        $this->session = $session;
        $this->productRepository = $productRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforePersist'],
            BeforeEntityUpdatedEvent::class => ['beforeUpdate'],
            BeforeEntityDeletedEvent::class => ['beforeDelete'],

        ];
    }



    public function beforePersist(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        $this->setCategory($entity);       

        $this->setProductForImages($entity);
        $this->setProductForStocks($entity);
        $this->setCategoriesForProduct($entity);
        $this->setHasStockForProduct($entity);


        return;
    }



    public function beforeUpdate(BeforeEntityUpdatedEvent $event)
    {
        $entity = $event->getEntityInstance();

        $this->removeCache($entity);

        $this->setCategory($entity);

        $this->setProductForImages($entity);
        $this->setProductForStocks($entity);
        $this->setCategoriesForProduct($entity);
        $this->setHasStockForProduct($entity);


        return;
    }

    public function beforeDelete(BeforeEntityDeletedEvent $event)
    {
        $entity = $event->getEntityInstance();

        $this->removeCache($entity);
        $this->setHasStockForProduct($entity);
        return;
    }

    private function removeCache($entity)
    {
        if ($entity instanceof Product) {
            if ($entity->getMainImageFile() instanceof UploadedFile) {
                $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'mainImageFile'));
            }
        }
        if ($entity instanceof Image) {
            if ($entity->getImageFile() instanceof UploadedFile) {
                $this->cacheManager->remove($this->uploaderHelper->asset($entity, 'imageFile'));
            }
        }
    }

    private function setCategory($entity)
    {

        if ($entity instanceof Category) {

            $entity
                ->setHasParent();

            if ($entity->getHasParent()) {
                $entity->getParent()->addCategory($entity);
            }

            if (count($entity->getCategories()) > 0) {
                foreach ($entity->getCategories() as $category) {
                    $entity->addCategory($category);
                    $category
                    ->setParent($entity)
                    ->setHasParent()
                    ;
                    
                }
            }
        }
    }

    // private function storeProductInSession($entity)
    // {
    //     if ($entity instanceof Product) {
    //         $this->session->set('lastProduct', $entity->getId());
    //     }
    // }

    // private function getStoredProductInSession()
    // {
    //     $productId = $this->session->get('lastProduct');
    //     return $this->productRepository->find($productId);
    // }

    private function setProductForImages($entity)
    {
        if ($entity instanceof Product) {
            foreach ($entity->getImages() as $image) {
                $image->setProduct($entity);
            }
        }
    }
    private function setProductForStocks($entity)
    {
        if ($entity instanceof Product) {
            foreach ($entity->getStocks() as $stock) {
                $stock->setProduct($entity);
            }
        }
    }

    private function setCategoriesForProduct($entity)
    {
        if($entity instanceof Product){
            foreach($entity->getCategories() as $category){
                while($category->getparent())
                {
                    $entity->addCategory($category->getParent());
                    $category = $category->getParent();
                }
            }
        }
    }

    public function setHasStockForProduct($entity)
    {
        if ($entity instanceof Product){
            $entity->setHasStock(null);
        }

        if ($entity instanceof Stock){
            if ($product = $entity->getProduct()){
                $product->setHasStock(null);
            }
        }
    }
}
