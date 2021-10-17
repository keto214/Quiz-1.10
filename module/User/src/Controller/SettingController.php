<?php

/**
 * This app was built using PHP 7.4 it might not work so well in PHP 8.0+ 
 *  @author    https://twitter.com/pulenong
 */

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\AuthenticationService;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use PHPThumb\GD; # <- be sure to add this
use User\Form\Setting\DeleteForm;
use User\Form\Setting\EmailForm;
use User\Form\Setting\PasswordForm;
use User\Form\Setting\UploadForm;  # <- be sure to add this
use User\Form\Setting\UsernameForm;
use User\Model\Table\UsersTable;
use User\Model\UrlModel; # be sure to add this line of code

class SettingController extends AbstractActionController
{
	private $usersTable;

	public function __construct(UsersTable $usersTable)
	{
		$this->usersTable = $usersTable;
	}

	public function deleteAction()
	{
		# make sure only signed in users see this page
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			#return $this->redirect()->toRoute('login');
			# accessing this page requires authentication
			# so let us change the redirect to include our little feature we just added

			return $this->redirect()->toRoute(
				'login',
				 [
					# I forgot to remind you to add the UrlModel to the using statements above
					'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
				]
			);
		}

		$deleteForm = new DeleteForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$deleteForm->setData($formData);

			if($deleteForm->isValid()) {
				if($request->getPost()->get('delete_account') == 'Yes') {
					$this->usersTable->deleteAccount((int) $this->authPlugin()->getUserId());
					$this->flashMessenger()->addSuccessMessage('Account successfully deleted.');
					# now clear all sessions that belongs to this user
					return $this->redirect()->toRoute('logout');
				}

				# otherwise return to the homepage
				return $this->redirect()->toRoute('home');
			}
		}


		return new ViewModel(['form' => $deleteForm]);
	}

	public function emailAction()
	{
		# only registered users should see this page
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			//return $this->redirect()->toRoute('login');
			return $this->redirect()->toRoute(
				'login',
				 [
					 'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
				]
			);
		}

		$emailForm = new EmailForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$emailForm->setInputFilter($this->usersTable->getEmailFormFilter());
			$emailForm->setData($formData);

			if($emailForm->isValid()) {
				try {
					$data = $emailForm->getData();
					$this->usersTable->updateEmail($data['new_email'], (int) $this->authPlugin()->getUserId());

					$this->flashMessenger()->addSuccessMessage('Email address successfully updated!');
					return $this->redirect()->toRoute(
						'profile',
						 [
						 	'id' => $this->authPlugin()->getUserId(),
						 	'username' => mb_strtolower($this->authPlugin()->getUsername())
						 ]
					);
				} catch(\RuntimeException $exception) {
					$this->flashMessenger()->addErrorMessage($exception->getMessage());
					return $this->redirect()->refresh();
				}
			}
		}

		return new ViewModel(['form' => $emailForm]);
	}

	public function indexAction()
	{	

		# only logged in users should see this page
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}
		
		return new ViewModel();
	}

	public function passwordAction()
	{
		# only logged in users should see this page
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			//return $this->redirect()->toRoute('login');
			return $this->redirect()->toRoute(
				'login',
				 [
					 'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
				]
			);
		}

		$passwordForm = new PasswordForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$passwordForm->setInputFilter($this->usersTable->getPasswordFormFilter()); 
			$passwordForm->setData($formData);

			if($passwordForm->isValid()) {
				$data = $passwordForm->getData();
				$hash = new Bcrypt();

				# compare password from passwordForm with one in database table
				if($hash->verify($data['current_password'], $this->authPlugin()->getPassword())) {
					# if all is well save the newly set password
					$this->usersTable->updatePassword($data['new_password'], (int)$this->authPlugin()->getUserId());

					$this->flashMessenger()->addSuccessMessage('Password successfully updated.');
					# guess what I am logging this person out
					# so that they signin with a new password
					# if u do not like that idea you can redirect them to the profile page
					return $this->redirect()->toRoute('logout');
					
					// return $this->redirect()->toRoute('profile', ['id' => $this->authPlugin()->getUserId(), 'username' => mb_strtolower($this->authPlugin()->getUsername())]);
					
				} else {
					$this->flashMessenger()->addErrorMessage('Incorrect current password!');
					return $this->redirect()->refresh();
				}

			} 
			// made correction. I had earlier put the else statement here instead of up where it should have been
		}

		return new ViewModel(['form' => $passwordForm]);
	}

	public function uploadAction()
	{
		# here we simply want only logged in users to access this resource
		# if not logged in we send you to the login page
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			//return $this->redirect()->toRoute('login');
			return $this->redirect()->toRoute(
				'login',
				 [
					 'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
				]
			);
		}


		$uploadForm = new UploadForm();
		$request = $this->getRequest();

		# here it is like saying if(isset($_POST['']) in procedural programming
		if($request->isPost()) {
			$formData = $request->getFiles()->toArray();
			$uploadForm->setInputFilter($this->usersTable->getUploadFormFilter());
			$uploadForm->setData($formData);

			# here as you already know we are sying if the form data is validated and good

			if($uploadForm->isValid()) {

				$data = $uploadForm->getData(); # get form data

				if(file_exists($data['photo']['tmp_name'])) {
					# from $data['photo']['tmp_name'] we get something like C:\~\file.png
					# now by using basename we are saying we want only the file.png part
					$name = basename($data['photo']['tmp_name']);
					$name = strtolower($name);

					$gd = new GD($data['photo']['tmp_name']); # GD is from the class we installed at the beginning of this video
					# next let us resize our newly uploaded image
					$gd->adaptiveResize(200, 200) # we want an 100x100 thumbnail
					   ->save(DOOR . DS . 'images' . DS . 'pictures' . DS . $name); # save our image

					# let us update our usersTable to store our image name in the photo column
					$this->usersTable->updatePhoto($name, (int) $this->authPlugin()->getUserId());

					# once the file has been resized we want to get rid of the original, just assuming..
					unlink($data['photo']['tmp_name']);

					#tell the uploader that the file has successfully been uploaded
					$this->flashMessenger()->addSuccessMessage('File successfully uploaded');
					return $this->redirect()->toRoute('home');
				} else {
					#if things do not go accordingly let us say so
					$this->flashMessenger()->addErrorMessage('File upload failed!');
					return $this->redirect()->refresh();
				}
			}
		}

		return new ViewModel(['form' => $uploadForm]);
	}

	public function usernameAction()
	{
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			//return $this->redirect()->toRoute('login');
			return $this->redirect()->toRoute(
				'login',
				 [
					 'returnUrl' => UrlModel::encode($this->getRequest()->getRequestUri())
				]
			);
		}

		$usernameForm = new UsernameForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$usernameForm->setInputFilter($this->usersTable->getUsernameFormFilter());
			$usernameForm->setData($formData);

			if($usernameForm->isValid()) {
				try {
					$data = $usernameForm->getData();
					$this->usersTable->updateUsername($data['new_username'], (int) $this->authPlugin()->getUserId());

					$this->flashMessenger()->addSuccessMessage('Username successfully updated');
					return $this->redirect()->toRoute('profile', ['id' => $this->authPlugin()->getUserId(), 'username' => mb_strtolower($this->authPlugin()->getUsername())]);
				} catch(\RuntimeException $exception) {
					$this->flashMessenger()->addErrorMessage($exception->getMessage());
					return $this->redirect()->refresh();
				}
			}
		}

		return new ViewModel(['form' => $usernameForm]);
	}
}
