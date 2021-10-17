<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\Adapter; # this as well
use Laminas\Db\ResultSet\HydratingResultSet; # add this as well
use Laminas\Db\TableGateway\AbstractTableGateway; # be sure to add this
use Laminas\Hydrator\ClassMethodsHydrator; # make sure to add this
use User\Model\Entity\PrivilegeEntity; # <-

class PrivilegesTable extends AbstractTableGateway
{
    protected $table = 'privileges';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }

    public function fetchAllResources()
    {
        $sqlQuery = $this->sql->select()
            ->join('resources', 'resources.resource_id='.$this->table.'.resource_id',
             ['module', 'controller', 'action'])
            ->join('roles', 'roles.role_id='.$this->table.'.role_id', 'role');
        
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);
        $handler = $sqlStmt->execute();

        $hydrator = new ClassMethodsHydrator();
        $entity = new PrivilegeEntity();
        $resultSet = new HydratingResultSet($hydrator, $entity);
        $resultSet->initialize($handler);

        return $resultSet;
    }
}
