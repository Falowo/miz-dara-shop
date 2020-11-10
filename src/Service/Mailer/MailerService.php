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

    ) {
        $this->mailer = $mailer;
        $this->cartService = $cartService;
        $this->flashBagInterface = $flashBagInterface;
    }

    public function sendSignUpEmail(User $user, $authenticate)
    {
       

        $email = (new TemplatedEmail())
            ->from(new Address('noreply@miz-dara-shop.com', 'Miz Dara Unique'))
            ->to(new Address($user->getEmail()))
            ->subject('Thanks for signing up!')

            // path of the Twig template to render
            ->htmlTemplate('emails/signup.html.twig')

            // pass variables (name => value) to the template
            ->context([
                'authenticate' => $authenticate,
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

            ->from(new Address($contact->getEmail(), 'Miz Dara Client'))
            ->to('josselinkrikorian@yahoo.fr')
            ->subject($contact->getSubject())

            // path of the Twig template to render
            ->text($contact->getContent())

            // pass variables (name => value) to the template
        ;
        try {
            if ($this->mailer->send($email)){
                $this->flashBagInterface->add('succes', 'Your message has been sent');
                }
        } catch (TransportExceptionInterface $e) {
            // some error prevented the email sending; display an
            // error message or try to resend the message
            try {
                if ($this->mailer->send($email)){
                $this->flashBagInterface->add('succes', 'Your message has been sent');
                }
            } catch (TransportExceptionInterface $e) {
                // some error prevented the email sending; display an
                // error message or try to resend the message
                $this->flashBagInterface->add('error', 'Something happend, your message has not been sent');

               
            }
           
           
        }
    }

    public function sendPurchasePaymentConfirmation($user)
    {
        if ((count($user->getPurchases())) > 0) {
            $paidPurchases = [];
            foreach ($user->getPurchases() as $purchase) {
                if ($purchase->getPaid()) {
                    $paidPurchases[] = $purchase;
                }
            }
            foreach ($paidPurchases as $paidPurchase) {
                if ($paidPurchase === end($paidPurchases)) {
                    $lastPaidPurchase = $paidPurchase;
                }
            }

            if ($lastPaidPurchase) {

                $total = $this->cartService->getTotalPurchaseLines($lastPaidPurchase);


                if ($purchase->getDeliveryPrice()) {
                    $total += $purchase->getDeliveryPrice();
                }


                $this->cartService->setImages($lastPaidPurchase);

                $email = (new TemplatedEmail())
                    ->from(new Address('noreply@miz-dara-shop.com', 'Miz Dara Shop'))
                    ->to(new Address($user->getEmail()))
                    ->subject('Congratulation for your purchase!')

                    // path of the Twig template to render
                    ->htmlTemplate('emails/purchase_payment_confirmation.html.twig')

                    // pass variables (name => value) to the template
                    ->context([
                        'purchase' => $lastPaidPurchase,
                        'username' => $user->getFirstName(),
                        'total' => $total,
                    ]);
                try {
                    if($this->mailer->send($email)){

                        $this->flashBagInterface->add('success', 'A confirmation email has been
                         sent to' . $user->getEmail());
                    }

                } catch (TransportExceptionInterface $e) {
                    // some error prevented the email sending; display an
                    // error message or try to resend the message
                    // $this->mailer->send($email);
                    try {
                        if($this->mailer->send($email)){

                            $this->flashBagInterface->add('success', 'A confirmation email has been
                             sent to' . $user->getEmail());
                        }
    
                    } catch (TransportExceptionInterface $e) {
                        // some error prevented the email sending; display an
                        // error message or try to resend the message


                        
                            $this->flashBagInterface->add('error', 'Something happend, your confirmation email could not be sent but your purchase is registered');

                    

                    }
                }
            }
        }
    }
}
