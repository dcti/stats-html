<?php
/**
 * Handles the DB abstraction layer for pgsql
 * 
 * @version 1.0
 */
class DB {
    /**
     * Database name to connect to.
     * 
     * @var string 
     */
    var $conn_string;

    /**
     * Database Server to connect to.
     * 
     * @var string 
     */
    var $server;

    var $link_id = 0;
    var $query_id = 0;
    var $record = array();

    var $errdesc = "";
    var $reporterror = 1;
	var $_connected = false;

    /**
     * Constructor
     */
    function DB($conn_string)
    {
        $this -> conn_string = $conn_string;
        $this -> _connect();
    } 

    /**
     * Connect to the database
     * 
     * @access private 
     */
    function _connect()
    {
        if(0 == $this -> link_id) {
            $this -> link_id = pg_pconnect($this -> conn_string);
            if(pg_connection_status() == PGSQL_CONNECTION_BAD) {
                $this -> halt("pgsql connection failed");
            } 
			$this->_connected = true;

            pg_query('SET STATEMENT_TIMEOUT=15000');
        } 
    } 

    /**
     * Return last sybase error message.
     * 
     * @return string returns the last message reported by the server
     * @access public 
     */
    function get_last_error()
    {
        return pg_last_error($this -> link_id);
    } 

    /**
     * Run Query
     * 
     * @param string $query_string Databasename to use
     * @return int Query id
     * @access public 
     * @todo -c"DB" Implement DB.query failed query error handling
     */
    function query($query_string)
    {
        if ($GLOBALS['debug'] >= DEBUG_SHOW_QUERY) {
            ?>
<!-- **** QUERY STRING ****
<?=$query_string?>
-->
<?
        }

        $this -> query_id = pg_query (/*$this -> link_id,*/ $query_string);
        if(!$this -> query_id) {
           // $this -> halt("Invalid SQL: " . $query_string);
        } 
        return $this -> query_id;
    } 

    // Function ....: fetch_array
    // Description .: Will take a query and fetch it into an associative array

    function fetch_array($query_id = -1, $query_string = "")
    { 
        // retrieve row
        if($query_id != -1)
            $this -> query_id = $query_id;

        if(isset($this -> query_id)) {
            $this -> record = pg_fetch_array($this -> query_id);
        } else {
            if(!empty($query_string)) {
                $this -> halt("Invalid query id (" . $this -> query_id . ") on this query: $query_string");
            } else {
                $this -> halt("Invalid query id " . $this -> query_id . " specified");
            } 
        } // end if
        return $this -> record;
    } 
    // Function ....: fetch_object
    function fetch_object($query_id = -1, $query_string = "")
    { 
        // retrieve row
        if($query_id != -1)
            $this -> query_id = $query_id;

        if(isset($this -> query_id)) {
            $this -> record = pg_fetch_object($this -> query_id);
        } else {
            if(!empty($query_string)) {
                $this -> halt("Invalid query id (" . $this -> query_id . ") on this query: $query_string");
            } else {
                $this -> halt("Invalid query id " . $this -> query_id . " specified");
            } 
        } // end if
        return $this -> record;
    } 

    // //////////////////////
    // Function: fetch_paged_result
    function fetch_paged_result($query_id, $start = -1, $limit = -1)
    {
      if($start > 1)
      {
        $this->data_seek($start-1, $query_id);
      }
      $ctr = 0;
      $result = array();
      while($tmp = $this->fetch_object($query_id))
      {
        $result[] = $tmp;
        $ctr++;
        if($limit != -1 && $ctr >= $limit) break;
      }
      return $result;
    }

    // //////////////////////
    // Function ....: free result
    // Description .: returns the memory (although PHP should do this automagically)
    // //////////////////////
    function free_result($query_id = -1)
    { 
        // retrieve row
        if($query_id != -1) {
            $this -> query_id = $query_id;
        } 
        return @pg_free_result($this -> query_id);
    } // end function free_result   
    // //////////////////////
    // Function ....: query_first
    // Description .: Executes a query and returns an object.
    // Useful if you are expecting a single row to be returned so you don't have to loop
    // //////////////////////
    function query_first($query_string)
    { 
        // does a query and returns first row
        $query_id = $this -> query($query_string);
        $returnobj = $this -> fetch_object($query_id, $query_string);
        $this -> free_result($query_id);
        return $returnobj;
    } // end function query_first   
    // //////////////////////
    // Function ....: data_seek
    // Description .: If you want to move around, generally don't use
    // //////////////////////
    function data_seek($pos = 1, $query_id = -1)
    { 
        // goes to row $pos
        if($query_id != -1) {
            $this -> query_id = $query_id;
        } 
        return pg_result_seek ($this -> query_id, $pos);
    } 

    // Function ....: num_rows
    // Description .: if you want the num rows returned
    function num_rows($query_id = -1)
    { 
        // returns number of rows in query
        if($query_id != -1) {
            $this -> query_id = $query_id;
        } 
        return pg_num_rows($this -> query_id);
    }
	
	// Function ....: close
    // Description .: closes (although PHP should do this automagically)
    function close()
    {
        return pgsql_close($this -> link_id);
    } 

    // Function ....: halt
    // Description .: If the DB class encounters an error, pretty it up first and don't show them the SQL stuff
    // and then mail a message saying what happened to you
    function halt($msg)
    {
        $this -> errdesc = $this->get_last_error();

        // prints warning message when there is an error
        if($this -> reporterror == 1) {

            exit;
        } 
    } 
} 

?>
