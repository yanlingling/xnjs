<?php 

  
  /**
  * BaseUserPasswords class
  *
  * @author Pablo Kamil <pablokam@gmail.com>
  */
  abstract class BaseUserPasswords extends DataManager {
  
    /**
    * Column name => Column type map
    *
    * @var array
    * @static
    */
    static private $columns = array(
    	'id' => DATA_TYPE_INTEGER, 
    	'user_id' => DATA_TYPE_INTEGER, 
    	'password' => DATA_TYPE_STRING, 
    	'password_date' => DATA_TYPE_DATETIME,
    );
  
    /**
    * Construct
    *
    * @return BaseUserPasswords 
    */
    function __construct() {
      parent::__construct('UserPassword', 'user_passwords', true);
    } // __construct
    
    // -------------------------------------------------------
    //  Description methods
    // -------------------------------------------------------
    
    /**
    * Return array of object columns
    *
    * @access public
    * @param void
    * @return array
    */
    function getColumns() {
      return array_keys(self::$columns);
    } // getColumns
    
    /**
    * Return column type
    *
    * @access public
    * @param string $column_name
    * @return string
    */
    function getColumnType($column_name) {
      if(isset(self::$columns[$column_name])) {
        return self::$columns[$column_name];
      } else {
        return DATA_TYPE_STRING;
      } // if
    } // getColumnType
    
    /**
    * Return array of PK columns. If only one column is PK returns its name as string
    *
    * @access public
    * @param void
    * @return array or string
    */
    function getPkColumns() {
      return 'id';
    } // getPkColumns
    
    /**
    * Return name of first auto_incremenent column if it exists
    *
    * @access public
    * @param void
    * @return string
    */
    function getAutoIncrementColumn() {
      return 'id';
    } // getAutoIncrementColumn
    
    // -------------------------------------------------------
    //  Finders
    // -------------------------------------------------------
  
  
    /**
    * Do a SELECT query over database with specified arguments
    *
    * @access public
    * @param array $arguments Array of query arguments. Fields:
    * 
    *  - one - select first row
    *  - conditions - additional conditions
    *  - order - order by string
    *  - offset - limit offset, valid only if limit is present
    *  - limit
    * 
    * @return one or Users objects
    * @throws DBQueryError
    */
    function find($arguments = null) {
      if(isset($this) && instance_of($this, 'UserPasswords')) {
        return parent::find($arguments);
      } else {
        return Users::instance()->find($arguments);
      } // if
    } // find
    
    /**
    * Find all records
    *
    * @access public
    * @param array $arguments
    * @return one or UserPassword objects
    */
    function findAll($arguments = null) {
      if(isset($this) && instance_of($this, 'UserPasswords')) {
        return parent::findAll($arguments);
      } else {
        return UserPasswords::instance()->findAll($arguments);
      } // if
    } // findAll
    
    /**
    * Find one specific record
    *
    * @access public
    * @param array $arguments
    * @return UserPassword 
    */
    function findOne($arguments = null) {
      if(isset($this) && instance_of($this, 'UserPasswords')) {
        return parent::findOne($arguments);
      } else {
        return UserPasswords::instance()->findOne($arguments);
      } // if
    } // findOne
    
    /**
    * Return object by its PK value
    *
    * @access public
    * @param mixed $id
    * @param boolean $force_reload If true cache will be skipped and data will be loaded from database
    * @return User 
    */
    function findById($id, $force_reload = false) {
      if(isset($this) && instance_of($this, 'UserPasswords')) {
        return parent::findById($id, $force_reload);
      } else {
        return UserPasswords::instance()->findById($id, $force_reload);
      } // if
    } // findById
    
    /**
    * Return number of rows in this table
    *
    * @access public
    * @param string $conditions Query conditions
    * @return integer
    */
    function count($condition = null) {
      if(isset($this) && instance_of($this, 'UserPasswords')) {
        return parent::count($condition);
      } else {
        return UserPasswords::instance()->count($condition);
      } // if
    } // count
    
    /**
    * Delete rows that match specific conditions. If $conditions is NULL all rows from table will be deleted
    *
    * @access public
    * @param string $conditions Query conditions
    * @return boolean
    */
    function delete($condition = null) {
      if(isset($this) && instance_of($this, 'UserPasswords')) {
        return parent::delete($condition);
      } else {
        return UserPasswords::instance()->delete($condition);
      } // if
    } // delete
   
    
    /**
    * Return manager instance
    *
    * @return Users 
    */
    function instance() {
      static $instance;
      if(!instance_of($instance, 'UserPasswords')) {
        $instance = new UserPasswords();
      } // if
      return $instance;
    } // instance
  
  } // Users 

?>