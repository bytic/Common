<?php

namespace ByTIC\Common\Security\ACL\Permissions;

use ByTIC\Common\Records\Record;
use ByTIC\Common\Security\ACL\ACL;
use ByTIC\Common\Security\ACL\Resources\Resource;
use ByTIC\Common\Security\ACL\Resources\Roles\Role;

class Permission extends Record
{
    protected $_resource;
    protected $_role;

    public function setResource(Resource $resource)
    {
        $this->_resource = $resource;
        $this->id_acl_resource = ACL::instance()->getResourcePathString($resource);
    }

    public function getResource()
    {
        if (!$this->_resource) {
            $this->_resource = ACL::instance()->getResource($this->id_acl_resource);
        }
        return $this->_resource;
    }

    public function setRole(Role $role)
    {
        $this->_role = $role;
        $this->id_acl_role = ACL::instance()->getRoleID($role);
    }

    public function getRole()
    {
        if (!$this->_role) {
            $this->_role = ACL::instance()->getRole($this->id_acl_role);
        }
        return $this->_role;
    }
}
