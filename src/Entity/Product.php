<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;




/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @Vich\Uploadable()
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=70)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $info;



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Stock", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\Embedded(class="Stock")
     */
    private $stocks;

    /**
     * @ORM\Embedded(class="Image")
     * @ORM\OneToMany(targetEntity="App\Entity\Image", mappedBy="product", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $images;



    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $discountPrice;


    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasStock = false;


    /**
     *
     * @ORM\Column(type="boolean")
     */
    private $hasMainImage = false;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Tag")
     */
    private $tags;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="products")
     */
    private $categories;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $mainImage;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="product_mainImage", fileNameProperty="mainImage")
     * @Assert\Image(mimeTypes="image/jpeg")
     */
    private $mainImageFile;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;



    public function __construct()
    {

        $this->stocks = new ArrayCollection();
        $this->images = new ArrayCollection();


        if (!isset($this->creationDate)) {
            $this->creationDate = new \DateTime('now');
        }
        $this->tags = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->id . ' ' . $this->name;
    }

    public function getSlug()
    {
        return (new AsciiSlugger())->slug($this->name);
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getInfo(): ?string
    {
        return $this->info;
    }

    public function setInfo(string $info): self
    {
        $this->info = $info;

        return $this;
    }



    public function getSizesByTint(Tint $tint)
    {

        $sizesByTint = [];
        foreach ($this->stocks as $stock) {
            if ($stock->getTint() === $tint) {
                $sizesByTint[] = $stock->getSize();
            }
        }
        $sizesByTint = array_unique($sizesByTint);

        return $sizesByTint;
    }

    /**
     *
     * @return Size[]
     */
    public function getSizes(): array
    {
        $sizes = [];
        /**@var Stock $stock */
        foreach ($this->stocks as $stock) {
            if ($stock->getQuantity() > 0) {
                $sizes[] = $stock->getSize();
            }
        }
        $tints = array_unique($sizes);


        return $sizes;
    }



    /**
     * @param Size $size
     * @return Tint[]
     */
    public function getTintsBySize(Size $size): array
    {

        $tintsBySize = [];
        foreach ($this->stocks as $stock) {
            if ($stock->getSize() === $size) {
                $tintsBySize[] = $stock->getTint();
            }
        }

        $tintsBySize = array_unique($tintsBySize);

        return $tintsBySize;
    }


    /**
     * @return Tint[]
     */
    public function getTints(): array
    {
        $tints = [];
        /**@var Stock $stock */
        foreach ($this->stocks as $stock) {
            if ($stock->getQuantity() > 0) {
                $tints[] = $stock->getTint();
            }
        }
        $tints = array_unique($tints);

        return $tints;
    }



    /**
     * @return Collection|Stock[]
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    /**
     * @param Size $size
     * @param Tint $tint
     * @return int
     */
    public function getStock(Size $size, Tint $tint): int
    {
        foreach ($this->stocks as $stock) {
            if ($stock->getTint() === $tint && $stock->getSize() === $size) {
                return $stock->getQuantity();
            }
        }         
                
    }

    public function addStock(Stock $stock): self
    {
        foreach ($this->getStocks() as $s) {
            if ($s->getSize() === $stock->getSize() && $s->getTint() === $stock->getTint()) {
                return $this;
            }
        }

        $this->stocks[] = $stock;

        $stock->setProduct($this);


        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->contains($stock)) {
            $this->stocks->removeElement($stock);
            // set the owning side to null (unless already changed)
            if ($stock->getProduct() === $this) {
                $this->setHasStock(null);
                $stock->setProduct(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if ($image->getImageFile() instanceof  UploadedFile) {

            if (!$this->images->contains($image)) {
                $this->images[] = $image;
                $image->setProduct($this);
            }
        } 
       
        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {

            $this->images->removeElement($image);
            // set the owning side to null (unless already changed)
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }


    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getFormatedPrice(): string
    {
        return number_format($this->price, 0, '', ',');
    }

    public function getDiscountPrice(): ?int
    {
        return $this->discountPrice;
    }

    public function setDiscountPrice(?int $discountPrice): self
    {
        $this->discountPrice = $discountPrice;

        return $this;
    }

    public function getFormatedDiscountPrice(): string
    {
        return number_format($this->discountPrice, 0, '', ',');
    }


    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getHasStock(): ?bool
    {
        return $this->hasStock;
    }

    public function setHasStock(?bool $hasStock): self
    {
        if (!is_null($hasStock)) {
            $this->hasStock = $hasStock;
            return $this;
        }

        if (count($this->stocks) > 0) {

            foreach ($this->stocks as $stock) {
                if ($stock->getQuantity() > 0) {
                    $this->hasStock = true;

                    return $this;
                } else {
                    $this->hasStock = false;
                }
            }
        } else {
            $this->hasStock = false;
        }



        return $this;
    }



    /**
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        if ($this->tags->contains($tag)) {
            $this->tags->removeElement($tag);
        }

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addProduct($this);
        }
        while ($category->getParent()) {
            $category = $category->getParent();
            if (!$this->categories->contains($category)) {
                $this->categories[] = $category;
                $category->addProduct($this);
            }
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function getMainImage(): ?string
    {
        return $this->mainImage;
    }

    public function setMainImage(?string $mainImage): self
    {
        $this->mainImage = $mainImage;
        if (!is_null($this->mainImage)) {
            $this->hasMainImage = true;
        } else {
            $this->hasMainImage = false;
        }

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(): self
    {
        $this->updated_at = new \DateTime('now');

        return $this;
    }

    /**
     * @return File|null
     */
    public function getMainImageFile(): ?File
    {
        return $this->mainImageFile;
    }

    /**
     * @param null|File $imageFile
     * @return Image
     */
    public function setMainImageFile(?File $mainImageFile): Product
    {
        $this->mainImageFile = $mainImageFile;
        if ($this->mainImageFile instanceof UploadedFile) {
            $this->updated_at = new \DateTime('now');
        }

        return $this;
    }

    /**
     * Undocumented function
     *
     * @return boolean
     */
    public function hasMainImage()
    {
        return $this->hasMainImage;
    }
}
