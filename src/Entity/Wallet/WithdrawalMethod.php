<?php

namespace App\Entity\Wallet;

use App\Entity\Security\User;
use App\Repository\Wallet\WithdrawalMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WithdrawalMethodRepository::class)]
class WithdrawalMethod
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'withdrawalMethods')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'method', targetEntity: WithdrawalRequest::class, cascade: ['persist', 'remove'])]
    private Collection $withdrawalRequests;

    #[ORM\Column(length: 255)]
    private ?string $bankName = null;

    #[ORM\Column(length: 255)]
    private ?string $bankAccountNumber = null;

    public function __construct()
    {
        $this->withdrawalRequests = new ArrayCollection();
    }

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
     * @return Collection<int, WithdrawalRequest>
     */
    public function getWithdrawalRequests(): Collection
    {
        return $this->withdrawalRequests;
    }

    public function addWithdrawalRequest(WithdrawalRequest $withdrawalRequest): self
    {
        if (!$this->withdrawalRequests->contains($withdrawalRequest)) {
            $this->withdrawalRequests->add($withdrawalRequest);
            $withdrawalRequest->setMethod($this);
        }

        return $this;
    }

    public function removeWithdrawalRequest(WithdrawalRequest $withdrawalRequest): self
    {
        if ($this->withdrawalRequests->removeElement($withdrawalRequest)) {
            // set the owning side to null (unless already changed)
            if ($withdrawalRequest->getMethod() === $this) {
                $withdrawalRequest->setMethod(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getBankName() . ' | ' . $this->getBankAccountNumber();
    }

    public function getBankName(): ?string
    {
        return $this->bankName;
    }

    public function setBankName(string $bankName): self
    {
        $this->bankName = $bankName;

        return $this;
    }

    public function getBankAccountNumber(): ?string
    {
        return $this->bankAccountNumber;
    }

    public function setBankAccountNumber(string $bankAccountNumber): self
    {
        $this->bankAccountNumber = $bankAccountNumber;

        return $this;
    }

}
