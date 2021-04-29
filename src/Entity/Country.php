<?php

namespace App\Entity;

use App\Service\Locale\LocaleService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CountryRepository")
 */
class Country
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2)
     */
    private $code;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\DeliveryFees", mappedBy="country")
     */
    private $deliveryFees;

    public function __toString()
    {
        if($this->getCountryItem()['name']){
            return $this->getCountryItem()['name'];
        }
        else return '';        
    }

    public function __construct()
    {
        $this->deliveryFees = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * give the item in countries.json
     *
     * @return array|null
     */
    public function getCountryItem(): ?array
    {
        return LocaleService::getArrayCountries()[$this->code];
       
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

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
            $deliveryFee->setCountry($this);
        }

        return $this;
    }

    public function removeDeliveryFee(DeliveryFees $deliveryFee): self
    {
        if ($this->deliveryFees->contains($deliveryFee)) {
            $this->deliveryFees->removeElement($deliveryFee);
            // set the owning side to null (unless already changed)
            if ($deliveryFee->getCountry() === $this) {
                $deliveryFee->setCountry(null);
            }
        }

        return $this;
    }
}
