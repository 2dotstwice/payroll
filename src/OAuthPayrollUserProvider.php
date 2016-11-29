<?php

namespace TwoDotsTwice\Payroll;

use Gigablah\Silex\OAuth\Security\Authentication\Token\OAuthTokenInterface;
use Gigablah\Silex\OAuth\Security\User\Provider\OAuthUserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * OAuth in-memory stub user provider.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class OAuthPayrollUserProvider implements UserProviderInterface, OAuthUserProviderInterface
{
    private $users;
    private $credentials;

    /**
     * Constructor.
     *
     * @param array $users       An array of users
     * @param array $credentials A map of usernames with service credentials (service name and uid)
     */
    public function __construct(array $users = array(), array $credentials = array())
    {
        if (function_exists('apc_exists') && apc_exists('users')) {
            $this->users = apc_fetch('users');
        }
        /*
        foreach ($users as $username => $attributes) {
            $password = isset($attributes['password']) ? $attributes['password'] : null;
            $email = isset($attributes['email']) ? $attributes['email'] : null;
            $enabled = isset($attributes['enabled']) ? $attributes['enabled'] : true;
            $roles = isset($attributes['roles']) ? (array) $attributes['roles'] : array();
            $user = new PayrollUser($username, $password, $email, $roles, $enabled, true, true, true);
            $this->createUser($user);
        }
        */

        $this->credentials = $credentials;
    }

    public function createUser(UserInterface $user)
    {
        /*
        if (isset($this->users[strtolower($user->getUsername())])) {
            throw new \LogicException('Another user with the same username already exist.');
        }
        */

        $this->users[strtolower($user->getUsername())] = $user;
        if (function_exists('apc_exists')) {
            apc_add('users', $this->users);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        //dump($this->users);
        if (isset($this->users[strtolower($username)])) {
            $user = $this->users[strtolower($username)];
        } else {
            $user = new PayrollUser($username, '', $username . '@example.org', $array('ROLE_USER'), true, true, true, true);
            $this->createUser($user);
        }

        return new PayrollUser($user->getUsername(), $user->getPassword(), $user->getEmail(), $user->getOauthUid(), $user->getRoles(), $user->isEnabled(), $user->isAccountNonExpired(), $user->isCredentialsNonExpired(), $user->isAccountNonLocked());
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByOAuthCredentials(OAuthTokenInterface $token)
    {
        //dump($this->credentials);
        //dump($token);
        foreach ($this->credentials as $username => $credentials) {
            foreach ($credentials as $credential) {
                if ($credential['service'] == $token->getService() && $credential['uid'] == $token->getUid()) {
                    return $this->loadUserByUsername($username);
                }
            }
        }

        $user = new PayrollUser($token->getUsername(), '', $token->getEmail(), $token->getUid(), array('ROLE_USER'), true, true, true, true);
        $this->createUser($user);

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof PayrollUser) {
            // TODO: this is not right, but not sure why
            // https://stackoverflow.com/questions/19601715/symfony2-security-there-is-no-user-provider-for-user-ibw-userbundle-entity-us
            //throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return $class === 'TwoDotsTwice\\Payroll\\PayrollUser';
    }
}
