<?php
// $Id: participantstats.php,v 1.12 2004/04/29 23:11:49 paul Exp $

/**
 * This class represents a participant stats entry
 *
 * This class represents a specific instance of the stats
 * for a given participant in the stats system.
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
 *
 * @access public
 */
class ParticipantStats {
    /**
     * The Id for the current participant stats info (read only)
     *
     * @access public
     * @type int
     */
    var $_id;
    var $_db;
    var $_project;
    /**
     * The StatsDate for the current participant stats info
     *
     * @access public
     * @type date
     */
    var $statsdate;
    var $_history;
    var $rs_rank;

    /**
     * Instantiates a new participant stats object, and loads it with the specified participant stats information.
     *
     * @access public
     * @return void
     * @param DBClass $ The database connectivity to use
     *             int The ID of the participant to load
     *             ProjectClass The project to retrieve stats for
     *             date The stats date to load
     */
    function ParticipantStats(&$dbPtr, &$project, $id = -1, $date = -1)
    {
        $this -> _db =& $dbPtr;
        $this -> _project =& $project;
        $this -> _id = $id;

        if ($id != -1) {
            $this -> load($id, $project, $date);
        }
    }

    /**
     * Loads the requested participant object using the current database connection
     * This function loads the requested date's stats data.
     *
     * @access public
     * @return bool
     * @param int $ The ID of the participant to load
     *             ProjectClass The project to load for
     *             date The date to load for
     */
    function load($id, &$project, $date)
    {
        $qs  = "SELECT day_rank, overall_rank, last_date + 1 - first_date as Days_Working,";
        $qs .= "                work_today, work_total, overall_rank_previous-overall_rank as Overall_Change,";
        $qs .= "                day_rank_previous-day_rank as Day_Change ";
        $qs .= "        FROM Email_Rank ";
        $qs .= "        WHERE id = " . $this->_db->prepare_int($id);
        $qs .= "            AND project_id = " . $this->_db->prepare_int($project->get_id());
        if ($date != -1) {
            // do..
        }

        $this -> _state = $this -> _db -> query_first ($qs);
    }

    /**
     * Loads the requested participant stats object using the current database connection.
     *
     *       This function loads the requested historical stats data available.
     *
     * @access public
     * @return ParticipantStats []
     * @param int $ The ID of the participant to load
     *             ProjectClass The project to load for
     *             date date to start from
     *             int Number of days prior to start (including start) to retrieve
     */
    function loadHistorical($id, $project, $start, $days_back)
    {
    }

    function get_stats_history($lastdays = -1)
    {
        $qs  = "SELECT to_char(date, 'dd-Mon-yyyy') as stats_date,";
        $qs .= "              SUM(work_units) as work_units";
        $qs .= "       FROM email_contrib ec, stats_participant sp";
        $qs .= "       WHERE ec.project_id=".$this->_db->prepare_int($this->_project->get_id())
        $qs .= "         AND (sp.id=".$this->_id." or sp.retire_to=".$this->_db->prepare_int($this->_id).")";
        $qs .= "         AND ec.id=sp.id";
        $qs .= "       GROUP BY date";
        $qs .= "       ORDER BY date DESC";
        if ($lastdays > 0) {
            $qs .= " LIMIT " . $this->_db->prepare_int($lastdays);
        }
        $dbstatshist = $this->_db->query($qs);
        $this->_history = $this->_db->fetch_paged_result($dbstatshist);
        return $this->_history;
    }

    /**
     * Returns the requested stats item for this ParticipantStats instance
     *
     * This routine retrieves the requested stats item (based on string index)
     *
     * @access public
     * @return variant
     * @param string $ The stats item to retrieve (i.e. FirstBlock)
     */
    function get_stats_item($name)
    {
        return $this -> _state -> {$name};
    }

    /**
     * Returns the available stats items
     *
     * This routine retrieves the available stats items
     *
     * @access public
     * @return string []
     */
    function getStatsItems()
    {
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
    function explode(&$obj)
    {
        $this->_state =& $obj;
    }
}

?>
