<?php

declare(strict_types=1);

namespace User\Model\Entity;

class PrivilegeEntity
{
    protected $resource_id;
    protected $role_id;
    #resources table columns
    protected $module;
    protected $controller;
    protected $action;
    #roles table columns
    protected $role;

    public function getResourceId()
    {
        return $this->resource_id;
    }

    public function setResourceId($resource_id)
    {
        $this->resource_id = $resource_id;
        return $this;
    }

    public function getRoleId()
    {
        return $this->role_id;
    }

    public function setRoleId($role_id)
    {
        $this->role_id = $role_id;
        return $this;
    }

    public function getModule()
    {
        return $this->module;
    }

    public function setModule($module)
    {
        $this->module = $module;
        return $this;
    }

    public function getController()
    {
        return $this->controller;
    }

    public function setController($controller)
    {
        $this->controller = $controller;
        return $this;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
        return $this;
    }

    # i forgot to add the getResource method in the privilegeEntity class
    # let us do so now

    public function getResource()
    {
        # what we want is to get a string in the form e.g - User\Controller\AuthController\create
        return ucfirst($this->getModule()) . DS . 'Controller' . 
          DS . ucfirst($this->getController()) . 'Controller' . DS . strtolower($this->getAction());
    }

    # i hope all is done well. Wish us luck. Let us try our feature.
}
