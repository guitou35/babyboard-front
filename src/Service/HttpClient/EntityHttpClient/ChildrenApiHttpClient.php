<?php 

namespace App\Service\HttpClient\EntityHttpClient;

use App\Helpers\Response\HttpResponse;
use App\Service\HttpClient\AppHttpClient\AppApiHttpClient;

class ChildrenApiHttpClient extends AppApiHttpClient
{   

    protected const CHILDREN_URI = self::PREFIX. '/childrens';

    public function getChildrensByUser( string $groups = null): HttpResponse
    {
        $user = $this->getUser();

        return $this->doRequest(self::GET, self::PREFIX . '/users/' . $user->getId() . '/childrens' );
    }

    public function getChildrensById($id, string $groups = null): HttpResponse
    {
        return $this->doRequest(self::GET, self::CHILDREN_URI . '/' . $id );
    }


    public function updateChildrens(array $data, string $id): HttpResponse
    {
        return $this->doRequest(self::PUT, self::CHILDREN_URI . '/' . $id, $data);
    }

    public function deleteChildrensById(string $id): HttpResponse
    {
        return $this->doRequest(self::DELETE, self::CHILDREN_URI . '/' . $id);
    }

    public function createChildrens(array $data): HttpResponse
    {
        return $this->doRequest(self::POST, self::CHILDREN_URI, $data);
    }
}