<?php

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class SendEmailController extends AbstractController
{
    #[Route('/email/send')]
    public function sendEmail(MailerInterface $mailer): Response
    {
        $email = (new TemplatedEmail())
            ->from('nagasadoy@gmail.com')
            ->to('dimabor9@mail.ru')
            ->subject('Test mail')
            ->htmlTemplate('email/welcome.html.twig');

        $mailer->send($email);

        return $this->json([
           'message' => 'email send'
        ]);
    }
}