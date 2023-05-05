<?php 

namespace App\Service\HttpClient\EntityHttpClient;

use App\Helpers\Response\HttpResponse;
use App\Service\HttpClient\AppHttpClient\AppApiHttpClient;

class UserApiHttpClient extends AppApiHttpClient
{   

    protected const USER_URI = self::PREFIX. '/users';

    public function getUserById(string $id, string $groups = null): HttpResponse
    {
        if ($groups) {
            $this->addDefaultOption(['query' => ['groups' => $groups]]);
        }
        return $this->doRequest(self::GET, self::USER_URI . '/' . $id);
    }

    public function createUser(array $data): HttpResponse
    {
        return $this->doRequest(self::POST, self::USER_URI, $data);
    }
}