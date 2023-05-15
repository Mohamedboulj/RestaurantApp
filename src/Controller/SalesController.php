<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\DishRepository;
use App\Repository\TableRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;

#[Route('/sales', name: 'sales.')]
class SalesController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRep): Response
    {
        $categories = $categoryRep->findAll() ;
        return $this->render('sales/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    #[Route('/getTables', name: 'tables')]
    public function alltables(TableRepository $tableRepository): Response{
        $package = new Package(new EmptyVersionStrategy());
        $pic = $package->getUrl('/pictures/table.png') ;
        $tables = $tableRepository->findAll() ;
        $html = " " ;
        foreach ($tables as $table) {
            $html .= '<div class="col-md-3 mb-4">';
            $html .= 
            '<button class="btn btn-light btn-table " data-id="'.$table->getNumber().'" data-name="'.$table->getNumber().'">
            
            <img class="img-fluid" src="'.$pic.'"/>
            <br>';
            if($table->getStatus() == "Available"){
                $html .= '<span class="badge badge-success">Table N° '.$table->getNumber().'</span>';
            }else{ // a table is not available
                $html .= '<span class="badge badge-danger">Table N° '.$table->getNumber().'</span>';
            }
            $html .='</button>';
            $html .= '</div>';
        }
        return new Response($html) ;
    }

    #[Route('/getMenu/{id}', name: 'getmenu')]
    public function getmenu($id , DishRepository $dishRep): Response{
        $package = new Package(new EmptyVersionStrategy());
        
        $menus = $dishRep->findByCategory($id) ;
        $html = "" ;
        foreach($menus as $menu){
            $pic = $package->getUrl('/pictures/'.$menu->getPicture()) ;
            $html .= 
            '<div class="col-md-3 text-center">
                <a class="btn btn-outline-light btn-menu" data-id="'.$menu->getId().'">
                    <img class="img-fluid" src="'.$pic.'">
                    <br>
                    '.$menu->getName().'
                    <br>
                    $'.number_format($menu->getPrice()).'
                </a>
            </div>';
        }
        
        return new Response($html) ;

    }
    
}
