<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['mail'], message: 'There is already an account with this mail')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $lastName = null;

  #[ORM\Column(length: 255)]
  private ?string $firstName = null;

  #[ORM\Column(length: 255)]
  private ?string $mail = null;

  #[ORM\Column(type: 'json')]
  private array $roles = [];

  #[ORM\Column(length: 255)]
  private ?string $password = null;

  /**
   * @var Collection<int, BasketProduct>
   */
  #[ORM\OneToMany(targetEntity: BasketProduct::class, mappedBy: 'user', cascade: ['persist', 'remove'])]
  private Collection $basketProducts;

  /**
   * @var Collection<int, Order>
   */
  #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user', orphanRemoval: true)]
  private Collection $orders;

  #[ORM\Column]
  private ?\DateTimeImmutable $creationDate = null;

  public function __construct()
  {
    $this->orders = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getLastName(): ?string
  {
    return $this->lastName;
  }

  public function setLastName(string $lastName): static
  {
    $this->lastName = $lastName;

    return $this;
  }

  public function getFirstName(): ?string
  {
    return $this->firstName;
  }

  public function setFirstName(string $firstName): static
  {
    $this->firstName = $firstName;

    return $this;
  }

  public function getMail(): ?string
  {
    return $this->mail;
  }

  public function setMail(string $mail): static
  {
    $this->mail = $mail;

    return $this;
  }

  public function getUserIdentifier(): string
  {
    return $this->mail;
  }

  public function getRoles(): array
  {
    // Par défaut, tous les utilisateurs ont le rôle "ROLE_USER"
    $roles = $this->roles;
    $roles[] = 'ROLE_USER';

    return array_unique($roles);
  }

  public function setRoles(array $roles): self
  {
    $this->roles = $roles;
    return $this;
  }

  public function getPassword(): ?string
  {
    return $this->password;
  }

  public function setPassword(string $password): static
  {
    $this->password = $password;

    return $this;
  }

  /**
   * @return Collection<int, BasketProduct>
   */
  public function getBasketProduct(): Collection
  {
    return $this->basketProducts;
  }

  public function addBasketProduct(BasketProduct $basketProducts): static
  {
    // set the owning side of the relation if necessary
    if (!$this->basketProducts->contains($basketProducts)) {
      $this->basketProducts->add($basketProducts);
      $basketProducts->setUser($this);
    }

    return $this;
  }

  public function removeBasketProduct(BasketProduct $basketProduct): self
  {
    if ($this->basketProducts->removeElement($basketProduct)) {
      // Assurez-vous que l'association est bien supprimée
      if ($basketProduct->getUser() === $this) {
        $basketProduct->setUser(null);
      }
    }

    return $this;
  }

  /**
   * @return Collection<int, Order>
   */
  public function getOrders(): Collection
  {
    return $this->orders;
  }

  public function addOrder(Order $order): static
  {
    if (!$this->orders->contains($order)) {
      $this->orders->add($order);
      $order->setUser($this);
    }

    return $this;
  }

  public function removeOrder(Order $order): static
  {
    if ($this->orders->removeElement($order)) {
      // set the owning side to null (unless already changed)
      if ($order->getUser() === $this) {
        $order->setUser(null);
      }
    }

    return $this;
  }

  public function getCreationDate(): ?\DateTimeImmutable
  {
    return $this->creationDate;
  }

  public function setCreationDate(\DateTimeImmutable $creationDate): static
  {
    $this->creationDate = $creationDate;

    return $this;
  }

  public function eraseCredentials(): void {}
}
