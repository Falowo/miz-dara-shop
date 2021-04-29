<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DeliveryFeesRepository")
 */
class DeliveryFees
{


    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     *
     * @var int
     */
    private $deliveryPrice;
    

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $fixedAmount;

   

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $freeForMoreThan;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $maxDays;

   
   
    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NgCity", inversedBy="deliveryFees")
     */
    private $ngCity;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\NgState", inversedBy="deliveryFees")
     */
    private $ngState;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country", inversedBy="deliveryFees")
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Continent", inversedBy="deliveryFees")
     */
    private $continent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $amountByKm;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $percentOfRawPrice;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Transport", inversedBy="deliveryFeess")
     * @ORM\JoinColumn(nullable=false)
     */
    private $transport;

    public function __toString()
    {
        $string =  $this->transport . ' ' ;

       if($this->ngCity){
            $string .= 'ngCity : ' . $this->ngCity;
        }
        
        elseif($this->ngState){
            $string .= 'ngState : ' . $this->ngState;
        }
        elseif($this->country){
            $string .= 'country : ' . $this->country;
        }
        elseif($this->continent){
            $string .= 'continent : '  . $this->continent;
        }


        return $string;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFixedAmount(): ?int
    {
        return $this->fixedAmount;
    }

    public function setFixedAmount(?int $fixedAmount): self
    {
        $this->fixedAmount = $fixedAmount;

        return $this;
    }


    public function getFreeForMoreThan(): ?int
    {
        return $this->freeForMoreThan;
    }

    public function setFreeForMoreThan(?int $freeForMoreThan): self
    {
        $this->freeForMoreThan = $freeForMoreThan;

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



    public function getNgCity(): ?NgCity
    {
        return $this->ngCity;
    }

    public function setNgCity(?NgCity $ngCity): self
    {
        $this->ngCity = $ngCity;

        return $this;
    }

    public function getNgState(): ?NgState
    {
        return $this->ngState;
    }

    public function setNgState(?NgState $ngState): self
    {
        $this->ngState = $ngState;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getContinent(): ?Continent
    {
        return $this->continent;
    }

    public function setContinent(?Continent $continent): self
    {
        $this->continent = $continent;

        return $this;
    }

    public function getAmountByKm(): ?int
    {
        return $this->amountByKm;
    }

    public function setAmountByKm(?int $amountByKm): self
    {
        $this->amountByKm = $amountByKm;

        return $this;
    }

    public function getPercentOfRawPrice(): ?int
    {
        return $this->percentOfRawPrice;
    }

    public function setPercentOfRawPrice(?int $percentOfRawPrice): self
    {
        $this->percentOfRawPrice = $percentOfRawPrice;

        return $this;
    }

    public function getTransport(): ?Transport
    {
        return $this->transport;
    }

    public function setTransport(?Transport $transport): self
    {
        $this->transport = $transport;

        return $this;
    }

   


    /**
     * Get the value of deliveryPrice
     *
     * @return  int
     */ 
    public function getDeliveryPrice()
    {
        return $this->deliveryPrice;
    }

    /**
     * Set the value of deliveryPrice
     *
     * @param  int  $deliveryPrice
     *
     * @return  self
     */ 
    public function setDeliveryPrice(int $deliveryPrice)
    {
        $this->deliveryPrice = $deliveryPrice;

        return $this;
    }
}
