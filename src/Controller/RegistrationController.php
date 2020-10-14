<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Service\Mailer\MailerService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class RegistrationController extends AbstractController
{

    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        GuardAuthenticatorHandler $guardHandler,
        LoginFormAuthenticator $authenticator,
        MailerService $mailerService
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            //  send an email
           $mailerService->sendSignUpEmail($user);

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }


    /**
     * Undocumented function
     *
     * @param User $user
     * @Route("/confirm_email/{email}/{token}", name="app_confirmEmail")
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param DeleteMemberResponder     $responder
     * @param Request                   $request
     * 
     * @return Response
     *
     * @throws InvalidCsrfTokenException
     */
    public function confirmEmail(
     string   $email,
     UserRepository $userRepository,
     CsrfTokenManagerInterface $csrfTokenManager,
     Request $request): Response
    {
         // "confirm_email" must be a unique token id for session storage inside application
        $token = new CsrfToken('confirm_email', $request->attributes->get('token'));

        // Action is stopped since token is not allowed!
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException('CSRF Token is not valid.');
        }

        

        $user = $userRepository->findOneBy([
            'email' => $email
        ]);



        if($user){           
                $user->setConfirmedEmail(true);
                $em = $this->getDoctrine()->getManager();
                $em->flush();
                return $this->redirectToRoute('app_index');
           }
        
        else{
            // return $this->redirectToRoute('app_register');
            return $this->render('registration/confirmEmailFailed.html.twig', [
                'user' => $user,
            ]);
        }


    }

}
