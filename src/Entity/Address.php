<?php

namespace App\Entity;

use App\Service\Locale\LocaleService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use libphonenumber\PhoneNumber;
use Misd\PhoneNumberBundle\Doctrine\DBAL\Types\PhoneNumberType;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;
use ZipCodeValidator\Constraints\ZipCode;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AddressRepository")
 */
class Address
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Country")
     * @ORM\JoinColumn(nullable=false)
     */
    private $country;

     /**
     * @Assert\NotBlank()
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $cityJsonId;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $street;

   /**
     * @ORM\Column(type="string", length=16, nullable=true)
     * @ZipCode(getter="getCountryCode", ignoreEmpty=true)
     * 
     */
    private $zipCode;

     /**
     * @ORM\Column(type="phone_number", nullable=true)
     * @AssertPhoneNumber
     * @Assert\NotBlank
     * 
     */
    private $phoneNumber;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Purchase", mappedBy="address")
     * @ORM\JoinColumn(nullable=true)
     * 
     */
    private $purchases;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $FirstName;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $lastName;

    public function __construct()
    {
        $this->purchases = new ArrayCollection();
    }

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function getCountryCode(): ?string
    {
        return $this->country->getCode();
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCityJsonId(): ?int
    {
        return $this->cityJsonId;
    }

    public function getCityItem(): array
    {
        return LocaleService::getArrayCities()[$this->cityJsonId];
    }

    public function getCityName(): string
    {
        return $this->getCityItem()['name'];
    }


    public function setCityJsonId(int $cityJsonId): self
    {
        $this->cityJsonId = $cityJsonId;

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): self
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    public function getPhoneNumber(): ?PhoneNumber
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?PhoneNumber $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * @return Collection|Purchase[]
     */
    public function getPurchases(): Collection
    {
        return $this->purchases;
    }

    public function addPurchase(Purchase $purchase): self
    {
        if (!$this->purchases->contains($purchase)) {
            $this->purchases[] = $purchase;
            $purchase->setAddress($this);
        }

        return $this;
    }

    public function removePurchase(Purchase $purchase): self
    {
        if ($this->purchases->contains($purchase)) {
            $this->purchases->removeElement($purchase);
            // set the owning side to null (unless already changed)
            if ($purchase->getAddress() === $this) {
                $purchase->setAddress(null);
            }
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(?string $FirstName): self
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

   
}
