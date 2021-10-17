<?php

declare(strict_types=1);

namespace User;

use Laminas\Authentication\AuthenticationService;
use Laminas\Db\Adapter\Adapter;
use Laminas\Http\Response; # <- add this
use Laminas\Mvc\MvcEvent; # add this
use User\Model\Table\ForgotTable;
use User\Model\Table\PrivilegesTable; # make sure you add this
use User\Model\Table\ResourcesTable;  # and this as well
use User\Model\Table\RolesTable;
use User\Model\Table\UsersTable;
use User\Plugin\AuthPlugin;
use User\Plugin\Factory\AuthPluginFactory;
use User\Service\AclService;
use User\View\Helper\AuthHelper;

class Module
{
	public function getConfig() : array
    {
        return include __DIR__ . '/../config/module.config.php';
    }

    # copy this and place in the Application\MOdule class
    public function onBootstrap(MvcEvent $event) 
    {
        $app = $event->getApplication();
        $eventManager = $app->getEventManager();

        $eventManager->attach($event::EVENT_DISPATCH, [$this, 'getAccessPrivileges']);
    }

    public function getServiceConfig(): array 
    {
    	return [
    		'factories' => [
                ForgotTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new ForgotTable($dbAdapter);
                },
    			UsersTable::class => function($sm) {
    				$dbAdapter = $sm->get(Adapter::class);
    				return new UsersTable($dbAdapter);
                },
                PrivilegesTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new PrivilegesTable($dbAdapter);
                },
                ResourcesTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new ResourcesTable($dbAdapter);
                },
                RolesTable::class => function($sm) {
                    $dbAdapter = $sm->get(Adapter::class);
                    return new RolesTable($dbAdapter);
                }
    		]
    	];
    }

    # let the framework know about your plugin
    public function getControllerPluginConfig()
    {
        return [
            'aliases' => [
                'authPlugin' => AuthPlugin::class,
            ],
            'factories' => [
                AuthPlugin::class => AuthPluginFactory::class
            ],
        ];
    }

    # let the service_manager know about your helper
    public function getViewHelperConfig()
    {
        return [
            'aliases' => [
                'authHelper' => AuthHelper::class,
            ],
            'factories' => [
                AuthHelper::class => AuthPluginFactory::class
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

        //print_r($viewAcl->isAuthorized($role->getRole(), $resource)); exit(1);

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
}
