<?php

namespace App\Controller;

use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Service\HttpClient\EntityHttpClient\UserApiHttpClient;
use App\Entity\User;

class SecurityController extends AbstractController
{
    public function __construct(private UserApiHttpClient $userApiHttpClient)
    {
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

         if ($this->getUser()) {
             return $this->redirectToRoute('app_home');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if($error){
            $this->addFlash('error', 'Mot de passe ou email incorrect');
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/register', name: 'app_register')]
    public function register(Request $request): Response
    {
        $form = $this->createForm(UserType::class, new User());

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $user = $form->getData();

            $json = [
                'email' => $user->getEmail(),
                'plainPassword' => $user->getPlainPassword(),
                'roles' => $user->getRoles(),
                'name' => $user->getName(),
                'lastName' => $user->getLastName(),
                'phone' => $user->getPhone(),
                'isNounou' => $user->getIsNounou()
            ];

            //send user to the api to be created
            $response = $this->userApiHttpClient->createUser(['json' => $json]);
            $redirect = 'app_home';

            if($response->getStatus() === 201){
                $this->addFlash('success', 'User created successfully');
            } else {
                $this->addFlash('error', 'User could not be created');
                $redirect = 'app_register';
            }
            return $this->redirectToRoute($redirect);
        }

        return $this->render('security/register.html.twig', [
            'form' => $form
        ]);
        
    }
}
