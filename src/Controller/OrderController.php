<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Order;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/order', name: 'orders')]
    public function index(OrderRepository $order): Response
    {
        $order = $order->findBy(
        ['ortable' => 'table1']
        );

        return $this->render('order/index.html.twig', [
            'order' => $order,
        ]);
    }
    #[Route('/order/{id}', name: 'order')]
    public function order(Dish $dish ,EntityManagerInterface $registry): Response
    {
        $order = new Order();
        $order->setOrtable('table1');
        $order->setOrnumber($dish->getId());
        $order->setName($dish->getName());
        $order->setPrice($dish->getPrice());
        $order->setStatus('Open');

        $registry->persist($order); 
        $registry->flush();
        //message
        $this ->addFlash('order',$order->getName().' was added to orders.') ; 
        return $this->redirect($this->generateUrl('menu'));
        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
        ]);
    }
    #[Route('/status/{id},{status}',name:'status')]
    public function status($id , $status,OrderRepository $ord, EntityManagerInterface $registry)
    {
        $order = $registry->getRepository(Order::class)->find($id);
        $order->setStatus($status);
        $registry->flush();
        return $this->redirect($this->generateUrl('orders'));

    }

    #[Route('/delete/{id}', name: 'delete')]
    function delete($id , OrderRepository $Ordrep ,EntityManagerInterface $registry){
        $order =$Ordrep ->find($id);
        $registry -> remove($order); 
        $registry -> flush();
        //message 
        return $this->redirect($this->generateUrl('orders'));
    }
}
