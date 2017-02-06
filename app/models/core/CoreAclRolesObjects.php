<?php

require_once __DIR__ . "/TableDefinitions.php";

/**
 * Class to work with table "acl_roles_objects"
 */
class CoreAclRolesObjects extends CoreAclRolesObjectsTable
{
    /**
     * Check if $roleId has access to $objectId
     * @param $roleId
     * @param $objectId
     * @return int
     */
    static function hasAccess($roleId, $objectId)
    {
        return self::querySelect('access')
            ->where('role_id = ? and object_id = ?', $roleId, $objectId)
            ->fetchCell();
    }
}