<?php

declare(strict_types=1);

namespace User\Plugin;

use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\AuthenticationServiceInterface;
use Laminas\Mvc\Controller\Plugin\AbstractPlugin;
use Laminas\Mvc\Plugin\Identity\Identity;  # <-- be sure to add this using
use User\Model\Table\UsersTable;

//class AuthPlugin extends AbstractPlugin
class AuthPlugin extends Identity
{
	//protected $authenticationService; no longer needed as well
	
	# If you look our AuthPlugin does exactly what the Identity Plugin does.
	# So it makes sense to just extend the Identity Plugin and add the extras we need.
	# let us do that.
	protected $usersTable;

	/*
	 * No longer needed
	 public function __construct(AuthenticationService $authenticationService, UsersTable $usersTable)
	{
		$this->authenticationService = $authenticationService;
		$this->usersTable = $usersTable;
	}*/

	public function __invoke()
	{

		if (!$this->usersTable instanceof UsersTable)
		{
			throw new \RuntimeException(
				'No UsersTable instance provided; cannot fetch user data as a result'
			);
		}

		if(!$this->authenticationService instanceof AuthenticationServiceInterface)
		{
			return;
		}

		if(!$this->authenticationService->hasIdentity()){
			return;
		}

		return $this->usersTable->fetchAccountById(
			(int)$this->authenticationService->getIdentity()->user_id
		);
	}


	public function getUsersTable()
	{
		return $this->usersTable;
	}

	public function setUsersTable($usersTable)
	{
		$this->usersTable = $usersTable;
		return $this;
	}
}
