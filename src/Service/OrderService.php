<?php


namespace App\Service;


use App\Entity\Order;
use App\Entity\Restaurant;
use App\Entity\Table;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;

final class OrderService implements OrderServiceInterface
{
    /**
     * @var RestaurantRepository
     */
    private $restaurantRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(RestaurantRepository $restaurantRepository, EntityManagerInterface $entityManager)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->entityManager = $entityManager;
    }

    public function hasAnyTables(Order $order): bool
    {
        return count($order->getTables()) > 0;
    }

    public function hasAnyOrdersOnThisDate(Order $newOrder): bool
    {
        $tables = $newOrder->getTables()->map(function (Table $table) use($newOrder) {
            $table->getOrders()->removeElement($newOrder);

            return $table;
        });
        $date = $newOrder->getDate();
        $timeFrom = $newOrder->getTimeFrom();
        $timeTo = $newOrder->getTimeTo();
        /** @var Table $table */
        foreach ($tables as $table) {
            foreach ($table->getOrders() as $order) {
                if($order->getDate()->getTimestamp() !== $date->getTimestamp()){
                    continue;
                }
                if (
                    ($order->getTimeFrom() >= $timeFrom && $order->getTimeTo() <= $timeTo)
                    || ($timeFrom < $order->getTimeFrom() && ($timeTo >= $order->getTimeFrom() && $timeTo <= $order->getTimeTo()))
                    || ($timeTo > $order->getTimeFrom() && ($timeFrom >= $order->getTimeFrom() && $timeFrom <= $order->getTimeTo()))
                ) {
                    return true;
                }
            }
        }
        return false;
    }

    public function saveNewOrder(Order $order): void
    {
        $this->entityManager->persist($order);
        $this->entityManager->flush();
    }
}