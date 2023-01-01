<?php
// $Id: ogrstubspace.php,v 1.1 2008/04/24 18:09:06 thejet Exp $
// ==========================================
// This file contains the classes which
// represent ogr project stats in the
// system.  It abstracts the concept of ogr
// stubspaces and their stats into objects.
// ==========================================
/**
 * This class represents an ogr stubspace
 * 
 * This class represents a specific ogr stubspace
 * for the ogr project.
 *
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
 *
 * @access public
 **/
class OGRStubspace
{
    /**
     * ** Internal Class variables go here **
     */
    var $_db;
    var $_project;
    var $_state;
    var $_stats;
    /**
     * ** END Internal Class variables **
     */

    /**
     * Constructor(s)
     *
     **/
    /**
     * Instantiates a new participant object, and loads it with the specified participant's information.
     *
     * @access public
     * @return void
     * @param DBClass $dbPtr The database connectivity to use
     * @param ProjectClass $prjPtr The current project
     * @param int $id The ID of the stubspace to load
     */
    function __construct($dbPtr, $prjPtr, $id = -1)
    {
        $this -> _db = $dbPtr;
        $this -> _project = $prjPtr;

        $id = (int)$id;
        if($id > 0)
        {
			$this -> load($id);
		}
    }
     
    /**
     * Property Accessor(s)
     *
     **/
    /* Return current project id
     *
     */
    function get_project_id()
    {
        return $this -> _state -> project_id;
    }

     /* Return current stubspace id
     *
     */
    function get_stubspace_id()
    {
        return $this -> _state -> stubspace_id;
    }
   
    /* Return stubspace name
     *
     */
    function get_name()
    {
        return $this -> _state -> name;
    }

    /* Return total number of stubs
     *
     */
    function get_total_stubs()
    {
        return $this -> _state -> total_stubs;
    }

    /**
     * Public Methods
     **/
    /**
     * Loads the requested ogr stubspace object using the current database connection
     *
     * @access public
     * @return bool
     * @param int $id The ID of the stubspace to load
     */
    function load($id)
    {
        $qs = 'SELECT project_id, stubspace_id, name, total_stubs FROM ogr_stubspace WHERE project_id = $1 AND stubspace_id = $2';
        $this -> _state = $this -> _db -> query_bound_first ($qs, array( $this->_project->get_id(), $id ));
    }       
    
    /**
     * Returns the current ParticipantStats object for this participant
     *
     * This routine is "load-on-demand", meaning that the data is retrieved from the DB
     * on first access, and then from a local variable thereafter.
     *
     * @access public
     * @return OGRStubspaceStats
     */
    function &get_current_stats()
    {
        if($this->_stats == null)
        {
            $this->_stats = new OGRStubspaceStats($this->_db, $this->_project, $this->get_stubspace_id());
        }
        return $this->_stats;
    }
    
    /**
     * Returns the list of OGR Stubspaces
     *
     * This routine retrieves the list of OGR stubspaces for the specified project_id.
     * 
     * @access public
     * @return OGRStubspace []
     * @param ProjectClass $prjPtr The project to retrieve the list for [required for static invocation]
     * @param DBClass $dbPtr The database to retrieve the list from [required for static invocation]
     */
    public static function &get_stubspace_list($prjPtr = null, $dbPtr = null)
    {
      if($prjPtr == null)
      {
        $prjPtr = $this->_project;
      }
      if($dbPtr == null)
      {
        $dbPtr = $this->_db;
      }
      
      $qs = "SELECT project_id, stubspace_id, name, total_stubs FROM ogr_stubspace WHERE project_id = $1 ORDER BY stubspace_id ASC";
      $result = array();
      
      $query = $dbPtr->query_bound($qs, array( $prjPtr->get_id() ));
      if(!$query)
      {
        return $result;
      }
      
      $dbResults =& $dbPtr->fetch_paged_result($query);
      $dbCount = count($dbResults);
      for($i = 0; $i < $dbCount; $i++)
      {
        $newResult = new OGRStubspace($dbPtr, $prjPtr);
        $newResult->explode($dbResults[$i]);
        $result[] = $newResult;
        unset($newResult);
      }
      return $result;
    }

    /**
     * Turns the current database-oriented object/array into an internal representation
     *
     * This routine provides for an easy way to turn database-oriented objects/arrays
     * into the generic internal representation that we're using, avoiding a database hit
     * in cases where you already have the participants information (i.e. when loading
     * friends/neighbors).  This is functionally similar to object deserialization.
     *
     * @access protected
     * @return bool
     * @param DBVariant $ This is the object/array from the database server which contains the data for the desired participant
     */
    function explode($obj, $stats = null)
    {
        $this->_state =& $obj;
        $this->_stats =& $stats;
    }
}

/**
 * This class represents an ogr stubspace stats entry
 *
 * This class represents a specific instance of the stats
 * for a given project in the stats system.
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
 *
 * @access public
 */
class OGRStubspaceStats {
    /**
     * ** Internal Class variables go here **
     */
    var $_db;
    var $_project;
    var $_stubspace_id;
    var $_state;
    /**
     * ** END Internal Class variables **
     */

    /**
     * Public Property Accessor(s)
     **/
    function get_project_id()
    {
      return $this->_state->project_id;
    }

    function get_stubspace_id()
    {
      return $this->_state->stubspace_id;
    }

    function get_stats_date()
    {
      return $this->_state->stats_date;
    }

    function get_stubs_done()
    {
      return $this->_state->stubs_done;
    }

    function get_stubs_verified()
    {
      return $this->_state->stubs_verified;
    }

    function get_stub_delta()
    {
      return $this->_state->stub_delta;
    }

    function get_3day_avg_delta()
    {
      return $this->_state->delta_3day_average;
    }

    function get_7day_avg_delta()
    {
      return $this->_state->delta_7day_average;
    }

    function get_14day_avg_delta()
    {
      return $this->_state->delta_14day_average;
    }

    function get_30day_avg_delta()
    {
      return $this->_state->delta_30day_average;
    }

    /**
     * Instantiates a new OGR Stubspace stats object
     *
     * @access public
     * @return void
     * @param DBClass $ The database connectivity to use
     */
    function __construct($dbPtr, $prjPtr, $id = -1, $date = -1)
    {
        $this->_db =& $dbPtr;
        $this->_project =& $prjPtr;
        if($id > 0)
        {
            if($date > 0)
            {
                $this->load($id, $date);
            }
            else
            {
                $this->load_current($id);
            }
        }
    }

    /**
     * Loads the requested project stats object using the current database connection
     *
     *        This function loads the requested date's stats data.
     *
     * @access public
     * @return bool
     * @param int $ The ID of the project to load
     *              date The date to load for
     */
    function load($id, $date)
    {
        $qs =  "SELECT project_id, stubspace_id, stats_date, ";
        $qs .= "       stubs_done, stubs_verified, stub_delta, ";
        $qs .= "       delta_3day_average, delta_7day_average, ";
        $qs .= "       delta_14day_average, delta_30day_average ";
        $qs .= "  FROM ogr_stubspace_stats ";
        $qs .= " WHERE project_id = $1 AND stubspace_id = $2 ";
        $qs .= "   AND stats_date = $3; ";

        $this->_state = $this->_db->query_bound_first($qs, array(
								$this->_project->get_id(),
								$id,
								$date
							    ));
    }

    /**
     * Loads the requested stats object using the current database connection.
     *
     * This function loads the most current stats data available.
     *
     * @access public
     * @return bool
     * @param int $ The ID of the project to load
     */
    function load_current($id)
    {
        $qs =  "SELECT project_id, stubspace_id, stats_date, ";
        $qs .= "       stubs_done, stubs_verified, stub_delta, ";
        $qs .= "       delta_3day_average, delta_7day_average, ";
        $qs .= "       delta_14day_average, delta_30day_average ";
        $qs .= "  FROM ogr_stubspace_stats ";
        $qs .= " WHERE project_id = $1 AND stubspace_id = $2 ";
        $qs .= " ORDER BY stats_date DESC LIMIT 1; ";

        $this->_state = $this->_db->query_bound_first($qs, array(
								$this->_project->get_id(),
								$id
							    ));
    }

    function &get_stats_history($dbPtr, $prjPtr, $stubspace_id, $lastX)
    {
        $qs =  "SELECT project_id, stubspace_id, stats_date, ";
        $qs .= "       stubs_done, stubs_verified, stub_delta, ";
        $qs .= "       delta_3day_average, delta_7day_average, ";
        $qs .= "       delta_14day_average, delta_30day_average ";
        $qs .= "  FROM ogr_stubspace_stats ";
        $qs .= " WHERE project_id = $1 AND stubspace_id = $2 ";
        $qs .= " ORDER BY stats_date DESC LIMIT $3";

        $queryData = $dbPtr->query_bound($qs, array(
						$prjPtr->get_id(),
						$stubspace_id,
						$lastX
					));
        $results =& $dbPtr->fetch_paged_result($queryData);
        $cnt = count($results);
        $retVal = array();
        for($i = 0; $i < $cnt; $i++)
        {
            $tmp = new OGRStubspaceStats($dbPtr, $prjPtr);
            $tmp->explode($results[$i]);
            $retVal[] = $tmp;
            unset($tmp);
        }

        return $retVal;
    }


    /**
     * Turns the current database-oriented object/array into an internal representation
     *
     * This routine provides for an easy way to turn database-oriented objects/arrays
     * into the generic internal representation that we're using, avoiding a database hit
     * in cases where you already have the participants information (i.e. when loading
     * friends/neighbors).  This is functionally similar to object deserialization.
     *
     * @access protected
     * @return bool
     * @param DBVariant $ This is the object/array from the database server which contains the data for the desired participant stats object
     */
    function explode($stats)
    {
      $this->_state =& $stats;
    }
}

?>
