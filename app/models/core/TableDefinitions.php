<?php 

/**
 * Class: CoreAclObjectsTable to work with table "acl_objects".
 * THIS CLASS WAS AUTOMATICALLY GENERATED. ALL MANUAL CHANGES WILL BE LOST!
 * PUT YOUR CODE TO CLASS "CoreAclObjects" INSTEAD.
*/
class CoreAclObjectsTable extends \Air\AbstractTable {

    static $fields;
    static $tablename = 'acl_objects';
    static $dbconfig = 'core';
    static $pk = array('id');
    static $generated;
    
    /**
    * Field: acl_objects.id
    * @var int(10) unsigned
    */
    public $id;
    
    /**
    * Field: acl_objects.name
    * @var varchar(50)
    */
    public $name;
    
    /**
    * Field: acl_objects.description
    * @var text
    */
    public $description;
    


}

CoreAclObjectsTable::$generated = array(
);


/**
 * Class: CoreAclRolesTable to work with table "acl_roles".
 * THIS CLASS WAS AUTOMATICALLY GENERATED. ALL MANUAL CHANGES WILL BE LOST!
 * PUT YOUR CODE TO CLASS "CoreAclRoles" INSTEAD.
*/
class CoreAclRolesTable extends \Air\AbstractTable {

    static $fields;
    static $tablename = 'acl_roles';
    static $dbconfig = 'core';
    static $pk = array('id');
    static $generated;
    
    /**
    * Field: acl_roles.id
    * @var int(10) unsigned
    */
    public $id;
    
    /**
    * Field: acl_roles.name
    * @var varchar(50)
    */
    public $name;
    
    /**
    * Field: acl_roles.description
    * @var text
    */
    public $description;
    


}

CoreAclRolesTable::$generated = array(
);


/**
 * Class: CoreAclRolesObjectsTable to work with table "acl_roles_objects".
 * THIS CLASS WAS AUTOMATICALLY GENERATED. ALL MANUAL CHANGES WILL BE LOST!
 * PUT YOUR CODE TO CLASS "CoreAclRolesObjects" INSTEAD.
*/
class CoreAclRolesObjectsTable extends \Air\AbstractTable {

    static $fields;
    static $tablename = 'acl_roles_objects';
    static $dbconfig = 'core';
    static $pk = array('id');
    static $generated;
    
    /**
    * Field: acl_roles_objects.id
    * @var int(11) unsigned
    */
    public $id;
    
    /**
    * Field: acl_roles_objects.role_id
    * @var int(11) unsigned
    */
    public $role_id;
    
    /**
    * Field: acl_roles_objects.object_id
    * @var int(11) unsigned
    */
    public $object_id;
    
    /**
    * Field: acl_roles_objects.access
    * @var int(10) unsigned (Default: '1')
    */
    public $access = '1';
    

    /**
    * Get linked record from table "acl_roles"
    * where acl_roles.id = acl_roles_objects.role_id
    * @return CoreAclRoles
    */
    public function getRole(){
        return db('core')->getObject('acl_roles', 'id', $this->role_id, 'CoreAclRoles');
    }

    /**
    * Get linked record from table "acl_objects"
    * where acl_objects.id = acl_roles_objects.object_id
    * @return CoreAclObjects
    */
    public function getObject(){
        return db('core')->getObject('acl_objects', 'id', $this->object_id, 'CoreAclObjects');
    }


}

CoreAclRolesObjectsTable::$generated = array(
);


/**
 * Class: CoreAclUsersRolesTable to work with table "acl_users_roles".
 * THIS CLASS WAS AUTOMATICALLY GENERATED. ALL MANUAL CHANGES WILL BE LOST!
 * PUT YOUR CODE TO CLASS "CoreAclUsersRoles" INSTEAD.
*/
class CoreAclUsersRolesTable extends \Air\AbstractTable {

    static $fields;
    static $tablename = 'acl_users_roles';
    static $dbconfig = 'core';
    static $pk = array('id');
    static $generated;
    
    /**
    * Field: acl_users_roles.id
    * @var int(10) unsigned
    */
    public $id;
    
    /**
    * Field: acl_users_roles.user_id
    * @var int(10) unsigned
    */
    public $user_id;
    
    /**
    * Field: acl_users_roles.role_id
    * @var int(10) unsigned
    */
    public $role_id;
    

    /**
    * Get linked record from table "users"
    * where users.id = acl_users_roles.user_id
    * @return CoreUsers
    */
    public function getUser(){
        return db('core')->getObject('users', 'id', $this->user_id, 'CoreUsers');
    }

    /**
    * Get linked record from table "acl_roles"
    * where acl_roles.id = acl_users_roles.role_id
    * @return CoreAclRoles
    */
    public function getRole(){
        return db('core')->getObject('acl_roles', 'id', $this->role_id, 'CoreAclRoles');
    }


}

CoreAclUsersRolesTable::$generated = array(
);


/**
 * Class: CoreUsersTable to work with table "users".
 * THIS CLASS WAS AUTOMATICALLY GENERATED. ALL MANUAL CHANGES WILL BE LOST!
 * PUT YOUR CODE TO CLASS "CoreUsers" INSTEAD.
*/
class CoreUsersTable extends \Air\AbstractTable {

    static $fields;
    static $tablename = 'users';
    static $dbconfig = 'core';
    static $pk = array('id');
    static $generated;
    
    /**
    * Field: users.id
    * @var int(10) unsigned
    */
    public $id;
    
    /**
    * Field: users.email
    * @var varchar(100)
    */
    public $email;
    
    /**
    * Field: users.password
    * @var varchar(32)
    */
    public $password;
    
    /**
    * Field: users.username
    * @var varchar(25)
    */
    public $username;
    
    /**
    * Field: users.confirmed
    * @var int(10) unsigned (Default: '0')
    */
    public $confirmed = '0';
    
    /**
    * Field: users.created_ts
    * @var datetime
    */
    public $created_ts;
    


}

CoreUsersTable::$generated = array(
);
