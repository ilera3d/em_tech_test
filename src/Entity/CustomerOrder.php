<?php

namespace App\Entity;

use App\Repository\CustomerOrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerOrderRepository::class)]
class CustomerOrder
{
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_DELAYED = 'delayed';
    public const STATUS_COMPLETED = 'completed';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $reference = null;

    #[ORM\Column(length: 255)]
    private ?string $customerName = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $customerAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'customerOrder', targetEntity: CustomerOrderItem::class)]
    private Collection $customerOrderItems;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $estimatedDeliveryDateTime = null;

    #[ORM\ManyToOne(inversedBy: 'customerOrders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?DeliveryOption $deliveryOption = null;

    public function __construct()
    {
        $this->customerOrderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    public function getCustomerName(): ?string
    {
        return $this->customerName;
    }

    public function setCustomerName(string $customerName): static
    {
        $this->customerName = $customerName;

        return $this;
    }

    public function getCustomerAddress(): ?string
    {
        return $this->customerAddress;
    }

    public function setCustomerAddress(string $customerAddress): static
    {
        $this->customerAddress = $customerAddress;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

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
            $customerOrderItem->setCustomerOrder($this);
        }

        return $this;
    }

    public function removeCustomerOrderItem(CustomerOrderItem $customerOrderItem): static
    {
        if ($this->customerOrderItems->removeElement($customerOrderItem)) {
            // set the owning side to null (unless already changed)
            if ($customerOrderItem->getCustomerOrder() === $this) {
                $customerOrderItem->setCustomerOrder(null);
            }
        }

        return $this;
    }

    public function getEstimatedDeliveryDateTime(): ?\DateTimeInterface
    {
        return $this->estimatedDeliveryDateTime;
    }

    public function setEstimatedDeliveryDateTime(\DateTimeInterface $estimatedDeliveryDateTime): static
    {
        $this->estimatedDeliveryDateTime = $estimatedDeliveryDateTime;

        return $this;
    }

    public function getDeliveryOption(): ?DeliveryOption
    {
        return $this->deliveryOption;
    }

    public function setDeliveryOption(?DeliveryOption $deliveryOption): static
    {
        $this->deliveryOption = $deliveryOption;

        return $this;
    }

}
