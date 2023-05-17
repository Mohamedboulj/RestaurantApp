<?php

namespace App\Controller;

use App\Entity\SaleDetails;
use App\Entity\Sales;
use App\Repository\CategoryRepository;
use App\Repository\DishRepository;
use App\Repository\SalesRepository;
use App\Repository\TableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

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
            '<button class="btn btn-light btn-table " data-id="'.$table->getId().'" data-name="Table N°'.$table->getNumber().'">
            
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
                <a class="btn btn-outline-light btn-menu curs-ptr" data-name="'.$menu->getName().'" data-id="'.$menu->getId().'">
                    <img class="img-fluid" src="'.$pic.'">
                    <br>
                    '.$menu->getName().'
                    <br>
                    $'.number_format($menu->getPrice(),2).'
                </a>
            </div>';
        }
        
        return new Response($html) ;

    }
    #[Route('/orderFood', name: 'orderFood')]
    public function orderFood(Request $req ,EntityManagerInterface $em,DishRepository $dishRepository ,TableRepository $tableRep , Security $security): Response
    {
        $menu_id = $req->get('menu_id') ;
        $menu = $dishRepository->find($menu_id) ;
        $table_id = $req->get('table_id');
        $table_name = $req->get('table_name');
        $saleRep = $em->getRepository(Sales::class);
        $sale = $saleRep->findOneBy(['table_id' => $table_id,'sale_status' => 'unpaid']);
        
        if (!$sale){
            $user = $security->getUser();
            $user_id =$user->getId() ;
            $sale = new Sales();
            $sale->setTableId($table_id);
            $sale->setTableName($table_name);
            $sale->setUserId($user_id);
            $sale->setUserName($user->getUsername());
            $sale->setCreatedAt(new \DateTime());
            $em -> persist($sale);
            $em -> flush();
            $sale_id = $sale->getId() ;
            $table = $tableRep->find($table_id) ;
            $table->setStatus('Not available');
            $em -> persist($table);
            $em -> flush();
            
        }
        else
        {
            $sale_id = $sale->getId();
            
        }
        // add ordered menu to the sale_details table

            $saleDetail = new SaleDetails(); 
            $saleDetail->setSaleId($sale_id);
            $saleDetail->setMenuId($menu_id);
            $saleDetail->setMenuName($menu->getName());
            $saleDetail->setMenuPrice($menu->getPrice());
            $saleDetail->setQuantity($req->get('quantity'));
            $saleDetail->setCreatedAt(new \DateTime()) ;
            $saleDetail->setEditedAt(new \DateTimeImmutable());
            $em -> persist($saleDetail);
            $em -> flush();
        //update total price in the sales table
            $tp = $sale->getTotalPrice() + $req->get('quantity') * $menu->getPrice() ;
            $sale->setTotalPrice($tp);
            $em -> persist($sale);
            $em -> flush();
        //return html to view
            $html = $this->getSaleDetails($sale_id , $em)->getContent() ;
            return new Response( $html ) ;
            
        
    }
    #[Route('/getSaleDetailsByTable/{table_id}', name:'SaleDetailsByTable')]
    public function getSaleDetailsByTable($table_id , EntityManagerInterface $em ){
        $saleRep = $em->getRepository(Sales::class);
        $sale = $saleRep->findOneBy(['table_id' => $table_id,'sale_status' => 'unpaid']);
        $html = "" ;
        if($sale){
            $sale_id = $sale->getId();
            $html .= $this->getSaleDetails($sale_id, $em)->getContent() ;
        }else{
            $html .= "<h5> No sales found on the selected table </h5>  " ;
        }

        return new Response( $html ) ;
    }

    private function getSaleDetails($sale_id,EntityManagerInterface $em ){
        //list all sale details
        $saleRep = $em->getRepository(Sales::class);
        $saleDetails = $em->getRepository(SaleDetails::class)->findBy(['sale_id' => $sale_id]);
        $html = "" ;
            $html .= '<div class="table-responsive-md" style="overflow-y:scroll; height: 400px; border: 1px solid #343A40">
            <table class="table table-primary ">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Menu</th>
                    <th scope="col">Quantity</th>
                    <th scope="col">Price</th>
                    <th scope="col">Total</th>
                    <th scope="col">Status</th>
                </tr>
            </thead>
            <tbody>';
            $showBtnPayment = true;
            foreach($saleDetails as $saleDetail){
            
                $html .= '
                <tr>
                    <td>'.$saleDetail->getMenuId().'</td>
                    <td>'.$saleDetail->getMenuName().'</td>
                    <td>'.$saleDetail->getQuantity().'</td>
                    <td>'.$saleDetail->getMenuPrice().'</td>
                    <td>'.($saleDetail->getMenuPrice() * $saleDetail->getQuantity()).'</td>';
                    if($saleDetail->getStatus() == "Not Confirmed"){
                        $showBtnPayment = false;
                        $html .= '<td><a data-id="'.$saleDetail->getMenuId().'" class="btn btn-danger btn-delete-saledetail"><i class="fa fa-trash-o" aria-hidden="true"></i>'.$saleDetail->getStatus().'</a></td>';
                    }else{ // status == "confirm"
                        $html .= '<td><i class="fa fa-check-circle-o" aria-hidden="true"></i></td>';
                    }
                $html .= '</tr>';
            }
            $html .='</tbody></table></div>';

            $sale = $saleRep->find($sale_id);
            $html .= '<hr>';
            $html .= '<h3>Total Amount: $'.number_format($sale->getTotalPrice(),2).'</h3>';

            if($showBtnPayment){
                $html .= '<button data-id="'.$sale_id.'" data-totalAmount="'.$sale->getTotalPrice().'" class="btn btn-success btn-block btn-payment my-3 rounded-pill" data-toggle="modal" data-target="#exampleModal">Payment</button>';
            }else{
                $html .= '<button data-id="'.$sale_id.'" class="btn btn-info btn-block btn-confirm-order">Confirm Order</button>';
            }

            return new Response($html) ;
    }
    #[Route('/ConfirmOrder',name:'confirmorder')]
    public function confirmOrder(Request $req , EntityManagerInterface $em){
        
        $sale_id = $req->get('SALE_ID');
        $saleRep = $em->getRepository(SaleDetails::class);
        $sale = $saleRep->findBy(['sale_id' => $sale_id , 'status'=>'Not Confirmed']);
        foreach ($sale as $s) {
            $s->setStatus('Confirmed');
            $em ->persist($s) ;
            $em ->flush();
        }
        $html = $this->getSaleDetails($sale_id , $em)->getContent() ;
        return new Response( $html ) ;

    }
}
