<?php

require_once __DIR__ . "/TableDefinitions.php";

/**
 * Class to work with table "users"
 */
class CoreUsers extends CoreUsersTable
{
    /**
     * Check if user has access to Acl object
     * @param $objectName
     * @return int
     * @throws Exception
     */
    function hasAccess($objectName)
    {
        $access = 0;

        if (!$object = CoreAclObjects::findRow('name = ?', $objectName)) {
            throw new Exception("Acl object {$objectName} not found");
        }

        foreach ($this->getRoles() as $role) {
            if ($access = CoreAclRolesObjects::hasAccess($role->id, $object->id)) {
                # user has access, stop search
                break;
            }
        }

        return $access;
    }

    /**
     * Get user's roles
     * @return array
     */
    function getRoles()
    {
        $roles = [];
        foreach (CoreAclUsersRoles::find('user_id = ?', $this->id) as $userRole) {
            $roles[] = CoreAclRoles::get($userRole->role_id);
        }
        return $roles;
    }
}