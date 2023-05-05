<?php

namespace App\Service\HttpClient\AppHttpClient;

use App\Service\HttpClient\BaseHttpClient;
use App\Helpers\EnvVar;
use App\Helpers\Response\HttpResponse;
use App\Service\HttpClient\AuthHttpClient\AuthApiHttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Error;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Entity\User;

class AppApiHttpClient extends BaseHttpClient
{

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const PATCH = 'PATCH';
    const DELETE = 'DELETE';
    const HYDRA_MEMBER = "hydra:member";

    public function __construct(protected RequestStack $requestStack, private AuthApiHttpClient $authApi, private Security $security)
    {
        parent::__construct(EnvVar::get('APP_API_URL'));
    }

    public function doRequest(string $method, string $uri, array $options = []): HttpResponse
    {
        $content = null;
        $status = null;
        $isError = false;
        $message = null;
        try {
            $options = array_merge_recursive($this->defaultOptions, $options);
            $options['headers']['Authorization'] = $this->getAuthorizationHeader();
            $request =  $this->httpClient->request($method, $uri, $options);
            $status = $request->getStatusCode();
            $content = json_decode($request->getContent(), true);
        } catch (Error | TransportExceptionInterface $error) {
            if ($error->getCode() == 401 || $error->getCode() == 403) {
                $exceptionResponse = $error->getResponse()->getContent(false);
                $exceptionMessage = json_decode($exceptionResponse, true);

                throw new CustomUserMessageAuthenticationException($exceptionMessage['error'], [], $error->getCode());
            }
            throw new CustomUserMessageAuthenticationException("Erreur de notre serveur d'authentification, veuillez ré-essayer plus tard.");
        }
        return new HttpResponse($status, $content, $isError, $message);
    }

    public function getExpirationTokenTimeStamp(?string $jwtToken): ?int
    {
        if(!$jwtToken){
            return null;
        }

        $tokenParts = explode('.', $jwtToken);
        $jwtPayload = json_decode(base64_decode($tokenParts[1]),true);

        return array_key_exists('exp', $jwtPayload) ? $jwtPayload["exp"] : null;
    }

    public function getAuthorizationHeader(): string
    {
        if ($accessToken = $this->requestStack->getSession()->get('accessToken')) {
            $dateNow = time();
            $expiredTokenTimestamp = $this->getExpirationTokenTimeStamp($accessToken);
            // Request a new token if the validity of the current token is approaching the expiration date (< N minutes)
            if ($dateNow > $expiredTokenTimestamp) {
                if(ceil(($dateNow - $expiredTokenTimestamp) / 3600) >= 24){
                    throw new CustomUserMessageAuthenticationException("Vous avez été déconnecté");

                }
                // Try to refresh token
                $refreshTokenRequest = $this->authApi->refreshTokenRequest($this->requestStack->getSession()->get("refreshToken"));
                $accessToken = $refreshTokenRequest["token"];
                $this->requestStack->getSession()->set('accessToken', $refreshTokenRequest["token"]);
                $this->requestStack->getSession()->set('refreshToken', $refreshTokenRequest["refresh_token"]);
            }
        }

        return "Bearer " . $accessToken;
    }

    public function getUser(): ?User
    {
      $user =  $this->security->getUser();
        if($user instanceof User){
            return $user;
        }

        throw new CustomUserMessageAuthenticationException('User is disconnected');
    }
}
