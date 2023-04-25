<?php

namespace App\Controller;


use App\Entity\Dish;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route("/dish",name :"dish.")]
class DishController extends AbstractController
{
    #[Route('/', name: 'edit')]
    public function index(DishRepository $dishrep): Response
    {
        $dishes = $dishrep->findAll();
        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes,
        ]);
    }

    #[Route('/create', name: 'create')]
    function create(Request $request , EntityManagerInterface $registry)
    {
        $dish = new Dish();
        $dish->setName('sandwich');
        $dishName =$dish ->getName();
        $registry->persist($dish);
        $registry->flush();

        return new Response($dishName.'has been created', Response::HTTP_CREATED);
    }
}
