<?php 
// $Id: participant.php,v 1.26 2003/09/17 21:39:40 paul Exp $

define('MAX_PASS_LEN',9);

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
	var $_authed = false;
	
    /**
     * ** End Internal class variables **
     */

	function get_team_id() 
	{
		// @todo return team user is with 
		return 0 ;	
	}

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

	function check_password($pass)
    {
    	$pass = substr($pass,0,MAX_PASS_LEN);
    	
		if ($this->get_password() == "") {
    		//auth fail - no pass set -> mail pass to user
  		}

    	// @ TODO - NEED TO CHECK users is not suspended, retired etc
        if ( $this -> get_password() == $pass ) {
       		if ($this->get_retire_to() > 0) {
    			//authfail("pretired",$test_from,$test_id,$test_pass);
  				return false;
  			}
 			if ($this->get_list_mode() > 7) {
    			//authfail("plocked",$test_from,$test_id,$test_pass);
  				return false;
  			}
        	$this ->_authed = true; 
        	return true;
        } else {
        	// authfail("pbadpass",$test_from,$test_id,$test_pass);
        	return false;
        }
    } 

    function is_authed()
    {
		return $this->_authed;
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
     * NOTE: This routine is "load-on-demand" so the friend data is not loaded until
     *       it is requested by the user interface.
     * 
     * @access public 
     * @type int[]
     */
    function get_friends($index = -1)
    {
        if($this->_friends == null)
          $this->_friends =& $this->load_friend_data();

        /* @todo friends */
        if($index == -1)
          return $this->_friends;
        else
          return $this -> _friends[$index];
    } 
    function set_friends($index, $value)
    {
        $this -> _friends[$index] = $value;
    } 
    function &load_friend_data()
    {
        $qs = "SELECT p.*, r.*, r.last_date - r.first_date + 1 AS days_working,
                        r.overall_rank_previous - r.overall_rank as overall_change,
                        r.day_rank_previous - r.day_rank as day_change
                    FROM stats_participant_friend pf, email_rank r, stats_participant p 
                WHERE pf.id = " . $this->get_id() . " 
                  AND p.listmode < 10 AND r.project_id = " . $this->_project->get_id() . "

                  AND pf.friend = r.id
                  AND pf.friend = p.id
                ORDER BY r.overall_rank ASC, r.work_total ASC";

        $queryData = $this->_db->query($qs);
        $total = $this->_db->num_rows($queryData);
        $result =& $this->_db->fetch_paged_result($queryData);
        $cnt = count($result);
        for($i = 0; $i < $cnt; $i++) {
            $partTmp =& new Participant($this->_db, $this->_project);
            $statsTmp =& new ParticipantStats($this->_db, $this->_project);
            $statsTmp->explode($result[$i]);
            $partTmp->explode($result[$i], $statsTmp);
            $retVal[] = $partTmp;
            unset($partTmp);
            unset($statsTmp);
        }

        return $retVal;
    }

    /**
     * The neighbors of this participant
     * NOTE: This routine is "load-on-demand" so the neighbor data is not loaded until
     *       it is requested by the user interface.
     * 
     * @access public 
     * @type int[]
     */
    var $_neighbors;
    function get_neighbors($index = -1)
    {
        if($this->_neighbors == null)
          $this->_neighbors =& $this->load_neighbor_data();

        if($index == -1)
          return $this->_neighbors;
        else
          return $this -> _neighbors[$index];
    }
    function &load_neighbor_data()
    {
        $mystats = $this->get_current_stats();
        $baserank = $mystats->get_stats_item("overall_rank");
        $qs = "SELECT p.*, r.*, r.last_date - r.first_date + 1 AS days_working,
                      r.overall_rank_previous - r.overall_rank as overall_change,
                      r.day_rank_previous - r.day_rank as day_change
                 FROM email_rank r, stats_participant p
                WHERE r.overall_rank >= ($baserank -3) 
                  AND r.overall_rank <= ($baserank +3)
                  AND p.listmode < 10 AND r.project_id = " . $this->_project->get_id() . "

                  AND r.id = p.id 
                ORDER BY r.overall_rank ASC, r.work_total ASC";

        $queryData = $this->_db->query($qs);
        $total = $this->_db->num_rows($queryData);
        $result =& $this->_db->fetch_paged_result($queryData);
        $cnt = count($result);
        for($i = 0; $i < $cnt; $i++) {
            $partTmp =& new Participant($this->_db, $this->_project);
            $statsTmp =& new ParticipantStats($this->_db, $this->_project);
            $statsTmp->explode($result[$i]);
            $partTmp->explode($result[$i], $statsTmp);
            $retVal[] = $partTmp;
            unset($partTmp);
            unset($statsTmp);
        }

        return $retVal;
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
            if (trim($this -> get_contact_name()) == "")
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
    function Participant($dbPtr, $prjPtr, $id = -1 )
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
        $qs = "SELECT * FROM stats_participant WHERE id = $id AND listmode < 10";
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

    function &get_ranked_list($source = 'o', $start = 1, $limit = 100, &$total, &$db, &$project)
    { 
        // First, we need to determine which query to run...
        if ($source == 'y') {
            $rank_field = 'day_rank';
            $work_field = 'work_today';
        } else {
            $rank_field = 'overall_rank';
            $work_field = 'work_total';
        } 
        $qs = "SELECT r.id, to_char(r.first_date, 'dd-Mon-YYYY') as first_date, to_char(r.last_date, 'dd-Mon-YYYY') as last_date,
                        r.$work_field as blocks, last_date - first_date + 1 AS days_working,
                        r.$rank_field as rank, r." . $rank_field . "_previous - r.$rank_field as change,
                        p.email, p.listmode, p.contact_name
                FROM email_rank r, stats_participant p
                WHERE r.$rank_field BETWEEN $start AND ($start + $limit)
                    AND r.project_id = " . $project->get_id() . "
                    AND p.listmode < 10
                    AND r.id = p.id
                ORDER BY r.$rank_field, r.$work_field DESC LIMIT 100";
        $queryData = $db->query($qs);
        $total = $db->num_rows($queryData);
        $result =& $db->fetch_paged_result($queryData, 0, $limit);
        $cnt = count($result);
        for($i = 0; $i < $cnt; $i++) {
            $partTmp =& new Participant($db, $project, null);
            $statsTmp =& new ParticipantStats($db, $project);
            $statsTmp->explode($result[$i]);
            $partTmp->explode($result[$i], $statsTmp);
            $retVal[] = $partTmp;
            unset($partTmp);
            unset($statsTmp);
        } 
        return $retVal;
    } 

        /***
         * Returns a list of participants
         *
         * This routine retrieves a list of participants (based on the search string)
         * You specify the number to return
         *
         * @access public
         * @return Participant[]
         * @param string The search string
         *        int The maximum number to return
         ***/
         function &get_search_list($sstr, $limit = 50, &$db, &$project)
         {
           $sstr = strtolower($sstr);

           // The query to run...
           $qs = "SELECT p.*,r.*, to_char(first_date, 'dd-Mon-YYYY') as first_date,
                         to_char(last_date, 'dd-Mon-YYYY') as last_date,
                         work_total, work_today,
                         last_date - first_date +1 AS days_working,
                         overall_rank_previous - overall_rank AS rank_change
                    FROM email_rank r,stats_participant p 
                   WHERE p.id = r.id
                     AND lower(email) like '%$sstr%'
                     AND listmode <= 10
                     AND project_id = " . $project->get_id() . "
                   ORDER BY overall_rank ASC
                   LIMIT $limit";
                   //WHERE stats_participant_display_name_l(listmode,p.id,email,contact_name) like $sstr

           // Actually run the query...
           $queryData = $db->query($qs);
           $total = $db->num_rows($queryData);
           $result =& $db->fetch_paged_result($queryData, 1, $limit);
           $cnt = count($result); 
           for($i = 0; $i < $cnt; $i++)
           {
             $partTmp =& new Participant($db, $project);
             $statsTmp =& new ParticipantStats($db, $project);
             $statsTmp->explode($result[$i]);
             $partTmp->explode($result[$i], $statsTmp);
             $retVal[] = $partTmp;
             unset($partTmp);
             unset($statsTmp);
           }

           return $retVal;
         }

        /***
         * Returns a list of participants for a team
         *
         * This routine retrieves a ranked list of participants for a particular
 team id (based on the source)
         * You specify the source (overall/yesterday) and the number to return
         *
         * @access public
         * @return Participant[]
         * @param string The source (yesterday, overall, etc)
         *        int The rank to start with
         *        int The number to return (starting at rank)
         *        int [output] The total number of ranked participants
         ***/
         function &get_team_list($teamid, $source = 'o', $start = 1, $limit = 100, &$total, &$db, &$project)
         {
           // First, we need to determine which query to run...
           if($source == 'y') {
            $rank_field = 'day_rank';
            $field = 'work_today';
            $other_field = 'work_total';
           } else {
            $rank_field = 'overall_rank';
            $field = 'work_total';
            $other_field = 'work_today';
          }
           $qs = "SELECT p.*, tm.work_total, tm.work_today, to_char(tm.first_date, 'dd-Mon-YYYY') AS first_date,
                         to_char(tm.last_date, 'dd-Mon-YYYY') AS last_date,
                         er.$rank_field as rank, (er.${rank_field}_previous - er.$rank_field) as rank_change
                    FROM team_members tm, stats_participant p, email_rank er 
                   WHERE tm.project_id = " . $project->get_id() . "
                     AND tm.team_id = " . $teamid . "
                     AND tm.$field > 0

                     AND p.id = tm.id
                     AND tm.id = er.id
                     AND tm.project_id = er.project_id
                   ORDER BY tm.$field DESC, tm.$other_field DESC;";

           $queryData = $db->query($qs);
           $total = $db->num_rows($queryData);
           $result =& $db->fetch_paged_result($queryData, $start, $limit);
           $cnt = count($result);
           for($i = 0; $i < $cnt; $i++)
           {
             $parTmp =& new Participant($db, $project, null);
             $statsTmp =& new ParticipantStats($db, $project);
             $statsTmp->explode($result[$i]);
             $parTmp->explode($result[$i], $statsTmp);
             $retVal[] = $parTmp;
             unset($parTmp);
             unset($statsTmp);
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
        $this->_state =& $obj;
        $this->_stats =& $stats;
    } 
} 

// vi: expandtab sw=2 ts=2 tw=128
?>
