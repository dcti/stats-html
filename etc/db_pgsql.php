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
    var $_conn_string;

    var $_link_id = 0;
    var $_query_id = 0;

	var $_connected = false;
	var $g_queries_array = array();
    /**
     * Constructor
     */
    function DB($conn_string)
    {
        $this -> _conn_string = $conn_string;
        $this -> _connect();
    }

    /**
     * Connect to the database
     *
     * @access private
     */
    function _connect()
    {
        if(0 == $this -> _link_id) {
            $this -> _link_id = @pg_pconnect($this -> _conn_string);
            if(pg_connection_status() == PGSQL_CONNECTION_BAD || $this -> _link_id == false) {
                $this -> _error();
				trigger_error("Connection to Database Failed",E_USER_ERROR);
				return false;
            }
			$this->_connected = true;
            pg_query('SET STATEMENT_TIMEOUT=30000');

			return true;
        }
    }

    /**
     * Return last error message.
     *
     * @return string returns the last message reported by the server
     * @access public
     */
    function get_last_error()
    {
		if ($this -> _link_id) {
	        return pg_last_error($this -> _link_id);
		}
		return false;
    }

	function _error( $p_query=null ) {
		if ( null !== $p_query ) {
			error_parameters( $this-> get_last_error(), $p_query );
		} else {
			error_parameters( $this -> get_last_error() );
		}
	}
    /**
     * Run Query
     *
     * @param string $query_string Databasename to use
     * @return int Query id
     * @access public
     */
    function query($p_query)
    {
        $this -> _query_id = @pg_query ($this -> _link_id, $p_query);
        if(!$this -> _query_id) {
			$this -> _error ($p_query);
			array_push ( $this->g_queries_array, array($p_query,false) );
          	trigger_error("DB Query Failed",E_USER_ERROR);
			return false;
        }
		array_push ( $this->g_queries_array, array($p_query,true) );
        return $this -> _query_id;
    }

    /**
      * Run Bound Query
      *
      * @param string $query_string Databasename to use
      * @return int Query id
      * @access public
      */
      function query_bound($p_query, $arr_parms)
      {
		// Check to see if query binding is implemented by extension, otherwise
		// emulate it
		if(in_array('pg_query_params', get_extension_funcs('pgsql')))
		{
			$this -> _query_id = @pg_query_params($this -> _link_id, $p_query, $arr_parms);

		}
		else
		{
			$lastoffset = 0;
			while (preg_match('/\$(\d+)/', $p_query, $matches, PREG_OFFSET_CAPTURE, $lastoffset)) {
    				$i = (int)$matches[1][0];
				if ($i >= 1 && $i <= count($arr_parms)) {
					if(is_string($arr_parms[$i-1]))
						$replace = "'" . pg_escape_string($arr_parms[$i-1]) . "'";
					else if(is_integer($arr_parms[$i-1]) || is_float($arr_parms[$i-1]))
						$replace = (float)$arr_parms[$i-1];
					else {
						echo("Invalid argument type passed to query_bound(): $i");
						exit(1);
					}
					$p_query = substr($p_query, 0, $matches[1][1]-1) . $replace . substr($p_query, $matches[1][1] + strlen($matches[1][0]));
					$lastoffset = $matches[1][1] + strlen($replace);
				} else {
					$lastoffset = $matches[1][1] + strlen($matches[1][0]);
				}
			}
			$this -> _query_id = @pg_query($this -> _link_id, $p_query);
		}
		if(!$this -> _query_id) {
     			$this -> _error ($p_query);
			array_push ( $this->g_queries_array, array($p_query,false) );
			return false;
		}
		array_push ( $this->g_queries_array, array($p_query,true) );
		return $this -> _query_id;
      }



    /* Fetch result as an array
	*/
    function fetch_array($query_id = null)
    {
        if($query_id != -1)
            $this -> _query_id = $query_id;

        if(isset($this -> _query_id)) {
            $record = pg_fetch_array($this -> _query_id);
        } else {
            trigger_error("Invalid Query ID");
        }
        return $record;
    }

	/* Fetch result as an object
	*/
    function fetch_object($query_id = -1)
    {
        // retrieve row
        if($query_id != -1)
            $this -> _query_id = $query_id;

        if(isset($this -> _query_id)) {
            $record = pg_fetch_object($this -> _query_id);
        } else {
			trigger_error("Invalid Query ID");
        }
        return $record;
    }

    /* fetch_paged_result
	*/
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
            $this -> _query_id = $query_id;
        }
        return @pg_free_result($this -> _query_id);
    }

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
    }

    // //////////////////////
    // Function ....: data_seek
    // Description .: If you want to move around, generally don't use
    // //////////////////////
    function data_seek($pos = 1, $query_id = -1)
    {
        // goes to row $pos
        if($query_id != -1) {
            $this -> _query_id = $query_id;
        }
        return pg_result_seek ($this -> _query_id, $pos);
    }


    /* if you want the num rows returned
	*/
    function num_rows($query_id = -1)
    {
        // returns number of rows in query
        if($query_id != -1) {
            $this -> _query_id = $query_id;
        }
        return pg_num_rows($this -> _query_id);
    }

	/* close
	*/
    function close()
    {
        return pgsql_close($this -> _link_id);
    }


    function prepare_int($integer)
    {
        return $integer;
    }

    function prepare_string($string)
    {
        return $string;
    }

}

?>
