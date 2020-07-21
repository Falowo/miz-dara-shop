<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * it is the delivery transport service
 * 
 * @ORM\Entity(repositoryClass="App\Repository\TransportRepository")
 */
class Transport
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $speed;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DeliveryFees", mappedBy="transport")
     */
    private $deliveryFeess;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $defaultAmountByKm;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $maxDaysByKm;

    public function __construct()
    {
        $this->deliveryFeess = new ArrayCollection();
    }

  

    public function __toString()
    {
        return $this->speed . ' : ' . $this->name;
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

    public function getSpeed(): ?string
    {
        return $this->speed;
    }

    public function setSpeed(string $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    /**
     * @return Collection|DeliveryFees[]
     */
    public function getDeliveryFeess(): Collection
    {
        return $this->deliveryFeess;
    }

    public function addDeliveryFeess(DeliveryFees $deliveryFeess): self
    {
        if (!$this->deliveryFeess->contains($deliveryFeess)) {
            $this->deliveryFeess[] = $deliveryFeess;
            $deliveryFeess->setTransport($this);
        }

        return $this;
    }

    public function removeDeliveryFeess(DeliveryFees $deliveryFeess): self
    {
        if ($this->deliveryFeess->contains($deliveryFeess)) {
            $this->deliveryFeess->removeElement($deliveryFeess);
            // set the owning side to null (unless already changed)
            if ($deliveryFeess->getTransport() === $this) {
                $deliveryFeess->setTransport(null);
            }
        }

        return $this;
    }

    public function getDefaultAmountByKm(): ?int
    {
        return $this->defaultAmountByKm;
    }

    public function setDefaultAmountByKm(?int $defaultAmountByKm): self
    {
        $this->defaultAmountByKm = $defaultAmountByKm;

        return $this;
    }

    public function getMaxDaysByKm(): ?float
    {
        return $this->maxDaysByKm;
    }

    public function setMaxDaysByKm(?float $maxDaysByKm): self
    {
        $this->maxDaysByKm = $maxDaysByKm;

        return $this;
    }

   
}
