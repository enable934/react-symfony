<?php

namespace App\Controller;

use App\Form\OrderType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/{reactRouting}", name="home", defaults={"reactRouting": null})
     */
    public function index()
    {
        return $this->render('home/index.html.twig', []);
    }
    /**
     * @Route("/test/test", name="test")
     */
    public function test()
    {
        $form = $this->createForm(OrderType::class);
        return $this->render('test.html.twig', ['form' => $form->createView()]);
    }
}
