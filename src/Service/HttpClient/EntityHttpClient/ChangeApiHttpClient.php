<?php 

namespace App\Service\HttpClient\EntityHttpClient;

use App\Helpers\Response\HttpResponse;
use App\Service\HttpClient\AppHttpClient\AppApiHttpClient;

class ChangeApiHttpClient extends AppApiHttpClient
{   

    protected const CHANGE_URI = self::PREFIX. '/Changes';

    public function getChangesByUser( string $groups = null): HttpResponse
    {
        $user = $this->getUser();

        return $this->doRequest(self::GET, self::PREFIX . '/users/' . $user->getId() . '/Changes' );
    }

    public function getChangesById($id, string $groups = null): HttpResponse
    {
        return $this->doRequest(self::GET, self::CHANGE_URI . '/' . $id );
    }


    public function updateChanges(array $data, string $id): HttpResponse
    {
        return $this->doRequest(self::PUT, self::CHANGE_URI . '/' . $id, $data);
    }

    public function deleteChangesById(string $id): HttpResponse
    {
        return $this->doRequest(self::DELETE, self::CHANGE_URI . '/' . $id);
    }

    public function createChanges(array $data): HttpResponse
    {
        return $this->doRequest(self::POST, self::CHANGE_URI, $data);
    }
}