<?php


namespace App\Service;


use App\Entity\Restaurant;
use App\Entity\Table;

interface RestaurantServiceInterface
{
    /**
     * @return Restaurant[]
     */
    public function getAllRestaurants():array;

    /**
     * @param Restaurant $restaurant
     * @return Table[]
     */
    public function getTablesByRestaurant(Restaurant $restaurant):array ;
}