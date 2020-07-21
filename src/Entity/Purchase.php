<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass="App\Repository\PurchaseRepository")
 */
class Purchase
{
   
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    

    /**
     * @ORM\Column(type="datetime")
     */
    private $purchaseDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PurchaseLine", mappedBy="purchase", cascade={"persist", "remove"}, fetch="EAGER")
     */
    private $purchaseLines;

    /**
     * @ORM\Column(type="boolean")
     */
    private $paid  = false;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="purchases")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Status", inversedBy="purchases")
     * @ORM\JoinColumn(nullable=true)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\DeliveryFees")
     * @ORM\JoinColumn(nullable=true)
     */
    private $deliveryFees;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Address", inversedBy="purchases")
     * @ORM\JoinColumn(nullable=true)
     * 
     */
    private $address;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $deliveryPrice;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxDays;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $totalPurchaseLines;

   

    public function __toString()
    {
        return 'id : ' . $this->id . '..... date : ' . $this->purchaseDate->format('Y-m-d H:i:s');
    }
           

    public function __construct()
    {
        $this->purchaseLines = new ArrayCollection();
        $this->setPurchaseDate(new \DateTime('now'));

    }
         

    public function getId(): ?int
    {
        return $this->id;
    }

   

    public function getPurchaseDate(): ?\DateTimeInterface
    {
        return $this->purchaseDate;
    }

    public function setPurchaseDate(\DateTimeInterface $purchaseDate): self
    {
        $this->purchaseDate = $purchaseDate;

        return $this;
    }

    /**
     * @return Collection|PurchaseLine[]
     */
    public function getPurchaseLines(): Collection
    {
        return $this->purchaseLines;
    }

    public function addPurchaseLine(PurchaseLine $purchaseLine): self
    {

        foreach ($this->purchaseLines as $sisterLine) {
            if ($purchaseLine->getProduct()->getId() === $sisterLine->getProduct()->getId()
                && $purchaseLine->getSize()->getId() === $sisterLine->getSize()->getId()
                && $purchaseLine->getTint()->getId() === $sisterLine->getTint()->getId()) {
                $quantity = $sisterLine->getQuantity() + $purchaseLine->getQuantity();
                $sisterLine->setQuantity($quantity);
                return $this;
            }
        }

        $this->purchaseLines[] = $purchaseLine;
        $purchaseLine->setPurchase($this);



        return $this;
    }

    public function removePurchaseLine(PurchaseLine $purchaseLine): self
    {
        if ($this->purchaseLines->contains($purchaseLine)) {
            $this->purchaseLines->removeElement($purchaseLine);
            // set the owning side to null (unless already changed)
            if ($purchaseLine->getPurchase() === $this) {
                $purchaseLine->setPurchase(null);
            }
        }

        return $this;
    }

    public function getPaid(): ?bool
    {
        return $this->paid;
    }

    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTotalPurchaseLines()
    {
        $total = 0;
        foreach($this->purchaseLines as $item){
            $total += $item->getPrice();
        }

        return $total;
    }

    public function getTotal()
    {
        return $this->getTotalPurchaseLines() + $this->deliveryFees;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getDeliveryFees(): ?DeliveryFees
    {
        return $this->deliveryFees;
    }

    public function setDeliveryFees(?DeliveryFees $deliveryFees): self
    {
        $this->deliveryFees = $deliveryFees;

        return $this;
    }

    public function getAmountDeliveryFees()
    {
        $total = 0;
        if($this->deliveryFees->getFreeForMoreThan()){
            if($this->getTotalPurchaseLines() > $this->deliveryFees->getFreeForMoreThan()){
                return $total;
            }
        }
        
        if($this->deliveryFees->getVariableAmount()){
            $total += $this->getTotalPurchaseLines() * $this->deliveryFees->getVariableAmount();
        }

        if($this->deliveryFees->getFixedAmount()){
            $total += $this->deliveryFees->getFixedAmount();
        }
            return $total;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getDeliveryPrice(): ?int
    {
        return $this->deliveryPrice;
    }

    public function setDeliveryPrice(?int $deliveryPrice): self
    {
        $this->deliveryPrice = $deliveryPrice;

        return $this;
    }

    public function getMaxDays(): ?int
    {
        return $this->maxDays;
    }

    public function setMaxDays(?int $maxDays): self
    {
        $this->maxDays = $maxDays;

        return $this;
    }

    public function setTotalPurchaseLines(?int $totalPurchaseLines): self
    {
        $this->totalPurchaseLines = $totalPurchaseLines;

        return $this;
    }

    
}
