<?php


namespace App\Controller;


use App\Entity\Order;
use App\Entity\Restaurant;
use App\Entity\Table;
use App\Form\OrderType;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     * @Route("/api/order/new", name="order_new", methods={"POST"})
     */
    public function createOrder(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new BadRequestHttpException('Invalid JSON');
        }
        /** @var Restaurant $restaurant */
        $restaurant = $entityManager->getRepository(Restaurant::class)->find($data['restaurant']);
        $order = new Order();
        $order->setRestaurant($restaurant);
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