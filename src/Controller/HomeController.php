<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(DishRepository $dish): Response
    {
        $dish = $dish->findAll();
        $rand_dish = array_rand($dish,2) ;
        
        return $this->render('home/index.html.twig', [
            'dish1' => $dish[$rand_dish[0]],
            'dish2' => $dish[$rand_dish[1]]

        ]);
    }
}
