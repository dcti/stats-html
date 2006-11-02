<?php
// $Id: participant.php,v 1.56 2006/11/02 15:42:41 thejet Exp $
// vi: expandtab sw=4 ts=4 tw=128

include_once "participantstats.php";

/**
 * This class represents a participant
 *
 * This class represents a participant in the stats system.
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
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
    var $_neighbors;
    var $_stats;

    /**
     * ** End Internal class variables **
     */

    function get_team_id()
    {
        $qs  = 'SELECT team_id, last_date FROM team_joins' .
          ' WHERE id = $1 ORDER BY join_date DESC LIMIT 1;';
        $res = $this->_db->query_bound_first($qs, array((int)$this->_state->id) );
        if($res == FALSE)
            return 0;
        else if(is_null($res->last_date))
            return $res->team_id;
        else
            return 0;
    }

    function join_team($team_id)
    {
        $team_id = (int) $team_id;
        // Only call team join if they are not already on this team
        if($team_id == $this->get_team_id()) return true;

        if(!$this->_authed)
            return false;

        if($team_id < 0)
            return false;

        $qs = 'SELECT p_teamjoin($1, $2);';
        $res = $this->_db->query_bound($qs, array( (int)$this->get_id(), (int)$team_id ) );
        if($res == FALSE)
            return false;
        else
            return true;
    }

    /* Return current participant id
     *
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
        $this -> _state -> email = stripslashes($value);
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
        $this -> _state -> password = stripslashes($value);
    }

    function check_password($pass)
    {
        $pass = substr($pass,0,MAX_PASS_LEN);

        if ($this->get_password() == "") {
            //auth fail - no pass set -> mail pass to user
            trigger_error("No Password Set for account");
            return false;
        }

        if ( $this -> get_password() == $pass ) {
            if ($this->get_retire_to() > 0) {
                return false;
            }
            if ($this->get_list_mode() > 7) {
                return false;
            }
            $this ->_authed = true;
            return true;
        } else {
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
        $this -> _state -> listmode = $this->_db->prepare_int($value);
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
        $this -> _state -> nonprofit = $this->_db->prepare_int($value);
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

    function retire($value)
    {
	if ($this->_authed != true)
            return false;
	if (!is_numeric($value))
	    return false;
	if ($value <= 0)
	    return false;
        if ( $this ->_state->id > 0 ) {
            $qs = 'select p_retire($1, $2)';
	    $res = $this->_db->query_bound($qs, array( (int)$this->_state->id, (int)$value ));
            if($res == FALSE) {
		return false;
            } else {
                return true;
            }
        } else {
            return false;
        }
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

        if($index == -1)
            return $this->_friends;
        else if($index >= 0 && $index < count($this->_friends))
            return $this->_friends[$index];
        else
            return null;
    }

    function set_friends($value)
    {
      // This routine gets friend information in a comma-separated list
      //NOTE: At this time, setting this value replaces the array of friend
      //      objects with an array of friend ids
      $this->_friends = explode(",", $value);
    }

    function save_friends()
    {
        // If friends is not yet set, then don't try to save it
        if($this->_friends == null)
            return "";

        // If the first item in the friends array is an object, don't save
        // since this means that set_friends was never called, so no change could be made
        if(is_object($this->_friends[0]))
            return "";

        // This routine saves current friend data to the database
        $qs = 'DELETE FROM stats_participant_friend WHERE id = $1;';
        $res = $this->_db->query_bound($qs, array( (int)$this->_state->id ));
        if($res == FALSE)
            return false;

        foreach($this->_friends as $friend)
        {
            $friend = (int) $friend;
            if($friend > 0)
            {
                $qs  = 'INSERT INTO stats_participant_friend (id, friend) VALUES ($1, $2)';
                $res = $this->_db->query_bound($qs, array( (int)$this->_state->id, (int)$friend ));
                if($res == FALSE)
                    return false;
           }
        }
        return true;
    }

    function &load_friend_data()
    {
        $qs  = 'SELECT p.*, r.*, r.last_date - r.first_date + 1 AS days_working,';
        $qs .= '                r.overall_rank_previous - r.overall_rank as overall_change,';
        $qs .= '                r.day_rank_previous - r.day_rank as day_change';
        $qs .= '            FROM stats_participant_friend pf, email_rank r, stats_participant p';
        $qs .= '        WHERE pf.id = $1' ;
        $qs .= '          AND p.listmode < 10 AND r.project_id = $2';
        $qs .= '          AND pf.friend = r.id';
        $qs .= '          AND pf.friend = p.id';
        $qs .= '        ORDER BY r.overall_rank ASC, r.work_total ASC';

        $queryData = $this->_db->query_bound($qs, array( (int)$this->get_id(),
                                                         (int)$this->_project->get_id() ));
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
        $qs  = 'SELECT *, last_date - first_date + 1 AS days_working,';
        $qs .= '              overall_rank_previous - overall_rank as overall_change,';
        $qs .= '              day_rank_previous - day_rank as day_change';
        $qs .= '         FROM (';
        $qs .= '                SELECT *';
        $qs .= '                    FROM (SELECT *';
        $qs .= '                                FROM email_rank r, stats_participant p';
        $qs .= '                                WHERE r.overall_rank >= ($1 -3)';
        $qs .= '                                    AND r.overall_rank <= ($1 +3)';
        $qs .= '                                    AND p.listmode < 10 AND r.project_id = $2';
        $qs .= '                                    AND r.id = p.id';
        $qs .= '                                LIMIT 7';
        $qs .= '                            ) a';
        $qs .= '                UNION';
        $qs .= '                SELECT *';
        $qs .= '                    FROM email_rank r, stats_participant p';
        $qs .= '                    WHERE p.id = $3';
        $qs .= '                        AND p.listmode < 10 AND r.project_id = $2';
        $qs .= '                        AND r.id = p.id';
        $qs .= '              ) a';
        $qs .= '        ORDER BY overall_rank ASC, work_total ASC';

        $queryData = $this->_db->query_bound($qs, array( (int)$baserank, 
                                                         (int)$this->_project->get_id(),
                                                         (int)$this->_state->id
                                                     ));
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
        $this -> _state -> dem_yob = $this->_db->prepare_int($value);
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
        $this -> _state -> dem_heard = $this->_db->prepare_int($value);
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
        $this -> _state -> dem_gender = stripslashes($value);
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
        $this -> _state -> dem_motivation = $this->_db->prepare_int($value);
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
        $this -> _state -> dem_country = stripslashes($value);
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
        $this -> _state -> contact_name = stripslashes($value);
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
        $this -> _state -> contact_phone = stripslashes($value);
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
        $this -> _state -> motto = stripslashes($value);
    }

    /**
     * Date that this account was retired
     *
     * @access public (readonly)
     * @type datetime
     */
    var $_retireDate; //@TODO
    function getRetireDate()
    {
        return $this > _retireDate;
    }


    /**
     * Returns the string that represents the name of this
     * participant, according to the configured public display privary
     * preferences.  (Be sure to use safe_display() before outputting
     * this value in an HTML page.)
     *
     * @access public
     * @type string
     */
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
    function Participant($dbPtr, $prjPtr, $id = -1, $include_banned = false )
    {
        $this -> _db = $dbPtr;
        $this -> _project = $prjPtr;

        if(is_numeric($id))
        {
            $id = (int)$id;
            if($id > 0)
            {
                $this -> load($id, $include_banned);
            } else {
                // Load default values
            }
        }
        else
        {
            // Try to load by email
            $this->load_by_email(stripslashes($id), $include_banned);
        }
    }


    /**
     * Loads the requested participant object using the current database connection
     *
     * @access public
     * @return bool
     * @param int $ The ID of the participant to load
     * @param int $include_banned Include participants with listmode >= 10
     */
    function load($id, $include_banned = false)
    {
        if ($include_banned)
            $qs = 'SELECT * FROM stats_participant WHERE id = $1';
        else
            $qs = 'SELECT * FROM stats_participant WHERE id = $1 AND listmode < 10';
        $this -> _state = $this -> _db -> query_bound_first ($qs, array( $id ));
    }

    /**
     * Loads the requested participant object (by email address) using the current database connection
     *
     * @access public
     * @return bool
     * @param string $ The email of the participant to load
     */
    function load_by_email($email, $include_banned = false)
    {
        if ($include_banned)
            $qs = 'SELECT * FROM stats_participant WHERE lower(email) = lower($1)';
        else
            $qs = 'SELECT * FROM stats_participant WHERE lower(email) = lower($1) AND listmode < 10';
        $this -> _state = $this -> _db -> query_bound_first ($qs, array( $email ));
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
        if(!$this->_authed) {
            trigger_error('You must be authorized to save participant data.');
            return false;
        }

        $chkResult = $this->is_valid();
        if($chkResult != '')
            return $chkResult;

        $qs = 'BEGIN;';
        $res = $this->_db->query($qs);
        if($res == FALSE)
            return "Error starting transaction for save\n";

        $qs  = 'UPDATE stats_participant ' .
          '          SET password = $1, ' .
          '              listmode = $2, ' .
          '              dem_yob = $3, ' .
          '              dem_heard = $4, ' .
          '              dem_motivation = $5, '.
          '              dem_gender = $6, ' .
          '              dem_country = $7, ' .
          '              contact_name = $8, ' .
          '              contact_phone = $9, ' .
          '              motto = $10, ' .
          '              nonprofit = $11 ' .
          '              WHERE id = $12;';

        $params = array( $this->_state->password,
                         (int)$this->_state->listmode,
                         (int)$this->_state->dem_yob,
                         (int)$this->_state->dem_heard,
                         (int)$this->_state->dem_motivation,
                         (trim($this->_state->dem_gender) == "" ? NULL : $this->_state->dem_gender),
                         (trim($this->_state->dem_country) == "" ? NULL : $this->_state->dem_country),
                         $this->_state->contact_name,
                         $this->_state->contact_phone,
                         $this->_state->motto,
                         $this->_state->nonprofit,
                         (int)$this->_state->id
                         );
        $res = $this->_db->query_bound($qs, $params);
        if($res == FALSE)
            $chkResult = "Error Updating Participant Record.$qs\n";

        $qs = 'SELECT * FROM stats_participant WHERE id = $1;';
        $res = $this->_db->query_bound_first($qs, array( (int)$this->_state->id ));
        if($res == FALSE)
            $chkResult = "Error retrieving updated participant record.\n";
        else
        $this->_state = $res;

        // If the _friends var is not an array, we can't save it
        if(is_array($this->_friends))
        {
            if(!$this->save_friends())
                $chkResult = "Error Saving Friend Information.\n";
            else
                $this->_friends = null; // reset the friends array, current is invalid
        }

        if($chkResult != "")
        {
            $res = $this->_db->query("ROLLBACK;");
            if($res == FALSE)
                $chkResult .= "Error rolling back transaction.\n";
        } else {
            $res = $this->_db->query("COMMIT;");
            if($res == FALSE)
                $chkResult = "Error committing transaction.\n";
        }

        return $chkResult;
    }

    function is_valid()
    {
        // @todo - Expand this to verify the validity of all fields
        $strResult = "";
        if($this->_state->password == "")
            $strResult .= "You must specify a password.\n";
        if(strlen($this->_state->password) != MAX_PASS_LEN)
            $strResult .= "Your password must be exactly 8 characters.\n";
        if($this->_state->listmode == 2 && strlen(trim($this->_state->contact_name)) <= 0)
            $strResult .= "Contact Name must be filled to use Real Name list mode.\n";
        if(strlen($this->_state->contact_name) > 50)
            $strResult .= "Contact Name must be no more than 50 characters.\n";
        if($this->_state->dem_gender != "M" &&
           $this->_state->dem_gender != "F" &&
           trim($this->_state->dem_gender) != "" &&
           !is_null($this->_state->dem_gender))
            $strResult .= "Invalid gender specification(".$this->_state->dem_gender.").\n";
        if(strlen($this->_state->dem_country) != 2 && $this->_state->dem_country != "")
            $strResult .= "Invalid country selected.\n";
        if(strlen($this->_state->contact_phone) > 20)
            $strResult .= "Contact Phone must be no more than 20 characters.\n";
        if(strlen($this->_state->motto) > 255)
            $strResult .= "Motto must be no more than 255 characters.\n";

        return $strResult;
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
     * Returns the current ParticipantStats object for this participant
     *
     * This routine is "load-on-demand", meaning that the data is retrieved from the DB
     * on first access, and then from a local variable thereafter.
     *
     * @access public
     * @return ParticipantStats
     */
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

        // Careful, the following query has some column names that
        // variable substitions and cannot be passed via query_bound.
        $qs  = "SELECT r.id, to_char(r.first_date, 'dd-Mon-YYYY') as first_date, to_char(r.last_date, 'dd-Mon-YYYY') as last_date,";
        $qs .= "                r.$work_field as blocks, last_date - first_date + 1 AS days_working,";
        $qs .= "                r.$rank_field as rank, r." . $rank_field . "_previous - r.$rank_field as change,";
        $qs .= "                p.email, p.listmode, p.contact_name";
        $qs .= "        FROM email_rank r, stats_participant p";
        $qs .= "        WHERE r.$rank_field BETWEEN $1 AND $2";
        $qs .= "            AND r.project_id = $3";
        $qs .= "            AND p.listmode < 10";
        $qs .= "            AND r.id = p.id";
        $qs .= "        ORDER BY r.$rank_field, r.$work_field DESC LIMIT 100";
        $queryData = $db->query_bound($qs, array( (int)$start,
                                                  (int)($start + $limit),
                                                  (int)$project->get_id()
                                                  ));
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
     */
    function &get_search_list($sstr, $limit = 50, &$db, &$project)
    {
        $sstr = strtolower( trim( $sstr ) );

        // The query to run...
        $qs  = "SELECT p.*,r.*, to_char(first_date, 'dd-Mon-YYYY') as first_date,";
        $qs .= "                 to_char(last_date, 'dd-Mon-YYYY') as last_date,";
        $qs .= '                 work_total, work_today,';
        $qs .= '                 last_date - first_date +1 AS days_working,';
        $qs .= '                 overall_rank_previous - overall_rank AS rank_change';
        $qs .= '        FROM email_rank r,stats_participant p';
        $qs .= '        WHERE p.id = r.id';
        $qs .= '            AND lower(email) like $1';
        $qs .= '            AND listmode <= 10';
        $qs .= '            AND project_id = $2';
        $qs .= '        ORDER BY overall_rank ASC';
        $qs .= '         LIMIT $3';

        // Actually run the query...
        $queryData = $db->query_bound($qs, array( "%$sstr%", (int)$project->get_id(), (int)$limit ));
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
     * Returns a list of participants' id and email
     *
     * This routine retrieves a list of participants' id and email-string
     * You specify the number to return
     *
     * @access public
     * @return (id,email)[]
     * @param string The search string
     *        int The maximum number to return
     */
    function &get_search_list_no_stats($sstr, $limit = 50, &$db)
    {
        $sstr = strtolower($sstr);

        // The query to run...
        $qs = 'SELECT id, lower(email) AS email
                FROM stats_participant
                WHERE lower(email) like $1
                    AND listmode <= 10
                LIMIT $2';

        // Actually run the query...
        $queryData = $db->query_bound($qs, array( "%$sstr%", (int)$limit ));
        $total = $db->num_rows($queryData);
        for($i = 0; $i < $total; $i++)
        {
            $retVal[] = $db->fetch_object($queryData);
        }

        return $retVal;
    }

    /***
     * Returns a list of participants' id and email
     *
     * This routine retrieves a list of participants' id and email-string
     * You specify the number to return
     * 
     * THIS FUNCTION IGNORES LISTMODE!
     *
     * @access public
     * @return (id,email)[]
     * @param string The search string
     *        int The maximum number to return
     */
    function &get_search_list_no_stats_all($sstr, $limit = 50, &$db)
    {
        $sstr = strtolower($sstr);

        // The query to run...
        $qs = 'SELECT id, lower(email) AS email
                FROM stats_participant
                WHERE lower(email) like $1
                LIMIT $2';

        // Actually run the query...
        $queryData = $db->query_bound($qs, array( "%$sstr%", (int)$limit ));
        $total = $db->num_rows($queryData);
        for($i = 0; $i < $total; $i++)
        {
            $retVal[] = $db->fetch_object($queryData);
        }

        return $retVal;
    }

    /***
     * Returns a list of participants for a team
     *
     * This routine retrieves a ranked list of participants for a particular team id (based on the source)
     * You specify the source (overall/yesterday) and the number to return
     *
     * @access public
     * @return Participant[]
     * @param string The source (yesterday, overall, etc)
     *        int The rank to start with
     *        int The number to return (starting at rank)
     *        int [output] The total number of ranked participants
     */
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

        // Careful, the following query has some column names that
        // variable substitions and cannot be passed via query_bound.
        $qs  = "SELECT p.*, tm.work_total, tm.work_today, to_char(tm.first_date, 'dd-Mon-YYYY') AS first_date,";
        $qs .= "                 to_char(tm.last_date, 'dd-Mon-YYYY') AS last_date,";
        $qs .= "                 er.$rank_field as rank, (er.${rank_field}_previous - er.$rank_field) as rank_change";
        $qs .= "        FROM team_members tm, stats_participant p, email_rank er";
        $qs .= "        WHERE tm.project_id = $1 ";
        $qs .= "            AND tm.team_id = $2 ";
        $qs .= "            AND tm.$field > 0";
        $qs .= "            AND p.id = tm.id";
        $qs .= "            AND tm.id = er.id";
        $qs .= "            AND tm.project_id = er.project_id";
        $qs .= "        ORDER BY tm.$field DESC, tm.$other_field DESC;";

        $queryData = $db->query_bound($qs, array( (int)$project->get_id(), (int)$teamid ));
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

?>
