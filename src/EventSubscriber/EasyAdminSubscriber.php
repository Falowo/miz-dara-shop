<?php

namespace App\EventSubscriber;

use App\Entity\Category;
use App\Entity\DeliveryFees;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Entity\Stock;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Event\AfterEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityDeletedEvent;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityUpdatedEvent;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;



class EasyAdminSubscriber implements EventSubscriberInterface
{

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var UploaderHelper
     */
    private $uploaderHelper;

    /**
     * @var FlashBagInterface
     */
    private $flashBagInterface;

    /**
     * @var [type]
     */
    private $em;

    /**
     * @var StockRepository
     */
    private $stockRepository;

    public function __construct(

        CacheManager $cacheManager,
        UploaderHelper $uploaderHelper,
        FlashBagInterface $flashBagInterface,
        EntityManagerInterface $em,
        StockRepository $stockRepository

    ) {

        $this->cacheManager = $cacheManager;
        $this->uploaderHelper = $uploaderHelper;
        $this->flashBagInterface = $flashBagInterface;
        $this->em = $em;
        $this->stockRepository = $stockRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            BeforeEntityPersistedEvent::class => ['beforePersist'],
            BeforeEntityUpdatedEvent::class => ['beforeUpdate'],
            BeforeEntityDeletedEvent::class => ['beforeDelete'],
            AfterEntityPersistedEvent::class => ['afterPersist']

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

        $this->makeSureOnlyOneLocalFieldIsCompletedInDeliveryFees($entity);



        return;
    }

    public function afterPersist(AfterEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();
        $this->makeSureOneLocalFieldIsCompletedInDeliveryFees($entity);
        $this->avoidCreatePurchase($entity);
        $this->avoidUncompleteStock($entity);
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
        $this->avoidDuplicatedStock($entity);

        $this->makeSureOnlyOneLocalFieldIsCompletedInDeliveryFees($entity);

        $this->setCategoriesForRelatedProducts($entity);


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

            if ($entity->getParent() === $entity) {
                $entity->setParent(null);
            }
            $entity
                ->setHasParent();

            if ($entity->getHasParent()) {
                $entity->getParent()->addCategory($entity);
            }

            if (count($entity->getCategories()) > 0) {
                foreach ($entity->getCategories() as $category) {
                    if ($entity === $category) {
                        $entity->removeCategory($category);
                    }
                    $entity->addCategory($category);
                    $category
                        ->setParent($entity)
                        ->setHasParent();
                }
            }
        }
    }


    private function setProductForImages($entity)
    {
        if ($entity instanceof Product) {
            foreach ($entity->getImages() as $image) {
                $image->setProduct($entity);
                if (!($image->getName()) && !($image->getImageFile())) {
                    $entity->removeImage($image);
                }
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
        if ($entity instanceof Product) {

            foreach ($entity->getCategories() as $category) {

                while ($category->getparent()) {
                    $entity->addCategory($category->getParent());
                    $category = $category->getParent();
                }
            }
        }
    }
    private function setCategoriesForRelatedProducts($entity)
    {
        if ($entity instanceof Category) {


            if ((count($entity->getProducts()) > 0)) {
                $this->flashBagInterface->add('warning', 'Warning ! You may have to edit the categories for the related products!');
                foreach($entity->getProducts() as $product){
                    $this->setCategoriesForProduct($product);
                }
            }
        }
    }

    public function setHasStockForProduct($entity)
    {
        if ($entity instanceof Product) {
            $entity->setHasStock(null);
        }

        if ($entity instanceof Stock) {
            if ($product = $entity->getProduct()) {
                $product->setHasStock(null);
            }
        }
    }
    private function makeSureOneLocalFieldIsCompletedInDeliveryFees($entity)
    {
        if ($entity instanceof DeliveryFees) {
            if (!($entity->getNgCity() || $entity->getNgState() || $entity->getCountry() || $entity->getContinent())) {
                $this->flashBagInterface->add('danger', 'You need to complete one of the Locale Fields');

                $this->em->remove($entity);
                $this->em->flush();
            }
        }
    }

    private function makeSureOnlyOneLocalFieldIsCompletedInDeliveryFees($entity)
    {

        if ($entity instanceof DeliveryFees) {

            if (!($entity->getNgCity() xor $entity->getNgState() xor $entity->getCountry() xor $entity->getContinent())) {
                $this->flashBagInterface->add('danger', 'You need to complete ONLY one of the Locale Fields, only the more precise field will be retained');
                if ($entity->getNgCity()) {
                    $entity->setNgState(null);
                    $entity->setCountry(null);
                    $entity->setContinent(null);
                } elseif ($entity->getNgState()) {
                    $entity->setCountry(null);
                    $entity->setContinent(null);
                } elseif ($entity->getCountry()) {
                    $entity->setContinent(null);
                }
            }
        }
    }

    private function avoidCreatePurchase($entity)
    {
        if ($entity instanceof Purchase) {
            if (!($entity->getUser() && $entity->getaddress())) {
                $this->flashBagInterface->add('danger', 'You cannot create purchase from admin');

                $this->em->remove($entity);
                $this->em->flush();
            }
        }
    }

    private function avoidDuplicatedStock($entity)
    {
        if ($entity instanceof Product) {
            if ($entity->getStocks()) {
                $pairSizeTints = [];
                foreach ($entity->getStocks() as $stock) {
                    $pairSizeTint = $stock->getSize() . $stock->getTint();
                    $k = array_search($pairSizeTint, $pairSizeTints);
                    if ($k === false) {
                        $pairSizeTints[] = $pairSizeTint;
                    } else {
                        $this->flashBagInterface->add('danger', 'The stock for size ' . $stock->getSize() . ' and color ' . $stock->getTint() . ' already exists, edit it !');
                        $entity->removeStock($stock);
                    }
                }
            }
        }
    }
    private function avoidUncompleteStock($entity)
    {
        if ($entity instanceof Stock) {
            if (!($entity->getProduct() && $entity->getTint() && $entity->getSize() && $entity->getQuantity())) {

                $this->flashBagInterface->add('danger', 'You need to complete all entities even as Unique or else (prepare your sizes and colors before) or 0 for the quantity');
                $this->em->remove($entity);
                $this->em->flush();
            }
        }
    }
}
