<?php

namespace App\Security;

use App\Entity\Person\Person;
use App\Entity\Security\OAuth2AccessToken;
use App\Security\OAuth2User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenIDConnectClient\OpenIDConnectProvider;
use OpenIDConnectClient\AccessToken;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;
use Symfony\Component\Security\Core\Exception\AuthenticationExpiredException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class OAuth2UserProvider implements UserProviderInterface
{
    private $provider;
    private $em;

    public function __construct(OpenIDConnectProvider $provider, EntityManagerInterface $em)
    {
        $this->provider = $provider;
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        throw new MethodNotImplementedException('ToDo (for impersonation)');
    }

    public function loadUserByToken(AccessToken $token)
    {
        $tokenArray = $token->getValues();
        $roles = ['ROLE_OAUTH2'];
        if (isset($tokenArray['scope']) && false !== strpos($tokenArray['scope'], 'admin'))
            $roles[] = 'ROLE_ADMIN';

        $idToken = $token->getIdToken();

        $person = new Person();
        $person
            ->setId($idToken->getClaim('sub'))
            ->setEmail($idToken->getClaim('email', ''))
            ->setShortnameExpr($idToken->getClaim('given_name', ''))
            ->setNameExpr($idToken->getClaim('given_name', '') . ' ' . $idToken->getClaim('family_name', ''))
        ;
        
        $user = new OAuth2User();
        $user
            ->setId($token->getIdToken()->getClaim('sub'))
            ->setRoles($roles)
            ->setPerson($person)
        ;

        $dbToken = $this->em->getRepository(OAuth2AccessToken::class)->findOneBy(['id' => $user->getId()]);

        if (is_null($dbToken)) {
            $dbToken = new OAuth2AccessToken();
            $dbToken->setId($user->getId());

            $this->em->persist($dbToken);
        }

        // We store the access token seperated from the User for security
        $dbToken->setAccessToken($token);
        $this->em->flush();

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }
        
        return $this->_refresh($user);
    }

    private function _refresh(OAuth2User $user)
    {
        $dbToken = $this->em->getRepository(OAuth2AccessToken::class)->findOneBy(['id' => $user->getId()]);
        $accessToken = $dbToken->getAccessToken();

        // Check whether user data is up-to-date (ID token) 
        // and user is logged in (access token)
        if (!$accessToken->getIdToken()->isExpired() && !$accessToken->hasExpired()) {
            return $user;
        }

        // ID token or access token is expired, so user should be refreshed
        try {
            $accessToken = $this->provider->getAccessToken('refresh_token', [
                'refresh_token' => $accessToken->getRefreshToken(),
            ]);

            if ($accessToken->hasExpired())
                throw new AuthenticationExpiredException("Access token has expired");

            // A valid token was obtain, we refresh the user data and return it
            return $this->loadUserByToken($accessToken);
        } catch (\Exception $e) {
            // getAccessToken throws exception, this means either a problem
            // occurred, or user has been logged out. Therefore, we log out
            $this->em->remove($dbToken);
            $this->em->flush();

            throw new UsernameNotFoundException(); // ugly, symfony fix ur shit
        }
    }

    public function supportsClass($class)
    {
        return OAuth2User::class === $class;
    }
}