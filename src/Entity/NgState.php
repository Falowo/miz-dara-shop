<?php

namespace App\Entity;

use App\Service\Locale\LocaleService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NgStateRepository")
 */
class NgState
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
     * @ORM\OneToMany(targetEntity="App\Entity\DeliveryFees", mappedBy="ngState")
     */
    private $deliveryFees;

    public function __toString()
    {
        return $this->getNgStateItem();
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
     * give the item in ngState.json
     *
     * @return string|null
     */
    public function getNgStateItem(): ?string
    {
        return LocaleService::getNgStateConst()[$this->jsonId];
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
            $deliveryFee->setNgState($this);
        }

        return $this;
    }

    public function removeDeliveryFee(DeliveryFees $deliveryFee): self
    {
        if ($this->deliveryFees->contains($deliveryFee)) {
            $this->deliveryFees->removeElement($deliveryFee);
            // set the owning side to null (unless already changed)
            if ($deliveryFee->getNgState() === $this) {
                $deliveryFee->setNgState(null);
            }
        }

        return $this;
    }
}
