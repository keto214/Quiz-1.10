<?php

/**
 * This app was built using PHP 7.4 it might not work so well in PHP 8.0+ 
 *  @author    https://twitter.com/pulenong
 */

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;

class LogoutController extends AbstractActionController
{
	public function indexAction()
	{
		$auth = new AuthenticationService();
		if($auth->hasIdentity()) {
			$auth->clearIdentity();
		}

		return $this->redirect()->toRoute('login');
	}
}
