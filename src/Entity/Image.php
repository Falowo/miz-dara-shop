<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ImageRepository")
 * @Vich\Uploadable()
 * @ORM\Embeddable
 *
 */
class Image
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="images")
     * @ORM\JoinColumn(nullable=true)
     */
    private $product;

    /**
     * @var File|null
     * @Vich\UploadableField(mapping="product_images", fileNameProperty="name")
     * @Assert\Image(mimeTypes="image/jpeg")
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255)
     * @var string
     */
    private $name;

    
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Tint")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tint;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated_at;


    public function __toString()
    {
        if ($this->name) {
            return $this->name;
        } else return '';    
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
            if (!is_null($this->product)) {        
                $product->addImage($this);
            }

        return $this;
    }


    /**
     * @return null|string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $image): self
    {
        $this->name = $image;

        return $this;
    }


    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param null|File $imageFile
     * @return Image
     */
    public function setImageFile(?File $imageFile): Image
    {
        $this->imageFile = $imageFile;
        if ($this->imageFile instanceof UploadedFile) {
            $this->updated_at = new \DateTime('now');
        }elseif(is_null($imageFile)){
            $this->setProduct(null);
        }

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

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(): self
    {
        $this->updated_at = new \DateTime('now');

        return $this;
    }
}
