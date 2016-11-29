<?php

namespace TwoDotsTwice\Payroll;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * Stub user class for testing.
 *
 * @author Chris Heng <bigblah@gmail.com>
 */
class PayrollUser implements AdvancedUserInterface
{
    private $username;
    private $password;
    private $email;
    private $oauthUid;

    private $enabled;
    private $accountNonExpired;
    private $credentialsNonExpired;
    private $accountNonLocked;
    private $roles;

    public function __construct($username, $password, $email, $oauthUid, array $roles = array(), $enabled = true, $userNonExpired = true, $credentialsNonExpired = true, $userNonLocked = true)
    {
        if (empty($username)) {
            throw new \InvalidArgumentException('The username cannot be empty.');
        }

        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->enabled = $enabled;
        $this->accountNonExpired = $userNonExpired;
        $this->credentialsNonExpired = $credentialsNonExpired;
        $this->accountNonLocked = $userNonLocked;
        $this->roles = $roles;
    }

    /**
     * Gets the user email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * returns the email domain part
     *
     * @return string
     */
    public function getEmailDomain()
    {
        return explode('@', $this->email)[1];
    }

    /**
     * @return mixed
     */
    public function getOauthUid()
    {
        return $this->oauthUid;
    }

    /**
     * returns the email domain part
     *
     * @return string
     */
    public function getUsername()
    {
        return explode('@', $this->email)[0];
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    /**
     * {@inheritdoc}
     */
    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    /**
     * {@inheritdoc}
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}
