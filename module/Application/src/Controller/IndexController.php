<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */
/**
 * This app was built using PHP 7.4 it might not work so well in PHP 8.0+ 
 *  @author    https://twitter.com/pulenong
 */

declare(strict_types=1);

namespace Application\Controller;

use Application\Model\Table\QuizzesTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
	private $quizzesTable;

	public function __construct(QuizzesTable $quizzesTable)
	{
		$this->quizzesTable = $quizzesTable;
	}

    public function indexAction()
    {
    	return new ViewModel([
    		'quizzes' => $this->quizzesTable->fetchLatestQuizzes()
    	]);
    }
}
