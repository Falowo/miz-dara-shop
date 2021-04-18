<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 */
class Category
{
    const ORDERBY = [
        'last products',
        'discounts',
        'crescent price',
        'decrescent price'
    ];

    /**
     * Undocumented variable
     *
     * @var string
     */
    private $orderby;

    /**
     * Undocumented variable
     *
     * @var ?string
     */
    private $selectedSize;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Category", inversedBy="categories", cascade={"persist"})
     */
    private $parent;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Category", mappedBy="parent", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $categories;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hasParent;



    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $theme;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Product", mappedBy="categories")
     */
    private $products;

    /**
     * @ORM\ManyToMany(targetEntity=Size::class)
     */
    private $sizes;

   

   

    public function __construct()
    {

        $this->categories = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->sizes = new ArrayCollection();
       
    }



    public function __toString()
    {
        $string = $this->name;
        $parent = $this->getParent();
        while (!is_null($parent)) {
            $string .= ' / ' . $parent->getName();
            $parent = $parent->getParent();
        }

        return $string;
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

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }




    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;
        $this->setHasParent();

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(self $category): self
    {
        if (!$this->categories->contains($category)) {

            $this->categories[] = $category;
            $category->setParent($this);
        }

        return $this;
    }

    public function removeCategory(self $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);

            // set the owning side to null (unless already changed)
            if ($category->getParent() === $this) {
                $category->setParent(null);
            }
        }

        return $this;
    }

    public function getHasParent(): ?bool
    {

        return $this->hasParent;
    }

    public function setHasParent(): self
    {
        if ($this->parent) {
            $this->hasParent = true;
        } else {
            $this->hasParent = false;
        }
        return $this;
    }



    public function getTheme(): ?int
    {
        return $this->theme;
    }

    public function setTheme(?int $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
            $product->addCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
            $product->removeCategory($this);
        }

        return $this;
    }


    /**
     * Get undocumented variable
     *
     * @return  string
     */
    public function getOrderby()
    {
        return $this->orderby;
    }

    /**
     * Set undocumented variable
     *
     * @param  string  $orderby  Undocumented variable
     *
     * @return  self
     */
    public function setOrderby(string $orderby)
    {
        $this->orderby = $orderby;

        return $this;
    }

   

    /**
     * Get the value of selectedSize
     */ 
    public function getSelectedSize():?string
    {
        return $this->selectedSize;
    }

    /**
     * Set the value of selectedSize
     *
     * @return  self
     */ 
    public function setSelectedSize(string $selectedSize):self
    {
        $this->selectedSize = $selectedSize;

        return $this;
    }

    /**
     * @return Collection|Size[]
     */
    public function getSizes(): Collection
    {
        return $this->sizes;
    }

    public function addSize(Size $size): self
    {
        if (!$this->sizes->contains($size)) {
            $this->sizes[] = $size;
        }

        return $this;
    }

    public function removeSize(Size $size): self
    {
        $this->sizes->removeElement($size);

        return $this;
    }
     
}
