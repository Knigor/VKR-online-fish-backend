<?php

namespace App\Entity;

use App\Repository\ReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reviews')]
    private ?Products $idProduct = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $textReview = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'reviewId')]
    private ?Users $users = null;

    #[ORM\Column]
    private ?bool $isModerate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?Users $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getIdProduct(): ?Products
    {
        return $this->idProduct;
    }

    public function setIdProduct(?Products $idProduct): static
    {
        $this->idProduct = $idProduct;

        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getTextReview(): ?string
    {
        return $this->textReview;
    }

    public function setTextReview(string $textReview): static
    {
        $this->textReview = $textReview;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUsers(): ?Users
    {
        return $this->users;
    }

    public function setUsers(?Users $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function isModerate(): ?bool
    {
        return $this->isModerate;
    }

    public function setIsModerate(bool $isModerate): static
    {
        $this->isModerate = $isModerate;

        return $this;
    }
}
