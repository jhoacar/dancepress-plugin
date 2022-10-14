<?php
namespace DancePressTRWA\Models;

use Exception;

/**
 * Main Parent Model.
 *
 * Other Models all extend this parent model and inherit its methods/properties.
 * @package DancePressTRWA\Models
 * @since 1.0
 */
abstract class Model
{

    /** @var object $db.	Wordpress global database object */
    protected $db;
    /** @var string $p. 	The current Wordpress installation's table prefix.*/
    protected $p;
    /** @var int $userid.  Current user id.*/
    protected $userid;
    /** @var array $weekdays.	An array of weekdays*/
    protected $weekdays;
    protected $error;
    /** @var string $sessionCondition.	A string to be used in SQL queries to store data in correct session.*/
    protected $sessionCondition;

    /**
     * Constructor for model and all child models.
     * Sets shared variables for all models.
     * @param string $sessionCondition Session condition to use in every SQL query.
     */
    public function __construct($sessionCondition = false)
    {
        $this->sessionCondition = $sessionCondition ? $sessionCondition : dance_getUserSessionCondition();
        global $wpdb;
        global $table_prefix;
        $this->db = $wpdb;
        $this->p = $table_prefix;
        $this->userid = get_current_user_id();
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        throw new Exception("Method not implemented");
    }
    
    protected function obfuscateEmail($email)
    {
        $link = 'mailto:' . $email;
        $obfuscatedLink = "";
        for ($i=0; $i<strlen($link); $i++) {
            $obfuscatedLink .= "&#" . ord($link[$i]) . ";";
        }
        return $obfuscatedLink;
    }

    public function getWeekdays()
    { //made public and to return value by Robert.

        $this->weekdays = array('1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday');
        return $this->weekdays;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getDB()
    {
        return $this->db;
    }

    public function getP()
    {
        return $this->p;
    }
    
    public function findById($id)
    {
        if (!$id) {
            return null;
        }
        
        $tableName = $this->getTableName();
        
        return $this->db->get_row("SELECT * FROM {$tableName} WHERE id = {$id}");
    }
    
    /**
     * Create a new table record. Only for use on tables that do not describe a session id
     * until this method has been improved to support these types of tables.
     *
     * @param array $data The data to use increating the new table record
     * @return null|integer
     */
    public function create($data)
    {
        if (empty($data)) {
            return false;
        }
        
        $format = array();
        
        foreach ($data as $value) {
            $type = gettype($value);
            
            if ($type == 'string') {
                $format[] = "%s";
            } elseif ($type == 'integer') {
                $format[] = "%d";
            } elseif ($type == 'float' || $type == 'double') {
                $format[] = "%d";
            } else {
                $format[] = "%s";
            }
        }
        
        $this->db->insert($this->getTableName(), $data, $format);

        return $this->db->insert_id;
    }
    
    /**
     *
     * @param int $id
     * @param array $data
     * @return number|false
     */
    public function update($id, $data)
    {
        return $this->db->update($this->getTableName(), $data, array(
            'id' => $id
        ));
    }
    
    /**
     *
     * @param int $id
     * @return number|false
     */
    public function delete($id)
    {
        $tableName = $this->getTableName();
        
        return $this->db->delete($tableName, array('id' => $id));
    }

    /**
     *
     * @param string $query Database query
     * @return int|false Number of rows affected/selected or false on error
     * @throws \Exception
     */
    public function query($query)
    {
        $result = $this->db->query($query);

        if ($this->db->last_error) {
            throw new \Exception("While executing query. Error: {$this->db->last_error}");
        }

        return $result;
    }
}
