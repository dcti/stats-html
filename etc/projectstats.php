<?php
// $Id: projectstats.php,v 1.4 2004/04/29 20:44:42 paul Exp $
// ==========================================
// file: projectstats.inc
// This file contains the classes which
// represent projects in the stats
// system.  It abstracts the concept of a
// project and their stats into objects.
// ==========================================
/**
 * This class represents a project stats entry
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
class ProjectStats {
    /**
     * ** Internal Class variables go here **
     */
    // This needs to be filled in
    /**
     * ** END Internal Class variables **
     */

    /**
     * The Id for the current project stats info (read only)
     *
     * @access public
     * @type int
     */
    var $_id;
    var $_state;
    var $_time_working;
    var $_tot_units;

    /**
     * The StatsDate for the current project stats info
     *
     * @access public
     * @type date
     */
    var $StatsDate;

    function get_total_emails()
    {
        global $project_id;
        $qs = "select count(*) as total_emails from Email_Rank where PROJECT_ID=" . $this->_db->prepare_int($project_id);
        $ptPtr = $this -> _db -> query_first ($qs);
        return $ptPtr -> total_emails;
    }

    function get_total_teams()
    {
        global $project_id;
        $qs = "select count(*) as total_teams from Team_Rank where PROJECT_ID=" . $this->_db->prepare_int($project_id);
        $ptPtr = $this -> _db -> query_first ($qs);
        return $ptPtr -> total_teams;
    }

    function get_time_working()
    {
        return $this-> _time_working;
    }

    function get_tot_units()
    {
        return $this -> _tot_units;
    }
    /**
     * Instantiates a new [empty] project stats object
     *
     * @access public
     * @return void
     * @param DBClass $ The database connectivity to use
     */
    function ProjectStats($dbPtr,$project)
    {
        $this->_db = $dbPtr;
        $this->_id = $project;
        $this->LoadCurrent($project);
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
    }

    /**
     * Loads the requested project stats object using the current database connection.
     *
     *        This function loads the most current stats data available.
     *
     * @access public
     * @return bool
     * @param int $ The ID of the project to load
     */
    function loadCurrent($id)
    {
        global $project_id;
        // Get the latest record from Daily_Summary, store in $yest_totals
        $qs = "SELECT *
                FROM daily_summary NOLOCK
                WHERE project_id = " . $this->_db->prepare_int($project_id) . "
                ORDER BY date desc
                LIMIT 1";
        $this -> _state = $this -> _db -> query_first ($qs);

        $qs = "SELECT SUM(work_units) AS tot_units, MAX(date)+1-MIN(date) AS time_working
                FROM daily_summary
                WHERE project_id=" . $this->_db->prepare_int($project_id);
        $ptPtr = $this -> _db -> query_first ($qs);
        $this -> _tot_units = $ptPtr -> tot_units;
        $this -> _time_working = $ptPtr -> time_working;
    }

    /**
     * Loads the requested project stats object using the current database connection.
     *
     *        This function loads the requested historical stats data available.
     *
     * @access public
     * @return ProjectStats []
     * @param int $ The ID of the project to load
     *              date date to start from
     *              int Number of days prior to start (including start) to retrieve
     */
    function loadHistorical($id, $start, $days_back)
    {
    }

    /**
     * Returns the requested stats item for this ProjectStats instance
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
    function explode($parStatsInfo)
    {
    }
}

?>
