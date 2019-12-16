<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TableRepository")
 * @ORM\Table(name="tables")
 */
class Table
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $number;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Order", mappedBy="tables")
     */
    private $orders;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Restaurant", inversedBy="tables")
     */
    private $restaurant;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    /**
     * @Groups({"json"})
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @Groups({"json"})
     */
    public function getNumber(): ?int
    {
        return $this->number;
    }

    public function setNumber(int $number): self
    {
        $this->number = $number;

        return $this;
    }

    /**
     * @return Collection|Order[]
     * @Groups({"json"})
     */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): self
    {
        if (!$this->orders->contains($order)) {
            $this->orders[] = $order;
        }

        return $this;
    }

    public function removeOrder(Order $order): self
    {
        if ($this->orders->contains($order)) {
            $this->orders->removeElement($order);
            $order->removeTable($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getNumber();
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): self
    {
        $this->restaurant = $restaurant;

        return $this;
    }
}
