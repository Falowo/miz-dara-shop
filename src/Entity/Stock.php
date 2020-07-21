<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StockRepository")
 * @UniqueEntity(fields={"product", "size", "tint"})
 * @ORM\Embeddable
 */
class Stock
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="stocks")
     * @ORM\JoinColumn(nullable=true)
     * @Assert\NotBlank()
     */
    private $product;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Size")
     * @ORM\JoinColumn(nullable=false)
     */
    private $size;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     */
    private $quantity;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tint")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tint;

    public function __toString()
    {
        return 'size : ' . $this->size . ' ..... Color : ' . $this->tint . ' ..... Quantity : ' . $this->quantity;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
       
        return $this;
    }


    public function getSize(): ?Size
    {
        return $this->size;
    }

    public function setSize(?Size $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }


    public function getTint(): ?Tint
    {
        return $this->tint;
    }

    public function setTint(?Tint $tint): self
    {
        $this->tint = $tint;

        return $this;
    }


}
