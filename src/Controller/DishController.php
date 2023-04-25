<?php

namespace App\Controller;


use App\Entity\Dish;
use App\Form\DishType;
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
    public function create(Request $request , EntityManagerInterface $registry)
    {
        $dish = new Dish();
        //Form
        $form = $this->createForm(DishType::class , $dish);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            $picture = $request->files->get('dish')['Attachment'];
            if($picture){
                $filename = md5(uniqid()).'.'.$picture->guessClientExtension();
            }
            $picture->move(
                $this->getParameter('picture_folder'),
                $filename
            );
            $dish -> setPicture($filename);
            $registry -> persist($dish);
            $registry -> flush();
            return $this->redirect($this->generateUrl('dish.edit'));
        }
        
        

        return $this->render('dish/create.html.twig', [
            'createForm' => $form->createView(),
        ]);
    }
    #[Route('/delete/{id}', name: 'delete')]
    function delete($id , DishRepository $dishRep ,EntityManagerInterface $registry){
        $dish =$dishRep ->find($id);
        $registry -> remove($dish); 
        $registry -> flush();
        //message
        $this ->addFlash('success','Dish has been deleted successfully') ; 
        return $this->redirect($this->generateUrl('dish.edit'));
    }
    #[Route('/show/{id}', name: 'show')]
    public function show(dish $dish){
        return $this->render('dish/show.html.twig',[
            'dish'=> $dish
        ]) ;
    }
}
