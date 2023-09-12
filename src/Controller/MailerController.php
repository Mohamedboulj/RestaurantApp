<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailerController extends AbstractController
{
    #[Route('/contact', name: 'contact')]
    public function sendmail(MailerInterface $mailer, Request $request): Response
    {
        $formEmail = $this->createFormBuilder()
        ->add('email', TextType::class)
        ->add('message', TextareaType::class, ['attr' => array('rows' => '5')])
        ->add('Submit', SubmitType::class, ['attr' => array('class' => 'btn btn-outline-danger float-right','type' => 'button')])
        ->getForm();
        $formEmail->handleRequest($request);
        if ($formEmail->isSubmitted()) {
            $input = $formEmail->getData();
            $sender = $input['email'] ;
            $text = $input['message'] ;
            $email = (new TemplatedEmail())
            ->from($sender)
            ->to('med.blj93@gmail.com')
            ->subject('order')
            ->htmlTemplate('mailer/mail.html.twig')
            ->context(['message' => $text]) ;
            try {
                $mailer -> send($email);
                $this->addFlash('success', 'Message sent successfully.');
                return $this->redirectToRoute('contact');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('error', 'Something went wrong , please try again, ('.$e->getMessage() .')');
                return $this->redirectToRoute('contact');

            }
        }
        return $this->render('mailer/index.html.twig', [
            'emailForm' => $formEmail->createView()
        ]);

    }
}
