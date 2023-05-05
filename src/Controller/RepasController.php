<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\HttpClient\EntityHttpClient\RepasApiHttpClient;
use App\Service\HttpClient\EntityHttpClient\ChildrenApiHttpClient;
use App\Form\RepasType;
use App\Helpers\Form\StructureType;

#[Route('/repas')]
class RepasController extends AbstractController
{

    public function __construct(private ChildrenApiHttpClient $childrenApiHttpClient, private RepasApiHttpClient $repasApiHttpClient)
    {
    }

    #[Route('/', name: 'index_repas')] 
    public function index(): Response
    {
        $options = ['query' => [
            'groups[]' => 'read:item'
        ]];
        $response = $this->repasApiHttpClient->getAllRepasByUsers($options);
        $repas = [];

        if($response->getStatus() === 200) {
            $repas = $response->getHydraMember();
        }
        return $this->render('repas/index.html.twig', [
            'listRepas' => $repas,
        ]);
    }

    #[Route('/new', name: 'new_repas')]
    public function new(Request $request): Response
    {
        $repas = [];
        $children = $this->childrenApiHttpClient->getChildrensByUser()->getHydraMember();
        $choices = [];
        $choices["Choisi un enfant"] = null;
        foreach($children as $child) {
            $choices[$child["name"]] = $child['@id'];
        }
        $form =  $this->createForm(RepasType::class, $repas, ['children' => $choices]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $json = [
                'children' =>  $data['children'],
                'repasTime' => $data['repasTime'],
                'alimentName' => $data['alimentName'],
                'quantity' => $data['quantity'],
                'commentaire' => $data['commentaire'],
                'repasAt' => $data['repasAt']->format('Y-m-d H:i:s')
            ];

            $response = $this->repasApiHttpClient->createRepas(['json' => $json]);

            if($response->getStatus() === 201) {
                $this->addFlash('success', 'Repas ajouté avec succès');
                return $this->redirectToRoute('index_repas');
            }else{
                $this->addFlash('error', 'Une erreur est survenue');
            }
        }

        return $this->render('repas/form.html.twig', [
            'form' => $form->createView(),
            'titleController' => 'Ajouter un repas',
            'buttonController' => 'Ajouter'  
        ]);
    }

    #[Route('/childrens/{id}', name: 'get_repas')]
    public function getRepasByUser(Request $request): Response
    {
        $response = $this->repasApiHttpClient->getRepasByChildren($request->get('id'));
        $repas = [];

        if($response->getStatus() === 200) {
            $repas = $response->getHydraMember();
        }

        return $this->render('repas/index.html.twig', [
            'repas' => $repas
        ]);
    } 

    #[Route('/edit/{id}', name: 'edit_repas')]
    public function edit(Request $request): Response
    {
        $options = ['query' => [
            'groups[]' => 'read:item'
        ]];
        $repas = $this->repasApiHttpClient->getRepasById($request->get('id'), $options)->getItemContent();
        $children = [$repas['children']['name'] => $repas['children']['@id']];
        $repas = StructureType::getRepasStructure($repas);

        $form =  $this->createForm(RepasType::class, $repas, ['children' => $children]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $json = [
                'children' => $data['children'],
                'repasTime' => $data['repasTime'],
                'alimentName' => $data['alimentName'],
                'quantity' => $data['quantity'],
                'commentaire' => $data['commentaire'],
                'repasAt' => $data['repasAt']->format('Y-m-d H:i:s')
            ];

            $response = $this->repasApiHttpClient->updateRepasById(['json' => $json], $request->get('id'));

            if($response->getStatus() === 200) {
                $this->addFlash('success', 'Repas modifié avec succès');
                return $this->redirectToRoute('index_repas');
            }else{
                $this->addFlash('error', 'Une erreur est survenue');
            }
        }

        return $this->render('repas/form.html.twig', [
            'form' => $form->createView(),
            'titleController' => 'Modifier un repas',
            'buttonController' => 'Modifier'  
        ]);
    }

    #[Route('/delete', name: 'delete_repas')]
    public function delete(Request $request): Response
    {
        $response = $this->repasApiHttpClient->deleteRepasById($request->get('id'));

        if($response->getStatus() === 204) {
            $this->addFlash('success', 'Repas supprimé avec succès');
        }else{
            $this->addFlash('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('index_repas');
    }
}
