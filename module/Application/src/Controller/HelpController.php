<?php

/**
 * This app was built using PHP 7.4 it might not work so well in PHP 8.0+ 
 *  @author    https://twitter.com/pulenong
 */
declare(strict_types=1);

namespace Application\Controller;

use Application\Form\Help\ContactForm;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class HelpController extends AbstractActionController
{
	public function contactAction()
	{
		$contactForm = new ContactForm();
		return new ViewModel(['form' => $contactForm]);
	}

	public function privacyAction()
	{
		return new ViewModel();
	}

	public function termsAction()
	{
		return new ViewModel();
	}
}
