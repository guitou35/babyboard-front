<?php

namespace App\Security;


use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;
use App\Service\HttpClient\AuthHttpClient\AuthApiHttpClient;
use App\Service\HttpClient\EntityHttpClient\UserApiHttpClient;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{

    public function __construct(
        private AuthApiHttpClient $authApiHttpClient, 
        private RequestStack $requestStack, 
        private Security $security,
        private UserApiHttpClient $UserApiHttpClient
    )
    {
    }

    public function getTokenByCredentials(string $username, string $password): array
    {
        $response = $this->authApiHttpClient->authenticationRequest($username, $password);

        return [
            'accessToken' => $response['token'],
            'refreshToken' => $response['refresh_token']
        ];
    }

    public function getUserIdByToken(string $token)
    {
        $tokenParts = explode('.', $token);
        $payload = json_decode(base64_decode($tokenParts[1]), true);

        return $payload['id'];
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $session = $this->requestStack->getSession();

        $userId = $identifier;

        // Normal authentication case
        if ($user = $this->UserApiHttpClient->getUserById($userId)->getContent()) {

            $loggedUser = (new User())
                ->setAccessToken($session->get('accessToken'))
                ->setRefreshToken($session->get('refreshToken'))
                ->setId($user["id"])
                ->setEmail($user["email"])
                ->setName($user["name"])
                ->setLastName($user["lastName"])
                ->setRoles($user["roles"])
            ;

            return $loggedUser;
        }
        return null;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }   

        return $user;
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class || is_subclass_of($class, User::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newEncodedPassword): void
    {
        
        // TODO: when encoded passwords are in use, this method should:
        // 1. persist the new password in the user storage
        // 2. update the $user object with $user->setPassword($newEncodedPassword);
    }
}