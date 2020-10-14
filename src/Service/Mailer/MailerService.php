<?php

namespace App\Service\Mailer;

use App\Entity\Contact;
use App\Entity\User;
use App\Service\Cart\CartService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
    private $mailer;
    private $cartService;
    private $flashBagInterface;

    public function __construct(
        MailerInterface $mailer, 
    CartService $cartService,
    FlashBagInterface $flashBagInterface
    )
    {
        $this->mailer = $mailer;
        $this->cartService = $cartService;
        $this->flashBagInterface = $flashBagInterface;
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


    public function sendContactEmail(Contact $contact)
    {
        
        $email = (new Email())

            ->from('noreply@miz-dara-shop.com')
            ->to('josselinkrikorian@yahoo.fr')
            ->subject($contact->getSubject())

            // path of the Twig template to render
            ->text($contact->getContent())

            // pass variables (name => value) to the template
            ;
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
            $this->mailer->send($email);
            dump($email);

        }
    }

    public function sendPurchasePaymentConfirmation($user)
    {
        if (count($this->user->getPurchases()) > 0) {
            $paidPurchases = [];
            foreach ($user->getPurchases() as $purchase) {
                if ($purchase->getPaid()) {
                    $paidPurchases[] = $purchase;
                }
            }
            foreach($paidPurchases as $paidPurchase){
                if($paidPurchase === end($paidPurchases)){
                    $lastPaidPurchase = $paidPurchase;
                }
            }

            if($lastPaidPurchase){

            $this->cartService->setImages($lastPaidPurchase);
                $email = (new TemplatedEmail())
                ->from('noreply@miz-dara-shop.com')
                ->to(new Address($user->getEmail()))
                ->subject('Congratulation for your purchase!')
    
                // path of the Twig template to render
                ->htmlTemplate('emails/purchase_payment_confirmation.html.twig')
    
                // pass variables (name => value) to the template
                ->context([
                    'purchase' => $lastPaidPurchase,
                    'username' => $user->getFirstName(),
                ]);
            try {
                $this->mailer->send($email);
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                // $this->mailer->send($email);
                $this->flashBagInterface->add('danger', 'Something happednd your confirmation email could not be sent bur your purchase is registered');
            }
            }
        }
    }
}
