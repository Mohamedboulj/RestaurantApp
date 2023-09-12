<?php

namespace App\Controller;

use App\Entity\Table;
use App\Form\TableType;
use App\Repository\TableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tables', name: 'tables.')]
class TablesController extends AbstractController
{
    #[Route('/', name: 'show')]
    public function show(TableRepository $table): Response
    {
        $tables = $table->findAll() ;
        return $this->render('tables/index.html.twig', [
            'tables' => $tables,
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $table = new Table() ;
        $form = $this->createForm(TableType::class, $table) ;
        $form->handleRequest($request) ;
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($table) ;
            $em->flush() ;
            $this->addFlash('success', 'Table created with success !') ;
            return $this->redirect($this->generateUrl('tables.create'));
        }
        return $this->render('tables/create.html.twig', [
            'createForm' => $form->createView()
        ]);
    }
    #[Route('/delete/{id}', name:'delete')]
    public function delete($id, TableRepository $tableRep, EntityManagerInterface $em)
    {
        $table = $tableRep->find($id);
        dump($table);
        $em->remove($table);
        $em->flush();
        $this->addFlash('success', 'Table number '.$table->getNumber().' is deleted with success !') ;
        return $this->redirect($this->generateUrl('tables.show'));


    }
    #[Route('/update/{id},{status}', name:'update')]
    public function update(TableRepository $tableRep, EntityManagerInterface $em, $status, $id)
    {
        $table = $tableRep->find($id);
        $table->setStatus($status);
        $em->flush();
        return $this->redirectToRoute('tables.show');
    }
}
