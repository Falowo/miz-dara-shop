<?php

namespace App\Entity;

use App\Service\Locale\LocaleService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NgCityRepository")
 */
class NgCity
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $jsonId;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DeliveryFees", mappedBy="ngCity")
     */
    private $deliveryFees;

    public function __toString()
    {
        return $this->getNgCityItem()['city'];
    }

    public function __construct()
    {
        $this->deliveryFees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJsonId(): ?int
    {
        return $this->jsonId;
    }

    /**
     * give the item in the json ng.json
     *
     * @return array|null
     */
    public function getNgCityItem(): ?array
    {
        return LocaleService::getArrayNgCities()[$this->jsonId];
    }



    public function setJsonId(int $jsonId): self
    {
        $this->jsonId = $jsonId;

        return $this;
    }

    /**
     * @return Collection|DeliveryFees[]
     */
    public function getDeliveryFees(): Collection
    {
        return $this->deliveryFees;
    }

    public function addDeliveryFee(DeliveryFees $deliveryFee): self
    {
        if (!$this->deliveryFees->contains($deliveryFee)) {
            $this->deliveryFees[] = $deliveryFee;
            $deliveryFee->setNgCity($this);
        }

        return $this;
    }

    public function removeDeliveryFee(DeliveryFees $deliveryFee): self
    {
        if ($this->deliveryFees->contains($deliveryFee)) {
            $this->deliveryFees->removeElement($deliveryFee);
            // set the owning side to null (unless already changed)
            if ($deliveryFee->getNgCity() === $this) {
                $deliveryFee->setNgCity(null);
            }
        }

        return $this;
    }
}
