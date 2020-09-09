<?php

namespace App\Service\Mailer;

use App\Entity\User;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;



class MailerService
{
    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;

    }
    
    public function sendSignUpEmail(User $user)
    {
        
        $email = (new TemplatedEmail())
            ->from('noreply@miz-dara-shop.com')
            ->to(new Address($user->getEmail()))
            ->subject('Thanks for signing up!')
    
            // path of the Twig template to render
            ->htmlTemplate('emails/signup.html.twig')
    
            // pass variables (name => value) to the template
            ->context([
                'expiration_date' => new \DateTime('+7 days'),
                'username' => $user->getFirstName(),
            ]);
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                $this->mailer->send($email);
            }
            
    }
}
