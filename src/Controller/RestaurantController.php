<?php


namespace App\Controller;


use App\Entity\Restaurant;
use App\Entity\Table;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RestaurantController extends AbstractController
{
    /**
     * @Route("/api/restaurants", name="restaurants")
     */
    public function index(EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var Restaurant[] $restaurants */
        $restaurants = $entityManager->getRepository(Restaurant::class)->findAll();
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);
        $restaurants = $serializer->serialize($restaurants, 'json', ['groups' => 'json']);
        return JsonResponse::fromJsonString($restaurants);
    }

    /**
     * @Route("/api/{id}/tables", name="tables")
     */
    public function getTables(Restaurant $restaurant, EntityManagerInterface $entityManager): JsonResponse
    {
        /** @var Table[] $tables */
        $tables = $entityManager->getRepository(Table::class)->findAll();
        $encoder = new JsonEncoder();
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                return $object->getId();
            },
        ];
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, null, null, null, $defaultContext);
        $serializer = new Serializer([$normalizer], [$encoder]);
        $tables = $serializer->serialize($tables, 'json', ['groups' => 'json']);
        return JsonResponse::fromJsonString($tables);
    }
}