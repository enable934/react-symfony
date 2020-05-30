<?php


namespace App\Controller;


use App\Entity\Restaurant;
use App\Service\RestaurantServiceInterface;
use App\Service\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class RestaurantController extends AbstractController
{
    /**
     * @Route("/api/restaurants", name="restaurants")
     */
    public function index(RestaurantServiceInterface $restaurantService, SerializerInterface $serializer): JsonResponse
    {
        $restaurants = $restaurantService->getAllRestaurants();

        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
        ];

        $restaurants = $serializer->serialize($restaurants, $defaultContext);

        return JsonResponse::fromJsonString($restaurants);
    }

    /**
     * @Route("/api/{id}/tables", name="tables")
     */
    public function getTables(Restaurant $restaurant, RestaurantServiceInterface $restaurantService, SerializerInterface $serializer): JsonResponse
    {
        $tables = $restaurantService->getTablesByRestaurant($restaurant);
        $dateCallback = function ($innerObject) {
            return $innerObject instanceof \DateTimeInterface ? json_encode($innerObject) : '';
        };
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object) {
                return $object->getId();
            },
            AbstractNormalizer::CALLBACKS => [
                'date' => $dateCallback,
                'timeFrom' => $dateCallback,
                'timeTo' => $dateCallback,
            ]
        ];
        $tables = $serializer->serialize($tables, $defaultContext);

        return JsonResponse::fromJsonString($tables);
    }
}