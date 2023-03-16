<?php

namespace App\Entity\Security;

use App\Entity\Financial\Investment;
use App\Entity\Financial\Transaction;
use App\Entity\Wallet\WithdrawalMethod;
use App\Entity\Wallet\WithdrawalRequest;
use App\Repository\Security\UserRepository;
use App\Service\WalletService;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{


    private const REGISTERED = 'registered';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $firstname = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastname = null;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Detail $detail = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Investment::class, orphanRemoval: true)]
    private Collection $investments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Transaction::class, orphanRemoval: true)]
    private Collection $transactions;

    private float $balance;


    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WithdrawalMethod::class, orphanRemoval: true)]
    private Collection $withdrawalMethods;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: WithdrawalRequest::class, orphanRemoval: true)]
    private Collection $withdrawalRequests;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Files $files = null;

    public function __construct(){
        $this->status = self::REGISTERED;
        $this->investments = new ArrayCollection();
        $this->transactions = new ArrayCollection();
        $this->withdrawalMethods = new ArrayCollection();
        $this->withdrawalRequests = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getDetail(): ?Detail
    {
        return $this->detail;
    }

    public function setDetail(Detail $detail): self
    {
        // set the owning side of the relation if necessary
        if ($detail->getUser() !== $this) {
            $detail->setUser($this);
        }

        $this->detail = $detail;

        return $this;
    }

    public function isAdmin(): bool {
        return in_array("ROLE_ADMIN", $this->getRoles());
    }

    /**
     * @return Collection<int, Investment>
     */
    public function getInvestments(): Collection
    {
        return $this->investments;
    }

    public function addInvestment(Investment $investment): self
    {
        if (!$this->investments->contains($investment)) {
            $this->investments->add($investment);
            $investment->setUser($this);
        }

        return $this;
    }

    public function removeInvestment(Investment $investment): self
    {
        if ($this->investments->removeElement($investment)) {
            // set the owning side to null (unless already changed)
            if ($investment->getUser() === $this) {
                $investment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): self
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): self
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }
    
    public function __toString()
    {
        return $this->getEmail();
    }


    /**
     * @return Collection<int, WithdrawalMethod>
     */
    public function getWithdrawalMethods(): Collection
    {
        return $this->withdrawalMethods;
    }

    public function addWithdrawalMethod(WithdrawalMethod $withdrawalMethod): self
    {
        if (!$this->withdrawalMethods->contains($withdrawalMethod)) {
            $this->withdrawalMethods->add($withdrawalMethod);
            $withdrawalMethod->setUser($this);
        }

        return $this;
    }

    public function removeWithdrawalMethod(WithdrawalMethod $withdrawalMethod): self
    {
        if ($this->withdrawalMethods->removeElement($withdrawalMethod)) {
            // set the owning side to null (unless already changed)
            if ($withdrawalMethod->getUser() === $this) {
                $withdrawalMethod->setUser(null);
            }
        }

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
            $withdrawalRequest->setUser($this);
        }

        return $this;
    }

    public function removeWithdrawalRequest(WithdrawalRequest $withdrawalRequest): self
    {
        if ($this->withdrawalRequests->removeElement($withdrawalRequest)) {
            // set the owning side to null (unless already changed)
            if ($withdrawalRequest->getUser() === $this) {
                $withdrawalRequest->setUser(null);
            }
        }

        return $this;

    }
    public function getFiles(): ?Files
    {
        return $this->files;
    }

    public function setFiles(?Files $files): self
    {
        // unset the owning side of the relation if necessary
        if ($files === null && $this->files !== null) {
            $this->files->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($files !== null && $files->getUser() !== $this) {
            $files->setUser($this);
        }

        $this->files = $files;

        return $this;
    }

}
