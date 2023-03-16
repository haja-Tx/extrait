<?php

namespace App\Entity\Security;

use App\Repository\Security\FilesRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: FilesRepository::class)]
#[Vich\Uploadable]
class Files implements \Serializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'files', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\Column(type: 'string',length: 255)]
    private ?string $frontIdCardName;

    #[ORM\Column(type: 'string',length: 255)]
    private ?string $backIdCardName;

    #[ORM\Column(type: 'string',length: 255)]
    private ?string $proofOfAddressName;

    #[Vich\UploadableField(mapping: 'identity_files', fileNameProperty: 'frontIdCardName')]
    private ?File $frontIdCard = Null;

    #[Vich\UploadableField(mapping: 'identity_files', fileNameProperty: 'backIdCardName')]
    private ?File $backIdCard = Null;

    #[Vich\UploadableField(mapping: 'identity_files', fileNameProperty: 'proofOfAddressName')]
    private ?File $proofOfAddress = Null;

    public function getId(): ?int
    {
        return $this->id;
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

    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $frontIdCard
     */
    public function setFrontIdCard(?File $frontIdCard = null): void
    {
        $this->frontIdCard = $frontIdCard;

        if (null !== $frontIdCard) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }
    }

    
    public function getFrontIdCard() : ?File
    {
        return $this->frontIdCard;
    }

    public function getFrontIdCardName(): ?string
    {
        return $this->frontIdCardName;
    }

    public function setFrontIdCardName(?string $frontIdCardName): self
    {
        $this->frontIdCardName = $frontIdCardName;

        return $this;
    }

    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $backIdCard
     */
    public function setBackIdCard(?File $backIdCard = null): void
    {
        $this->backIdCard = $backIdCard;

        if (null !== $backIdCard) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }
    }

    public function getBackIdCard() : ?File
    {
        return $this->backIdCard;
    }
    
    public function getBackIdCardName(): ?string
    {
        return $this->backIdCardName;
    }

    public function setBackIdCardName(?string $backIdCardName): self
    {
        $this->backIdCardName = $backIdCardName;

        return $this;
    }

    
    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|\Symfony\Component\HttpFoundation\File\UploadedFile|null $proofOfAddress
     */
    public function setProofOfAddress(?File $proofOfAddress = null): void
    {
        $this->proofOfAddress = $proofOfAddress;

        if (null !== $proofOfAddress) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updatedAt = new \DateTime();
        }
    }

    public function getProofOfAddress(): ?File
    {
        return $this->proofOfAddress;
    }

    public function getProofOfAddressName(): ?string
    {
        return $this->proofOfAddressName;
    }

    public function setProofOfAddressName(?string $proofOfAddressName): self
    {
        $this->proofOfAddressName = $proofOfAddressName;

        return $this;
    }

    public function serialize()
    {
        $this->frontIdCardName = base64_encode($this->frontIdCardName);
        $this->backIdCardName = base64_encode($this->backIdCardName);
        $this->proofOfAddressName = base64_encode($this->proofOfAddressName);
    }
    
    public function unserialize($serialized)
    {
        $this->frontIdCardName = base64_decode($this->frontIdCardName);
        $this->backIdCardName = base64_decode($this->backIdCardName);
        $this->proofOfAddressName = base64_decode($this->proofOfAddressName);

    }


}

