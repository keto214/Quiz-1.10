<?php

declare(strict_types=1);

namespace User\Model\Table;

use Laminas\Db\Adapter\Adapter; 
use Laminas\Db\TableGateway\AbstractTableGateway; 

class ResourcesTable extends AbstractTableGateway
{
    protected $table = 'resources';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->initialize();
    }
}
