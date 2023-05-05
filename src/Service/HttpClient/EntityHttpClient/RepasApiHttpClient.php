<?php 

namespace App\Service\HttpClient\EntityHttpClient;

use App\Helpers\Response\HttpResponse;
use App\Service\HttpClient\AppHttpClient\AppApiHttpClient;

class RepasApiHttpClient extends AppApiHttpClient
{   

    protected const REPAS_URI = self::PREFIX. '/repas';

    public function getRepasByChildren( string $id, array $groups = []): HttpResponse
    {
        return $this->doRequest(self::GET, self::PREFIX . '/childrens/' . $id . '/repas', $groups );
    }

    public function getAllRepasByUsers($groups = []): HttpResponse
    {

        $user = $this->getUser();

        return $this->doRequest(self::GET, self::PREFIX . '/users/' .$user->getId() . '/repas', $groups );
    }

    public function createRepas(array $data): HttpResponse
    {
        return $this->doRequest(self::POST, self::REPAS_URI, $data);
    }

    public function getRepasById(string $id, array $groups = null): HttpResponse
    {
        return $this->doRequest(self::GET, self::REPAS_URI . '/' . $id, $groups);
    }

    public function updateRepasById(array $data, string $id): HttpResponse
    {
        return $this->doRequest(self::PUT, self::REPAS_URI . '/' . $id, $data);
    }

    public function deleteRepasById(string $id): HttpResponse
    {
        return $this->doRequest(self::DELETE, self::REPAS_URI . '/' . $id);
    }
}