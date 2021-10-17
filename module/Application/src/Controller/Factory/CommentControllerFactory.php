<?php

declare(strict_types=1);

namespace Application\Controller\Factory;

use Application\Controller\CommentController;
use Application\Model\Table\CommentsTable;
use Application\Model\Table\QuizzesTable;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;

class CommentControllerFactory implements FactoryInterface
{
	public function __invoke(ContainerInterface $container, $requestedName ,array $options = null)
	{
		return new CommentController(
            $container->get(CommentsTable::class),
			$container->get(QuizzesTable::class)
		);
	}
}
