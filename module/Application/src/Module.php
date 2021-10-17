<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 * 
 * @author    https://twitter.com/pulenong
 */

declare(strict_types=1);

namespace Application;

use Application\Model\Table\AnswersTable;
use Application\Model\Table\CategoriesTable;
use Application\Model\Table\QuizzesTable;
use Application\Model\Table\TalliesTable;
use Application\Form\Quiz\CreateForm;
use Application\Model\Table\CommentsTable;
use Application\View\Helper\CommentHelper;
use Application\View\Helper\CommentHelperFactory;
use Laminas\Authentication\AuthenticationService;
use Laminas\Db\Adapter\Adapter;
use Laminas\Http\Response; # <- add this
use Laminas\Mvc\MvcEvent; # add this
use User\Service\AclService;
use User\Model\Table\RolesTable;
use User\Model\Table\PrivilegesTable; # be sure to add this line in Application\Module.php file

class Module
{
    public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

	public function onBootstrap(MvcEvent $event) 
    {
        $app = $event->getApplication();
        $eventManager = $app->getEventManager();

        $eventManager->attach($event::EVENT_DISPATCH, [$this, 'getAccessPrivileges']);
    }

    public function getServiceConfig()
    {
    	return [
    		'factories' => [
    			AnswersTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new AnswersTable($dbAdapter);
                },
                CommentsTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new CommentsTable($dbAdapter);
                },
    			CategoriesTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new CategoriesTable($dbAdapter);
    			},
    			QuizzesTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new QuizzesTable($dbAdapter);
    			},
    			TalliesTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new TalliesTable($dbAdapter);
    			},
    		]
    	];
    }

    public function getFormElementConfig()
    {
        return [
            'factories' => [
                CreateForm::class => function($sm) {
                    $categoriesTable = $sm->get(CategoriesTable::class);
                    return new CreateForm($categoriesTable);
                }
            ]
        ];
	}
	
	public function getAccessPrivileges(MvcEvent $mvcEvent)
    {
        $services = $mvcEvent->getApplication()->getServiceManager();
        $viewAcl  = new AclService($services->get(PrivilegesTable::class));
        $viewAcl->grantAccess();

        $auth = new AuthenticationService();
        $rolesTable = $services->get(RolesTable::class);
        $guest = $rolesTable->fetchRole('guest'); # alternatively you can set a DEFAULT_ROLE = guest constant

        # here we are simply checking if the user is logged in or not. If not logged in, they are
        # of guest role. If they are logged iin we get their role_id from the session
        $roleId = !$auth->hasIdentity() ? (int) $guest->getRoleId() : (int) $auth->getIdentity()->role_id;
        $role = $rolesTable->fetchRoleById($roleId);

        $routeMatch = $mvcEvent->getRouteMatch();
        $resource = $routeMatch->getParam('controller') . DS . $routeMatch->getParam('action');

        $response = $mvcEvent->getResponse();
        if($viewAcl->isAuthorized($role->getRole(), $resource)) {
            if($response instanceof Response) {
                if($response->getStatusCode() != 200) {
                    $response->setStatusCode(200);
                }
            }

            return;
        }

        if(!$response instanceof Response) {
            return $response;
        }

        $response->setStatusCode(403);
        $response->setReasonPhrase('Forbidden');
        
        # custom handle the 403 error
        return $mvcEvent->getViewModel()->setTemplate('error/403');
	}
    
    
    public function getViewHelperConfig(): array
    {
        return [
            'aliases' => [
                'commentHelper' => CommentHelper::class,
            ],
            'factories' => [
                CommentHelper::class => CommentHelperFactory::class
            ]
        ];
    }
}

# Alright let us stop here. We will continue in the next video.