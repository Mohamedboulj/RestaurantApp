<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordhasher, EntityManagerInterface $registry, UserRepository $usersRepository): Response
    {
        $regform = $this->createFormBuilder()
        ->add('username', TextType::class, ['label' => 'Employee'])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Repeat password']
        ])
        ->add('Role', ChoiceType::class, ['choices' => ['ROLE_WAITER' => 'ROLE_WAITER','ROLE_ADMIN' => 'ROLE_ADMIN'],'attr' => ['class' => 'form-control'],'required' => true])
        ->add('Save', SubmitType::class, ['attr' => array('class' => 'btn btn-outline-primary float-right')])
        ->getForm()
        ;
        $regform->handleRequest($request);
        if($regform->isSubmitted()) {
            $input = $regform->getData();
            $user = new User();
            $user->setUsername($input['username']);
            $user->setPassword(
                $passwordhasher
                ->hashPassword($user, $input['password'])
            );
            $role_arr = explode(',', $input['Role']);
            $user->setRoles($role_arr);
            $registry->persist($user);
            $registry->flush();
            return $this->redirect($this->generateUrl('register'));
        }
        $users = $usersRepository->findAll();
        return $this->render('register/index.html.twig', [
            'regform' => $regform->createView(),
            'users' => $users ,
        ]);
    }
    #[Route("/delete_user/{id}", name:"delete_user")]
    public function delete_user($id, UserRepository $usersRepository, EntityManagerInterface $em)
    {
        $user = $usersRepository->find($id);
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'Waiter has been deleted with success');
        return $this->redirect($this->generateUrl('register'));

    }
}
