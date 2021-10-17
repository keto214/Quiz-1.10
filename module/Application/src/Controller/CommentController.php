<?php

/**
 * This app was built using PHP 7.4 it might not work so well in PHP 8.0+ 
 *  @author    https://twitter.com/pulenong
 */

declare(strict_types=1);

namespace Application\Controller;

use Application\Form\Comment\CreateForm; # be sure to add this use statement
use Application\Model\Table\CommentsTable; 
use Application\Model\Table\QuizzesTable;
use Laminas\Authentication\AuthenticationService; # <- be sure to add this statement
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use RuntimeException;

class CommentController extends AbstractActionController
{
    private $commentsTable;
    private $quizzesTable;

    public function __construct(
        CommentsTable $commentsTable,
        QuizzesTable $quizzesTable
    )
    {
        $this->commentsTable = $commentsTable;
        $this->quizzesTable = $quizzesTable;
    }

    public function createAction()
    {
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()) {
            return $this->redirect()->toRoute('login');
        }

        $createForm = new CreateForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $createForm->setInputFilter($this->commentsTable->getCommentFormFilter());
            $createForm->setData($formData);

            if ($createForm->isValid()) {

                try {
                    $data = $createForm->getData();
                    $this->commentsTable->insertComment($data);
                    $this->quizzesTable->updateComments((int) $data['quiz_id']);

                    return $this->redirect()->toRoute('quiz', ['action' => 'view', 'id' => $data['quiz_id']]);

                } catch(\RuntimeException $exception) {
                    $this->flashMessenger()->addErrorMessage($exception->getMessage());
                    return $this->redirect()->refresh();
                }
            }
        }

        return (new ViewModel())->setTerminal(true);
    }

    public function deleteAction()
    {
        # @todo - try completing this on your own.
        return (new ViewModel())->setTerminal(true);
    }

    public function editAction()
    {
        # @todo try completing this on your own
        return (new ViewModel())->setTerminal(true);
    }
}
