<?php
// $Id: teamstats.php,v 1.10 2005/04/01 16:00:49 decibel Exp $

/***
 * This class represents a team stats entry
 *
 * This class represents a specific instance of the stats
 * for a given team in the stats system.
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
 *
 * @access public
 ***/
class TeamStats
{
    var $_db;
    var $_project;
    var $_state;

    /***
     * Instantiates a new [empty] participant stats object
     *
     * @access public
     * @return void
     * @param DBClass The database connectivity to use
     ***/
    function TeamStats(&$dbPtr, &$prjPtr, $team_id = -1, $stats_date = -1)
    {
        $this->_db =& $dbPtr;
        $this->_project =& $prjPtr;

        if($team_id != -1)
            $this->load($team_id, $stats_date);
    }

    /***
     * Loads the requested team stats object
     *
     *  This function loads the requested date's stats data. (or lastest if $stats_date == -1)
     *
     * @access public
     * @return bool
     * @param int The ID of the team to load
     *        date The date to load for
     ***/
    function load($id, $date)
    {
        $qs  = 'SELECT first_date, last_date, day_rank, day_rank_previous, overall_rank, overall_rank_previous';
        $qs .= '        , members_today, members_overall, members_current';
        $qs .= '        , work_today, work_total';
        $qs .= '        , last_date::DATE - first_date::DATE + 1 AS days_working ';
        $qs .= 'FROM team_rank ';
        $qs .= 'WHERE team_id = ' . $this->_db->prepare_int($id);
        $qs .= ' AND project_id = '. $this->_db->prepare_int($this->_project->get_id());
        if($date == -1)
        {
            $qs .= " AND last_date = (SELECT MAX(last_date) FROM team_rank t2 WHERE t2.team_id = team_rank.team_id AND t2.project_id = team_rank.project_id)";
        }
        else
        {
            $qs .= " AND last_date = '" + $date + "'::DATE";
        }

        $this->_state = $this->_db->query_first($qs);
    }

    /***
     * Returns the requested stats item for this TeamStats instance
     *
     * This routine retrieves the requested stats item (based on string index)
     *
     * @access public
     * @return variant
     * @param string The stats item to retrieve (i.e. FirstBlock)
     */
    function get_stats_item($name)
    {
        return $this->_state->{$name};
    }

    /***
     * Returns the available stats items
     *
     * This routine retrieves the available stats items
     *
     * @access public
     * @return string[]
     */
    function get_stats_items()
    {
    }

    /***
     * Explodes this object's internal state with the passed object
     *
     * @access protected
     * @param DBObject The new state for this object
     ***/
    function explode(&$obj)
    {
        $this->_state =& $obj;
    }

}
?>
