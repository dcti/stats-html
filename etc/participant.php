<?php 
// $Id: participant.php,v 1.6 2003/08/01 23:51:05 paul Exp $
/**
 * This class represents a participant
 * 
 * This class represents a participant in the stats system.
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
 * 
 * 
 *   retire_date    | date                   |
 * contact_phone  | character varying(20)  | not null default ''
 * 
 * @access public 
 */
class Participant {

    /**
     * ** Internal Class Variables **
     */
    var $_db;
    var $_project;
    var $_state;
    var $_friends;

    /**
     * ** End Internal class variables **
     */

    function get_id()
    {
        return $this -> _state -> id;
    } 

    /**
     * The Email for the current participant
     * 
     * @access public 
     * @type string
     */
    function get_email()
    {
        return $this -> _state -> email;
    } 
    /**
     * ** NOTE: We may want to ensure validity here... **
     */
    function set_email($value)
    {
        $this -> _state -> email = $value;
    } 

    /**
     * The Password for the current participant
     * 
     * @access public 
     * @type string
     */
    function get_password()
    {
        return $this -> _state -> password;
    } 
    function set_password($value)
    {
        $this -> _state -> password = $value;
    } 

    /**
     * The listmode of this user
     * 
     * @access public 
     * @type int
     */
    function get_list_mode()
    {
        return $this -> _state -> listmode;
    } 
    function set_list_mode($value)
    {
        $this -> _state -> listmode = $value;
    } 

    /**
     * Which non-profit did this participant vote for?
     * 
     * @access public 
     * @type int
     */
    function get_non_profit()
    {
        return $this -> _state -> nonprofit;
    } 
    function set_non_profit($value)
    {
        $this -> _state -> nonprofit = $value;
    } 


    /**
     * Has this participant retired their record to another participant?
     * 
     * @access public 
     * @type int
     */
    function get_retire_to()
    {
        return $this -> _state -> retire_to;
    } 
    function set_retire_to($value)
    {
        $this -> _state -> retire_to = $value;
    } 

    /**
     * The friends of this participant (up to 6)
     * 
     * @access public 
     * @type int[]
     */
    /**
     * ** NOTE: We need to ensure the index is valid in both of these ... **
     */
    function getFriends($index)
    {
        /* @todo friends */
        return $this -> _friends[$index];
    } 
    function setFriends($index, $value)
    {
        $this -> _friends[$index] = $value;
    } 

    /**
     * Demographic: Year of birth
     * 
     * @access public 
     * @type int
     */
    function get_dem_yob()
    {
        return $this -> _state -> dem_yob;
    } 
    function set_dem_yob($value)
    {
        $this -> _state -> dem_yob = $value;
    } 

    /**
     * Demographic: How participant learned about distributed.net
     * 
     * @access public 
     * @type smallint
     */
    function get_dem_heard()
    {
        return $this -> _state -> dem_heard;
    } 
    function set_dem_heard($value)
    {
        $this -> _state -> dem_heard = $value;
    } 

    /**
     * Demographic: Gender of participant
     * 
     * @access public 
     * @type string
     */
    function get_dem_gender()
    {
        return $this -> _state -> dem_gender;
    } 
    function set_dem_gender($value)
    {
        $this -> _state -> dem_gender = $value;
    } 

    /**
     * Demographic: Motivation for running distributed.net client
     * 
     * @access public 
     * @type smallint
     */
    function get_dem_motivation()
    {
        return $this -> _state -> dem_motivation;
    } 
    function set_dem_motivation($value)
    {
        $this -> _state -> dem_motivation = $value;
    } 

    /**
     * Demographic: Country of origin
     * 
     * @access public 
     * @type string
     */
    function get_dem_country()
    {
        return $this -> _state -> dem_country;
    } 
    function set_dem_country($value)
    {
        $this -> _state -> dem_country = $value;
    } 

    /**
     * Contact name for the participant
     * 
     * @access public 
     * @type string
     */
    function get_contact_name()
    {
        return $this -> _state -> contact_name; 
    } 
    function set_contact_name($value)
    {
        $this -> _state -> contact_name = $value;
    } 

    /**
     * Contact phone for the participant
     * 
     * @access public 
     * @type string
     */
    function get_contact_phone()
    {
        return $this -> _state -> contact_phone;
    } 
    function set_contact_phone($value)
    {
        $this -> _state -> contact_phone = $value;
    } 

    /**
     * Participant motto
     * 
     * @access public 
     * @type string
     */
    function get_motto()
    {
        return $this -> _state -> motto;
    } 
    function set_motto($value)
    {
        $this -> _state -> motto = $value;
    } 

    /**
     * Date that this account was retired
     * 
     * @access public (readonly)
     * @type datetime
     */
    var $_retireDate;
    function getRetireDate()
    {
        return $this > _retireDate;
    } 


    function get_display_name()
    {
        if ($this -> _state -> listmode == 0 || $this -> _state -> listmode == 8 || $this -> _state -> listmode == 9) {
            $listas = $this -> get_email();
        } else if ($this -> _state -> listmode == 1) {
            $listas = "Participant #" . number_style_convert($this -> get_id());
        } else if ($this -> _state -> listmode == 2) {
            if ($this -> get_contact_name() == "")
                $listas = "Participant #" . number_style_convert($this -> get_id());
            else
                $listas = $this -> get_contact_name();
        } else {
            $listas = "Record error for #" . number_style_convert($this -> get_id()) . "!";
        } 
        return $listas;
    } 
    /**
     * Instantiates a new participant object, and loads it with the specified participant's information.
     * 
     * @access public 
     * @return void 
     * @param DBClass $ The database connectivity to use
     *                                  ProjectClass The current project
     *                                  int The ID of the participant to load
     */
    function Participant($dbPtr, $prjPtr, $id )
    {
        $this -> _db = $dbPtr;
        $this -> _project = $prjPtr;
		if (!is_null($id)) {
		    if($id != -1)
		    {
		        $this -> load($id);
		    } else {
				// load default values
			}
		}
    } 

    /**
     * Loads the requested participant object using the current database connection
     * 
     * @access public 
     * @return bool 
     * @param int $ The ID of the participant to load
     */
    function load($id)
    {
        $qs = "select * from STATS_Participant where id = $id and listmode < 10";
        $this -> _state = $this -> _db -> query_first ($qs);
    } 

    /**
     * Saves the current user to the database
     * 
     * This routine saves the current user to the database, as a secondary result
     * it also refreshes the internal data for the user based on any new information
     * that may have appeared in the database since the last load.
     * 
     * @access public 
     * @return bool 
     */
    function save()
    {
    } 

    /**
     * Deletes the current user from the database
     * 
     * This routine removes the current user from the database, the end result of this
     * routine is an empty participant object.
     * 
     * @access public 
     * @return bool 
     */
    function delete()
    {
    } 

    /**
     * Retires this participant into another participant's account
     * 
     * This routine retires the current participant into the requested participant's
     * account.  Passing 0 to this routine "un-retires" the participant.
     * 
     * @access public 
     * @return bool 
     * @param int $ The participant to retire this participant into
     */
    function retire($new_id)
    {
    } 

    /**
     * This function returns an array of participant objects representing the friends of the current participant
     * 
     * This function is a "load on demand" function, so the first call loads the
     * participant's friends from the database, and subsequent calls access the local
     * data.
     * 
     * @access public 
     * @return Participant []
     */
    function getFriendsObj()
    {
    } 

    /**
     * Returns the current ParticipantStats object for this participant
     * 
     * This routine is "load-on-demand", meaning that the data is retrieved from the DB
     * on first access, and then from a local variable thereafter.
     * 
     * @access public 
     * @return ParticipantStats 
     */
         var $_stats;
         function &get_current_stats()
         {
           if($this->_stats == null)
           {
             $this->_stats = new ParticipantStats($this->_db, $this->_project, $this->get_id());
           }
           return $this->_stats;
         }

    /**
     * Returns the requested amount of historical stats information for this participant
     * 
     * This routine retrieves the requested number of previous days of stats information
     * for this participant.  You specify the start date, and the number of previous days
     * to retrieve.
     * 
     * @access public 
     * @return ParticipantStats []
     * @param date $ The date to start retrieval
     *                                  int The number of days prior to $start to retrieve data for
     */
    function getStatsHistory($start, $getDays)
    {
    } 

    function get_ranked_list($source = 'o', $start, $limit)
    { 
        // First, we need to determine which query to run...
        if ($source == 'y') {
            $qs = "select r.id, r.first_date as first, r.LAST_DATE as last, r.WORK_TODAY as blocks,
						LAST_DATE + 1 - FIRST_DATE as days_working,
						r.DAY_RANK as rank, r.DAY_RANK_PREVIOUS - r.DAY_RANK as change,
						p.email, p.listmode, p.contact_name
						from Email_Rank r, STATS_Participant p
						where DAY_RANK <= $start + $limit and DAY_RANK >= $start and r.id = p.id and p.listmode < 10 and 	r.PROJECT_ID = " . $this -> _project . "
						order by r.DAY_RANK, r.WORK_TODAY desc";
        } else {
            $qs = "select r.id, r.first_date as first, r.LAST_DATE as last, r.WORK_TOTAL as blocks,
						LAST_DATE + 1 - FIRST_DATE as days_working,
						r.OVERALL_RANK as rank, r.OVERALL_RANK_PREVIOUS - r.OVERALL_RANK as change,
						p.email, p.listmode, p.contact_name
						from Email_Rank r, STATS_Participant p
						where OVERALL_RANK <= $start + $limit and OVERALL_RANK >= $start and r.id = p.id and p.listmode < 	10 and r.PROJECT_ID = " . $this -> _project . "
						order by r.OVERALL_RANK, r.WORK_TOTAL desc";
        } 

        $queryData = $this -> _db -> query($qs);
        $total = $this -> _db -> num_rows($queryData);
        $result = &$this -> _db -> fetch_paged_result($queryData, $start, $limit);
        $cnt = count($result);
        for($i = 0; $i < $cnt; $i++) {
            $partTmp = new Participant($this -> _db, $this -> _project, null);
            $statsTmp = new ParticipantStats($this -> _db, $this -> _project);
            $statsTmp -> explode($result[$i]);
            $partTmp -> explode($result[$i], $statsTmp);
            $retVal[] = $partTmp;
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
     * @param DBVariant $ This is the object/array from the database server which contains the data for the desired participant
     */

    function explode($obj, $stats = null)
    {
        $this -> _state = &$obj;
        $this -> _stats = &$stats;
    } 
} 

?>
