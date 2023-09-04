<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column]
    private ?float $price = null;

    #[ORM\OneToMany(mappedBy: 'item', targetEntity: CustomerOrderItem::class)]
    private Collection $customerOrderItems;

    public function __construct()
    {
        $this->customerOrderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return Collection<int, CustomerOrderItem>
     */
    public function getCustomerOrderItems(): Collection
    {
        return $this->customerOrderItems;
    }

    public function addCustomerOrderItem(CustomerOrderItem $customerOrderItem): static
    {
        if (!$this->customerOrderItems->contains($customerOrderItem)) {
            $this->customerOrderItems->add($customerOrderItem);
            $customerOrderItem->setItem($this);
        }

        return $this;
    }

    public function removeCustomerOrderItem(CustomerOrderItem $customerOrderItem): static
    {
        if ($this->customerOrderItems->removeElement($customerOrderItem)) {
            // set the owning side to null (unless already changed)
            if ($customerOrderItem->getItem() === $this) {
                $customerOrderItem->setItem(null);
            }
        }

        return $this;
    }
}
