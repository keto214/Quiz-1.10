<?php

declare(strict_types=1);

namespace Application\View\Helper;

use Application\Model\Table\CommentsTable;
use Application\View\Helper\CommentHelper;
use Interop\Container\ContainerInterface;
use Laminas\Router\RouteMatch;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CommentHelperFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName ,array $options = null)
	{
        $commentHelper = new CommentHelper();
        

        if($container->has(CommentsTable::class)) {
            $commentHelper->setCommentsTable(
                $container->get(CommentsTable::class)
            );
        }

        if($container->has(RouteMatch::class)) {
            $commentHelper->setRouteMatch($container->get('Application')->getMvcEvent()->getRouteMatch());
        }

        return $commentHelper;
	}
}
