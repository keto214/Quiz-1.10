<?php

/**
 * This app was built using PHP 7.4 it might not work so well in PHP 8.0+ 
 *  @author    https://twitter.com/pulenong
 */

declare(strict_types=1);

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Model\Table\UsersTable;

class ProfileController extends AbstractActionController
{
	private $usersTable;

	public function __construct(UsersTable $usersTable)
	{
		$this->usersTable = $usersTable;
	}

	public function indexAction()
	{
		# ok now I am in the ProfileCOntroller
		# the reason I am here is because I want us to now display the profile picture

		$id = (int) $this->params()->fromRoute('id');
		$info = $this->usersTable->fetchAccountById((int) $id);
		if(!$info) {
			return $this->notFoundAction();
		}

    	return new ViewModel(['data' => $info]);
	}
}
