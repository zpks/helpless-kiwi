<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Symfony\Component\Intl\Exception\MethodNotImplementedException;

class OAuth2UserProvider implements UserProviderInterface
{
    private $provider;

    public function __construct(GenericProvider $provider)
    {
        $this->provider = $provider;
    }

    public function loadUserByUsername($username)
    {
        throw new MethodNotImplementedException('ToDo (for impersonation)');
    }

    public function loadUserByToken(AccessTokenInterface $token)
    {
        // Using the access token, we may look up details about the
        // resource owner.
        $resourceOwner = $this->provider->getResourceOwner($token);

        return new User(
            $resourceOwner->getId(),
            null,
            // the roles for the user - you may choose to determine
            // these dynamically somehow based on the user
            array('ROLE_API')
        );
    }

    public function refreshUser(UserInterface $user)
    {

        // $user is the User that you set in the token inside authenticateToken()
        // after it has been deserialized from the session

        // you might use $user to query the database for a fresh user
        // $id = $user->getId();
        // use $id to make a query

        // if you are *not* reading from a database and are just creating
        // a User object (like in this example), you can just return it
        return $user;
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}