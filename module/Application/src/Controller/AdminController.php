<?php

/**
 * This app was built using PHP 7.4 it might not work so well in PHP 8.0+ 
 *  @author    https://twitter.com/pulenong
 */
declare(strict_types=1);

namespace Application\Controller;

use Application\Model\Table\QuizzesTable;
use Application\Form\Quiz\DeleteForm;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AdminController extends AbstractActionController
{
	private $quizzesTable;

	public function __construct(QuizzesTable $quizzesTable)
	{
		$this->quizzesTable = $quizzesTable;
	}

	public function indexAction()
	{
		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}

		# make sure that only the admin can acess this page
		if(!$this->authPlugin()->getRoleId() == 1) {
			return $this->notFoundAction();
		}

		return new ViewModel([
			'quizzes' => $this->quizzesTable->fetchLatestQuizzes()
		]);
	}

	public function deleteAction()
	{
		$id = (int) $this->params()->fromRoute('id');
		if(!is_numeric($id)) {
			return $this->notFoundAction();
		}

		$auth = new AuthenticationService();
		if(!$auth->hasIdentity()) {
			return $this->redirect()->toRoute('login');
		}

		# make sure that only the admin can acess this page
		if(!$this->authPlugin()->getRoleId() == 1) {
			return $this->notFoundAction();
		}

		$info = $this->quizzesTable->fetchQuizById((int)$id);
		if(!$info) {
			return $this->notFoundAction();
		}

		$deleteForm = new DeleteForm();
		$request = $this->getRequest();

		if($request->isPost()) {
			$formData = $request->getPost()->toArray();
			$deleteForm->setData($formData);

			if($deleteForm->isValid()) {
				if($request->getPost()->get('delete_quiz') == 'Yes') {
					# now check that the person deleting the quiz is the author of the quiz
					if($info->getUserId() == $this->authPlugin()->getUserId() || $this->authPlugin()->getRoleId() == 1)
					{
						$this->quizzesTable->deleteQuizById((int)$info->getQuizId());
						$this->flashMessenger()->addInfoMessage('Quiz successfully deleted!');

						return $this->redirect()->toRoute('admin_quiz', ['action' => 'index']);
					}

					# redirect this person away from this page with a warning
					$this->flashMessenger()->addWarningMessage('You can only delete quiz you have posted');
					return $this->redirect()->toRoute('home');
				}

				# here as well. The person presumably has clicked the No button
				return $this->redirect()->toRoute('admin_quiz', ['action' => 'index']);
			}
		}

		return new ViewModel([
			'form' => $deleteForm,
			'quiz' => $info
		]);
	}
}
