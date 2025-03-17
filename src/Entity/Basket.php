<?php

namespace App\Entity;

use App\Repository\BasketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BasketRepository::class)]
class Basket
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\OneToOne(inversedBy: 'basket', cascade: ['persist', 'remove'])]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $user = null;

  /**
   * @var Collection<int, BasketProduct>
   */
  #[ORM\OneToMany(targetEntity: BasketProduct::class, mappedBy: 'basketId', orphanRemoval: true)]
  private Collection $basketProducts;

  public function __construct()
  {
    $this->basketProducts = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(User $user): static
  {
    $this->user = $user;

    return $this;
  }

  /**
   * @return Collection<int, BasketProduct>
   */
  public function getBasketProducts(): Collection
  {
    return $this->basketProducts;
  }

  public function addBasketProduct(BasketProduct $basketProduct): static
  {
    if (!$this->basketProducts->contains($basketProduct)) {
      $this->basketProducts->add($basketProduct);
      $basketProduct->setBasket($this);
    }

    return $this;
  }

  public function removeBasketProduct(BasketProduct $basketProduct): static
  {
    if ($this->basketProducts->removeElement($basketProduct)) {
      // set the owning side to null (unless already changed)
      if ($basketProduct->getBasket() === $this) {
        $basketProduct->setBasket(null);
      }
    }

    return $this;
  }
}
