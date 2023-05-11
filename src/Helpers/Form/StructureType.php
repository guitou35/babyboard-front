<?php

namespace App\Helpers\Form;

use DateTime;

class StructureType 
{

    public static function getRepasStructure(array $responseRepas): array
    {
        $repas['repasTime'] = $responseRepas['repasTime'];
        $repas['alimentName'] = $responseRepas['alimentName'];
        $repas['quantity'] = $responseRepas['quantity'];
        $repas['commentaire'] = $responseRepas['commentaire'];
        $repas['repasAt'] = new Datetime($responseRepas['repasAt']);
        return $repas;
    }

    public static function setDatasChangesStructure(array $responseChanges): array
    {
        return [
            'children' => $responseChanges['children'],
            'type' => $responseChanges['type'],
            'heure' => $responseChanges['heure']->format("Y-m-d H:i:s"),
            'products' => $responseChanges['products'],
            'contenu' => $responseChanges['contenu'],
            'problems' => $responseChanges['problems'],
            'commentaire' => $responseChanges['commentaire']
        ];
    }

}