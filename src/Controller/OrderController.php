<?php


namespace App\Controller;


use App\Entity\Order;
use App\Entity\Restaurant;
use App\Entity\Table;
use App\Form\OrderType;
use App\Service\OrderServiceInterface;
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
    public function createOrder(Request $request, OrderServiceInterface $orderService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new BadRequestHttpException('Неправильный JSON');
        }

        $form = $this->createForm(OrderType::class, null, [
            'csrf_protection' => false,
        ]);
        $form->submit($data);
        if (!$form->isValid()) {

            return new JsonResponse(['error' => 'Форма заповнена некоректно'], 400);
        }
        /** @var Order $order */
        $order = $form->getData();
        if (!$orderService->hasAnyTables($order)) {
            return new JsonResponse(['error' => 'Треба вибрати хоча б один стіл'], 400);
        }
        if ($orderService->hasAnyOrdersOnThisDate($order)){
            return new JsonResponse(['error' => 'На цю дату вже є замовлення'], 400);
        }
        $orderService->saveNewOrder($order);

        return new JsonResponse(['message' => 'Успішно заброньовано!', 'error' => '']);
    }

}