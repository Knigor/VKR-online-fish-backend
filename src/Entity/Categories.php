<?php

namespace App\Entity;

use App\Repository\CategoriesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoriesRepository::class)]
class Categories
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $nameCategories = null;

    public function __construct()
    {
        $this->id = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function addId(Products $id): static
    {
        if (!$this->id->contains($id)) {
            $this->id->add($id);
            $id->setCategories($this);
        }

        return $this;
    }

    public function removeId(Products $id): static
    {
        if ($this->id->removeElement($id)) {
            // set the owning side to null (unless already changed)
            if ($id->getCategories() === $this) {
                $id->setCategories(null);
            }
        }

        return $this;
    }

    public function getNameCategories(): ?string
    {
        return $this->nameCategories;
    }

    public function setNameCategories(string $nameCategories): static
    {
        $this->nameCategories = $nameCategories;

        return $this;
    }
}
