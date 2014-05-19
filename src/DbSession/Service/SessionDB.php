<?php

namespace DbSession\Service;

use Zend\Session\SaveHandler\SaveHandlerInterface;

/**
 * Description of SessionDB
 *
 * @author rab
 */
class SessionDB  implements SaveHandlerInterface 
{

    /**
     * Session Save Path
     *
     * @var string
     */
    protected $sessionSavePath;

    /**
     * Session Name
     *
     * @var string
     */
    protected $sessionName;

    /**
     * Lifetime
     * @var int
     */
    protected $lifetime;

    /**
     * Constructor
     *
     */
    public function __construct( $dbConfig )
    {

        $this->dbconn  = mysql_connect(
            $dbConfig['host'],
            $dbConfig['username'], 
            $dbConfig['password']
        );

        if ( $this->dbconn ) {
            return mysql_select_db($dbConfig['dbname'], $this->dbconn);
        }
    }


    /**
     * Open the session
     * 
     * @return bool
     */
    public function open( $savePath, $name )
    {
        $this->sessionSavePath = $savePath;
        $this->sessionName     = $name;
        $this->lifetime        = ini_get('session.gc_maxlifetime');

        return true;

    }


    /**
     * Close the session
     * 
     * @return bool
     */
    public function close() 
    {

        return mysql_close($this->dbconn);
    }


    /**
     * Read the session
     * 
     * @param int session id
     * @return string string of the sessoin
     */
    public function read($id) 
    {

            $id     = mysql_real_escape_string($id);

            $sql    = "SELECT `data` FROM `session` " .
                      "WHERE id = '$id'";

            if ( $result = mysql_query($sql, $this->dbconn)) {
                if ( mysql_num_rows($result) ) {
                    $record = mysql_fetch_assoc($result);
                    return $record['data'];
                }
            }

            return '';
    }


    /**
     * Write the session
     * 
     * @param int session id
     * @param string data of the session
     */
    public function write($id, $data ) 
    {

            $data   = (string) $data ;

            $dbdata = array(
                'modified' => time(),
                'data'     => mysql_real_escape_string( $data  ) ,
            );

            $selectSql = "SELECT * FROM session 
                          WHERE id = '$id' AND name = '{$this->sessionName}' ";

            $rs          = mysql_query( $selectSql, $this->dbconn );

            if ( $rs = mysql_query( $selectSql , $this->dbconn)) {
                if ( mysql_num_rows($rs) ) {

                    $updateSql = "UPDATE `session` SET 
                           `modified`= '".$dbdata['modified'] . "' ,
                           `data`= '".$dbdata['data']. "' 
                            WHERE id= '$id' AND name = '{$this->sessionName}' ";


                    mysql_query( $updateSql ,  $this->dbconn );
                    return true;

                }
            }


            $dbdata['lifetime']  = $this->lifetime;
            $dbdata['id']        = $id;
            $dbdata['name']      = $this->sessionName;

            $insertSql =  "INSERT INTO session (". implode(',' , array_keys($dbdata)) .")"
                           ."VALUES ('" . implode("','" , array_values( $dbdata )). "')";

            return mysql_query( $insertSql, $this->dbconn);

    }


    /**
     * Destoroy the session
     * 
     * @param int session id
     * @return bool
     */
    public function destroy($id) 
    {

            $sql = sprintf("DELETE FROM `session` WHERE `id` = '%s'", $id);
            return mysql_query($sql, $this->dbconn);
    }


    /**
     * Garbage Collector
     * 
     * @param int life time (sec.)
     * @return bool
     */
    public function gc( $maxlifetime ) 
    {

            $sql = sprintf("DELETE FROM `session` WHERE `modified` < '%s'",
                    mysql_real_escape_string(time() - $maxlifetime)
            );

            return mysql_query($sql, $this->dbconn);
    }

}