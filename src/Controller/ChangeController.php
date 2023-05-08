<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\HttpClient\EntityHttpClient\ChangeApiHttpClient;
use Symfony\Component\HttpFoundation\Request;
use App\Form\ChangeType;
use App\Helpers\Form\StructureType;
use App\Service\HttpClient\EntityHttpClient\ChildrenApiHttpClient;

#[Route('/changes')]
class ChangeController extends AbstractController
{
    public function __construct(private ChangeApiHttpClient $ChangeApiHttpClient, private ChildrenApiHttpClient $childrenApiHttpClient)
    {
    }

    #[Route('/', name: 'get_Changes', methods: ['GET'])]
    public function index(): Response
    {
        $response = $this->ChangeApiHttpClient->getChangesByUser();
        $Changes = [];

        if ($response->getStatus() === 200) {
            $Changes = $response->getHydraMember();
        }

        return $this->render('Changes/index.html.twig', [
            'Changes' => $Changes,

        ]);
    }

    #[Route('/new', name: 'new_Changes', methods: ['POST', 'GET'])]
    public function new(Request $request): Response
    {
        $Change = [];
        $children = $this->childrenApiHttpClient->getChildrensByUser()->getHydraMember();
        $choices = [];
        $choices["Choisi un enfant"] = null;
        foreach($children as $child) {
            $choices[$child["name"]] = $child['@id'];
        }
        $form =  $this->createForm(ChangeType::class, $Change, ['children' => $choices]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $json = StructureType::setDatasChangesStructure($data);
            $response = $this->ChangeApiHttpClient->createChanges(['json' => $json]);

            if ($response->getStatus() === 201) {
                $this->addFlash('success', 'Le change a été ajouté avec succès');
                return $this->redirectToRoute('get_Changes');
            } else {
                $this->addFlash('error', 'Une erreur est survenue');
            }
        }

        return $this->render('changes/form.html.twig', [
            'form' => $form->createView(),
            'buttonController' => 'Ajouter',
            'titleController' => 'Ajouter un change'
        ]);
    }

    #[Route('/Changes/edit/{id}', name: 'edit_Changes', methods: ['POST', 'GET'])]
    public function edit(Request $request): Response
    {
        $Change = $this->ChangeApiHttpClient->getChangesById($request->get('id'))->getItemContent();
        $Change['birthdate'] = new \Datetime($Change['birthdate']);
        $form =  $this->createForm(ChangeType::class, $Change);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $json = [
                'name' => $data['name'],
                'birthdate' => $data['birthdate']->format('Y-m-d'),
                'weight' => $data['weight'],
                'size' => $data['size']
            ];
            $response = $this->ChangeApiHttpClient->updateChanges(['json' => $json], $request->get('id'));

            if ($response->getStatus() === 200) {
                $this->addFlash('success', 'Enfant modifié avec succès');
                return $this->redirectToRoute('get_Changes');
            } else {
                $this->addFlash('error', 'Une erreur est survenue');
            }
        }

        return $this->render('Changes/forms/form.html.twig', [
            'form' => $form->createView(),
            'buttonController' => 'Modifier',
            'titleController' => 'Modifier un enfant'
        ]);
    }

    #[Route('/Changes/delete/{id}', name: 'delete_Changes', methods: ['POST'])]
    public function delete(Request $request): Response
    {
        $response = $this->ChangeApiHttpClient->deleteChangesById($request->get('id'));

        if ($response->getStatus() === 204) {
            $this->addFlash('success', 'Enfant supprimé avec succès');
        } else {
            $this->addFlash('error', 'Une erreur est survenue');
        }

        return $this->redirectToRoute('get_Changes');
    }
}
