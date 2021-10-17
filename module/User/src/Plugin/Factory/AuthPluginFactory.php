<?php

declare(strict_types=1);

namespace User\Plugin\Factory;

use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService; # this should be added
use Laminas\Authentication\AuthenticationServiceInterface; # be sure to add this
use Laminas\ServiceManager\Factory\FactoryInterface;
use User\Model\Table\UsersTable; # this as well.
use User\Plugin\AuthPlugin;

class AuthPluginFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
	{
		/*return new AuthPlugin(
			$container->get(AuthenticationService::class),
			$container->get(UsersTable::class)
		);*/

		$authPlugin = new AuthPlugin();

		if ($container->has(AuthenticationService::class)) {
			// set the dependency
			$authPlugin->setAuthenticationService(
				$container->get(AuthenticationService::class)
			);
		} elseif ($container->has(AuthenticationServiceInterface::class)) {
			$authPlugin->setAuthenticationService(
				$container->get(AuthenticationServiceInterface::class)
			);
		}

		if ($container->has(UsersTable::class)) {
			$authPlugin->setUsersTable(
				$container->get(UsersTable::class)
			);
		}

		return $authPlugin;
	}
}
