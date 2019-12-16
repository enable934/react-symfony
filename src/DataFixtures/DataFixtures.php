<?php

namespace App\DataFixtures;

use App\Entity\Order;
use App\Entity\Restaurant;
use App\Entity\Table;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class DataFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $this->loadRestaurant($manager);
        $this->loadTables($manager);
        $this->loadOrders($manager);
    }

    private function loadTables(ObjectManager $manager):void {
        for ($i=1;$i<=25;$i++){
            $table = new Table();
            $table->setNumber($i);
            $manager->persist($table);
        }
        $manager->flush();
    }

    private function loadOrders(ObjectManager $manager): void
    {
        $order = new Order();
        $now = new \DateTimeImmutable();
        $order->setDate($now);
        $order->setTimeFrom($now);
        $order->setTimeTo($now->add(new \DateInterval('PT2H')));
        $order->setUserEmail('example@mail.ru');
        $order->setUserName('Orest');
        $order->setUserPhone('+380966608140');
        $order->addTable($manager->getRepository(Table::class)->findOneBy(['number' => 2]));
        $manager->persist($order);
        $manager->flush();
    }

    private function loadRestaurant(ObjectManager $manager): void{
        $restaurant = new Restaurant();
        $restaurant->setName('Rabbit\'s restaurant');
        $manager->persist($restaurant);
        $manager->flush();
    }
}
