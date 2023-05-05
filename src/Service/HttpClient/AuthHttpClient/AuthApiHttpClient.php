<?php

namespace App\Service\HttpClient\AuthHttpClient;

use Exception;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Service\HttpClient\BaseHttpClient;
use App\Helpers\EnvVar;

class AuthApiHttpClient extends BaseHttpClient
{

    protected const AUTH_METHOD = 'POST';
    protected const AUTH_URI = self::PREFIX. '/login_check';
    protected const REFRESH_PATH = self::PREFIX. '/token/refresh';

    public function __construct()
    {
        parent::__construct(EnvVar::get('AUTH_API_URL'));
    
    }

    public function doRequest(string $method, string $uri, array $options = []): mixed
    {
        try {
            $request =  $this->httpClient->request($method, $uri, $options);
            $status = $request->getStatusCode();
            $responseBody = $request->toArray();
        } catch (Exception|TransportExceptionInterface $error) {
            if($error->getCode() == 401 || $error->getCode() == 403){
                $exceptionResponse = $error->getResponse()->getContent(false);
                $exceptionMessage = json_decode($exceptionResponse, true);

                throw new CustomUserMessageAuthenticationException($exceptionMessage['message'], [], $error->getCode());
            }
            throw new CustomUserMessageAuthenticationException("Erreur de notre serveur d'authentification, veuillez ré-essayer plus tard.", [], 401);
        }
        return $responseBody;
    }

    public function authenticationRequest(string $username, string $password): array
    {
        $options = array_merge_recursive($this->defaultOptions, [
            "json"=> [
                "username" => $username,
                "password" => $password
            ]
            ]);

        return $this->doRequest(self::AUTH_METHOD, self::AUTH_URI, $options);
    }

        /**
     * Get a token with a refresh token
     * @param string $refreshToken
     * @return array
     */
    public function refreshTokenRequest(string $refreshToken): array
    {
        $options = array_merge_recursive($this->defaultOptions,[
            'json' => [
                'refresh_token'=> $refreshToken
            ]
        ]);

        return $this->doRequest(self::AUTH_METHOD, self::REFRESH_PATH, $options);
    }

}