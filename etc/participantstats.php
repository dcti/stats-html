<?php
// $Id: participantstats.php,v 1.5 2003/08/25 18:35:50 thejet Exp $
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
     * 
     *       This function loads the requested date's stats data.
     * 
     * @access public 
     * @return bool 
     * @param int $ The ID of the participant to load
     *             ProjectClass The project to load for
     *             date The date to load for
     */
    function load($id, &$project, $date)
    {
        $qs = "select DAY_RANK, OVERALL_RANK, LAST_DATE + 1 - FIRST_DATE as Days_Working, 
          WORK_TODAY as TODAY,
          WORK_TOTAL as TOTAL,
          OVERALL_RANK_PREVIOUS-OVERALL_RANK as Overall_Change,
          DAY_RANK_PREVIOUS-DAY_RANK as Day_Change
        from Email_Rank
        where id = $id
          and PROJECT_ID = " . $project->get_id();
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

    /**
     * This function returns an array of participant objects representing the neighbors of the current participant
     * 
     * This function is a "load on demand" function, so the first call loads the
     * participant's neighbors from the database, and subsequent calls access the local
     * data.
     * 
     * @access public 
     * @return Participant []
     */
    function getNeighborsObj()
    {
        $qs = "select r.id, p.listmode, p.email, p.contact_name, r.OVERALL_RANK,
          r.LAST_DATE + 1 - r.FIRST_DATE as Days_Working,
          WORK_TODAY as TODAY,
          WORK_TOTAL as TOTAL,
          (r.OVERALL_RANK_PREVIOUS-r.OVERALL_RANK) as Overall_Change,
          (r.DAY_RANK_PREVIOUS-r.DAY_RANK) as Day_Change
        from STATS_Participant p, Email_Rank r
        where p.id = r.id
          and PROJECT_ID = ".$this->_project->get_id()."
          and (r.OVERALL_RANK < (" . $this -> rs_rank -> overall_rank . "+5))
          and (r.OVERALL_RANK > (" . $this -> rs_rank -> overall_rank . "-5))
        order by r.OVERALL_RANK LIMIT 18";

        $dbneighbors = $this -> _db -> query($qs);
        $i = 0;
        while ($temp = $this -> _db -> fetch_object($dbneighbors)) {
            $neighbors[$i++] = $temp;
        } 
        return $neighbors;
    } 

    function get_stats_history($lastdays = -1)
    {
        $qs = "SELECT to_char(date, 'dd-Mon-yyyy') as stats_date, 
                      SUM(work_units) as work_units
               FROM email_contrib ec, stats_participant sp 
               WHERE ec.project_id=".$this->_project->get_id()."
                 AND ec.id=sp.id
                 AND (sp.id=".$this->_id." or sp.retire_to=".$this->_id.")
               GROUP BY date
               ORDER BY date DESC";
        if ($lastdays > 0) {
            $qs .= " LIMIT $lastdays";
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
      function explode(&$obj) { $this->_state =& $obj; }
} 

?>
