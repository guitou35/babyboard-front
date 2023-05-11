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
        $options = ['query' => [
            'groups[]' => 'read:item'
        ]];
        $response = $this->ChangeApiHttpClient->getChangesByUser($options);
        $listChanges = [];

        if ($response->getStatus() === 200) {
            $listChanges = $response->getHydraMember();
        }

        return $this->render('changes/index.html.twig', [
            'listChanges' => $listChanges,

        ]);
    }

    #[Route('/new', name: 'new_changes', methods: ['POST', 'GET'])]
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

    #[Route('/edit/{id}', name: 'edit_changes', methods: ['POST', 'GET'])]
    public function edit(Request $request): Response
    {
        $Change = $this->ChangeApiHttpClient->getChangesById($request->get('id'))->getItemContent();
        $Change['heure'] = new \Datetime($Change['heure']);
        $form =  $this->createForm(ChangeType::class, $Change);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $json = StructureType::setDatasChangesStructure($data);
            $response = $this->ChangeApiHttpClient->updateChanges(['json' => $json], $request->get('id'));

            if ($response->getStatus() === 200) {
                $this->addFlash('success', 'Enfant modifié avec succès');
                return $this->redirectToRoute('get_Changes');
            } else {
                $this->addFlash('error', 'Une erreur est survenue');
            }
        }

        return $this->render('changes/form.html.twig', [
            'form' => $form->createView(),
            'buttonController' => 'Modifier',
            'titleController' => 'Modifier un enfant'
        ]);
    }

    #[Route('/delete/{id}', name: 'delete_changes', methods: ['POST'])]
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
