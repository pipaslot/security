<?php


namespace Pipas\Security;

use Nette\Object;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator;
use Nette\Security\Identity;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;

/**
 * Enable pass to user list hashed and also un-hashed passwords
 * @author Petr Štipek <p.stipek@email.cz>
 */
class SimpleHashedAuthenticator extends Object implements IAuthenticator
{
	/** @var array */
	private $userlist;

	/** @var array */
	private $usersRoles;

	/**
	 * @param  array  list of pairs username => password
	 * @param  array  list of pairs username => role[]
	 */
	public function __construct(array $userlist, array $usersRoles = array())
	{
		$this->userlist = $userlist;
		$this->usersRoles = $usersRoles;
	}

	/**
	 * Performs an authentication against e.g. database.
	 * and returns IIdentity on success or throws AuthenticationException
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		foreach ($this->userlist as $name => $pass) {
			if (strcasecmp($name, $username) === 0) {
				$isNoHashed = Passwords::needsRehash($pass);
				if (($isNoHashed AND (string)$pass === (string)$password) OR (!$isNoHashed AND Passwords::verify($password, $pass))) {
					return new Identity($name, isset($this->usersRoles[$name]) ? $this->usersRoles[$name] : NULL);
				} else {
					throw new AuthenticationException('Invalid password.', self::INVALID_CREDENTIAL);
				}
			}
		}
		throw new AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
	}
}