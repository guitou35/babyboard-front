<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\HttpClient\EntityHttpClient\ChildrenApiHttpClient;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ChildrenType;

class ChildrenController extends AbstractController
{
    public function __construct(private ChildrenApiHttpClient $childrenApiHttpClient)
    {
    }

    #[Route('/childrens', name: 'get_childrens', methods:['GET'])]
    public function index(): Response
    {
        $response = $this->childrenApiHttpClient->getChildrensByUser();
        $childrens = [];

        if($response->getStatus() === 200) {
            $childrens = $response->getHydraMember();
        }

        return $this->render('childrens/index.html.twig', [
            'childrens' => $childrens,  
                      
        ]);
    }

    #[Route('/childrens/new', name: 'new_childrens', methods:['POST','GET'])]
    public function new(Request $request): Response
    {
        $children = [];
        $form =  $this->createForm(ChildrenType::class, $children);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $json = [
                'name' => $data['name'],
                'birthdate' => $data['birthdate']->format('Y-m-d'),
                'weight' => $data['weight'],
                'size' => $data['size']
            ];
            $response = $this->childrenApiHttpClient->createChildrens(['json' => $json]);

            if($response->getStatus() === 201) {
                $this->addFlash('success', 'Enfant ajouté avec succès');
                return $this->redirectToRoute('get_childrens');
            }else{
                $this->addFlash('error', 'Une erreur est survenue');
            }
        }

        return $this->render('childrens/forms/form.html.twig', [
            'form' => $form->createView(),
            'buttonController' => 'Ajouter',
            'titleController' => 'Ajouter un enfant'
        ]);
    }

    #[Route('/childrens/edit/{id}', name: 'edit_childrens', methods:['POST','GET'])]
    public function edit(Request $request): Response
    {
        $children = $this->childrenApiHttpClient->getChildrensById($request->get('id'))->getItemContent();
        $children['birthdate'] = new \Datetime($children['birthdate']);
        $form =  $this->createForm(ChildrenType::class, $children);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $json = [
                'name' => $data['name'],
                'birthdate' => $data['birthdate']->format('Y-m-d'),
                'weight' => $data['weight'],
                'size' => $data['size']
            ];
            $response = $this->childrenApiHttpClient->updateChildrens(['json' => $json], $request->get('id'));

            if($response->getStatus() === 200) {
                $this->addFlash('success', 'Enfant modifié avec succès');
                return $this->redirectToRoute('get_childrens');
            }else{
                $this->addFlash('error', 'Une erreur est survenue');
            }
        }

        return $this->render('childrens/forms/form.html.twig', [
            'form' => $form->createView(),
            'buttonController' => 'Modifier',
            'titleController' => 'Modifier un enfant'
        ]);
    }

    #[Route('/childrens/delete/{id}', name: 'delete_childrens', methods:['POST'])]
    public function delete(Request $request): Response
    {
        $response = $this->childrenApiHttpClient->deleteChildrensById($request->get('id'));

        if($response->getStatus() === 204) {
            $this->addFlash('success', 'Enfant supprimé avec succès');
        }else{
            $this->addFlash('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('get_childrens');
    }
}
