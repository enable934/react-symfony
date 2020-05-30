<?php


namespace App\Service;


use App\Entity\Order;

interface OrderServiceInterface
{
    public function hasAnyTables(Order $order):bool;

    public function hasAnyOrdersOnThisDate(Order $newOrder):bool;

    public function saveNewOrder(Order $order):void;
}