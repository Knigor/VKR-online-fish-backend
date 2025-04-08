<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsersRepository::class)]
class Users
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nameUser = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $passwordHash = null;

    #[ORM\Column(length: 30)]
    private ?string $phone = null;

    #[ORM\Column(length: 40)]
    private ?string $role = null;

    /**
     * @var Collection<int, Products>
     */
    #[ORM\OneToMany(targetEntity: Products::class, mappedBy: 'id')]
    private Collection $products;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'id')]
    private Collection $reviews;

    /**
     * @var Collection<int, Orders>
     */
    #[ORM\OneToMany(targetEntity: Orders::class, mappedBy: 'id')]
    private Collection $orders;

    /**
     * @var Collection<int, Products>
     */
    #[ORM\OneToMany(targetEntity: Products::class, mappedBy: 'users')]
    private Collection $idProduct;

    /**
     * @var Collection<int, Orders>
     */
    #[ORM\OneToMany(targetEntity: Orders::class, mappedBy: 'users')]
    private Collection $idOrder;

    /**
     * @var Collection<int, Review>
     */
    #[ORM\OneToMany(targetEntity: Review::class, mappedBy: 'users')]
    private Collection $reviewId;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->idProduct = new ArrayCollection();
        $this->idOrder = new ArrayCollection();
        $this->reviewId = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameUser(): ?string
    {
        return $this->nameUser;
    }

    public function setNameUser(string $nameUser): static
    {
        $this->nameUser = $nameUser;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPasswordHash(): ?string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): static
    {
        $this->passwordHash = $passwordHash;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): static
    {
        $this->phone = $phone;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection<int, Products>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Products $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setId($this);
        }

        return $this;
    }

    public function removeProduct(Products $product): static
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getId() === $this) {
                $product->setId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setId($this);
        }

        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getId() === $this) {
                $review->setId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Orders $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setId($this);
        }

        return $this;
    }

    public function removeOrder(Orders $order): static
    {
        if ($this->orders->removeElement($order)) {
            // set the owning side to null (unless already changed)
            if ($order->getId() === $this) {
                $order->setId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Products>
     */
    public function getIdProduct(): Collection
    {
        return $this->idProduct;
    }

    public function addIdProduct(Products $idProduct): static
    {
        if (!$this->idProduct->contains($idProduct)) {
            $this->idProduct->add($idProduct);
            $idProduct->setUsers($this);
        }

        return $this;
    }

    public function removeIdProduct(Products $idProduct): static
    {
        if ($this->idProduct->removeElement($idProduct)) {
            // set the owning side to null (unless already changed)
            if ($idProduct->getUsers() === $this) {
                $idProduct->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Orders>
     */
    public function getIdOrder(): Collection
    {
        return $this->idOrder;
    }

    public function addIdOrder(Orders $idOrder): static
    {
        if (!$this->idOrder->contains($idOrder)) {
            $this->idOrder->add($idOrder);
            $idOrder->setUsers($this);
        }

        return $this;
    }

    public function removeIdOrder(Orders $idOrder): static
    {
        if ($this->idOrder->removeElement($idOrder)) {
            // set the owning side to null (unless already changed)
            if ($idOrder->getUsers() === $this) {
                $idOrder->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviewId(): Collection
    {
        return $this->reviewId;
    }

    public function addReviewId(Review $reviewId): static
    {
        if (!$this->reviewId->contains($reviewId)) {
            $this->reviewId->add($reviewId);
            $reviewId->setUsers($this);
        }

        return $this;
    }

    public function removeReviewId(Review $reviewId): static
    {
        if ($this->reviewId->removeElement($reviewId)) {
            // set the owning side to null (unless already changed)
            if ($reviewId->getUsers() === $this) {
                $reviewId->setUsers(null);
            }
        }

        return $this;
    }
}
