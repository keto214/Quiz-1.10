<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\Adapter;
use Laminas\Db\TableGateway\AbstractTableGateway;
use Laminas\Hydrator\ClassMethodsHydrator; # add this
use User\Model\Entity\RoleEntity; # add this

class RolesTable extends AbstractTableGateway
{
	protected $adapter;
	protected $table = 'roles';

	public function __construct(Adapter $adapter)
	{
		$this->adapter = $adapter;
		$this->initialize();
	}

	public function fetchRoleById(int $roleId)
	{
		$sqlQuery = $this->sql->select()->where(['role_id' => $roleId]);
		$sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler  = $sqlStmt->execute()->current();

		if(!$handler) {
			return null;
		}

		$hydrator = new ClassMethodsHydrator();
		$entity   = new RoleEntity();
		$hydrator->hydrate($handler, $entity);

		return $entity;
	}

	public function fetchRole(string $role)
	{
		$sqlQuery = $this->sql->select()->where(['role' => $role]);
		$sqlStmt  = $this->sql->prepareStatementForSqlObject($sqlQuery);
		$handler  = $sqlStmt->execute()->current();

		if(!$handler) {
			return null;
		}

		$hydrator = new ClassMethodsHydrator();
		$entity   = new RoleEntity();
		$hydrator->hydrate($handler, $entity);

		return $entity;
	}
}