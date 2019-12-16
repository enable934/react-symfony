<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Table;
use App\Form\OrderType;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DataController extends AbstractController
{
    /**
     * @Route("/api/tables", name="tables")
     */
    public function index(EntityManagerInterface $entityManager): JsonResponse
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

    /**
     * @Route("/api/order/new", name="order_new", methods={"POST"})
     */
    public function createOrder(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        $form = $this->createForm(OrderType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);
        if (!$form->isValid()) {
            $errors = $form->getErrors();

            return new JsonResponse(['error' => 'Форма заповнена некоректно'], 400);
        }
        /** @var Order $order */
        $order = $form->getData();
        if (count($order->getTables()) === 0) {
            return new JsonResponse(['error' => 'Треба вибрати хоча б один стіл'], 400);
        }
        if ($this->checkOrdersByTimeAndTable(
            $order->getTables()->map(function (Table $table) use($order) {
                $table->getOrders()->removeElement($order);

                return $table;
            }),
            $order->getDate(),
            $order->getTimeFrom(),
            $order->getTimeTo())
        ){
            return new JsonResponse(['error' => 'На цю дату вже є замовлення'], 400);
        }
            $entityManager->persist($order);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Успішно заброньовано!', 'error' => '']);
    }

    private function checkOrdersByTimeAndTable(Collection $tables, \DateTimeInterface $date, \DateTimeInterface $timeFrom, \DateTimeInterface $timeTo): bool
    {
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
}
