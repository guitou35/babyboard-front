<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GestionController extends AbstractController
{
    public const GESTION = [
        'Repas' => [
            'name' => 'Ajouter un repas',
            'route' => 'new_repas',
            'icon' => 'fa-solid fa-utensils fa-2x',
            'description' => 'Ajouter un repas à un enfant, qui peut être un biberon ou un repas solide',
        ],
        'Change' => [
            'name' => 'Ajouter un change',
            'route' => 'new_Changes',
            'icon' => 'fa-solid fa-toilet fa-2x',
            'description' => 'Ajouter un change à un enfant, qui peut être un change de couche ou un change de vêtements',
        ],
        'Sleep' => [
            'name' => 'Ajouter un dodo',
            'route' => 'get_childrens',
            'icon' => 'fa-solid fa-bed fa-2x',
            'description' => 'Ajouter un dodo à un enfant',
        ],
    ];

    #[Route('/gestion', name: 'app_gestion')]
    public function index(): Response
    {
        return $this->render('gestion/index.html.twig', [
            'controller_name' => 'HomeController',
            'gestions' => self::GESTION,
        ]);
    }
}
