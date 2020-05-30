<?php


namespace App\Service;


use App\Entity\Restaurant;
use App\Repository\RestaurantRepository;
use App\Repository\TableRepository;

final class RestaurantService implements RestaurantServiceInterface
{
    /**
     * @var RestaurantRepository
     */
    private $restaurantRepository;
    /**
     * @var TableRepository
     */
    private $tableRepository;

    public function __construct(RestaurantRepository $restaurantRepository, TableRepository $tableRepository)
    {
        $this->restaurantRepository = $restaurantRepository;
        $this->tableRepository = $tableRepository;
    }

    public function getAllRestaurants(): array
    {
       return $this->restaurantRepository->findAll();
    }

    public function getTablesByRestaurant(Restaurant $restaurant):array
    {
       return $this->tableRepository->findBy(['restaurant' => $restaurant]);
    }
}