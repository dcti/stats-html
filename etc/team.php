<?php
// $Id: team.php,v 1.35 2010/12/18 05:35:58 jlawson Exp $

//==========================================
// file: team.php
// This file contains the classes which
// represent teams in the stats
// system.  It abstracts the concept of a
// team and its stats into objects.
//==========================================

/***
 * This class represents a team
 *
 * This class represents a team in the stats system.
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
 *
 * @access public
 ***/
class Team
{
     /*** Internal Class variables go here ***/
     // Here, our internal state is located in a DB generated class...
     var $_db;
     var $_project;
     var $_state;
     var $_authed;
     var $_IDMismatch;

     /*** END Internal Class variables ***/

     /***
      * The Id for the current team (read only)
      *
      * NOTE: We may want to make these get/set accessor methods
      *       to enforce business rules, since this type of object can
      *       actually be saved back to the database.
      *
      * @access public
      * @type int
      ***/
      function get_id() { return $this->_state->team; }
	  function get_id_mismatch() { return $this->_IDMismatch; }

     /***
	 * The Name for the current team captain
	 *
	 * @access public
	 * @type string
	 * */
	 function get_contact_name() { return $this->_state->contactname; }
	 function set_contact_name($value) {
           //print("Got contact name: $value<br>");
           $this->_state->contactname = stripslashes(trim($value));
           //print('Result: ' . $this->_state->contactname . '<br>');
         }

        /***
         * The Email for the current team captain
         *
         * @access public
         * @type string
         ***/
	  function get_contact_email() { return $this->_state->contactemail; }
	  function set_contact_email($value) { $this->_state->contactemail = stripslashes(trim($value)); }

        /***
         * The Password for the current team
         *
         * @access public
         * @type string
         ***/
         function get_password() { return $this->_state->password; }
	 function set_password($value) { $this->_state->password = stripslashes(trim($value)); }

        /***
	 * The listmode for the current team
	 *
	 * @access public
	 * @type int
	 * */
	 function get_listmode() { return $this->_state->listmode; }
	 function set_listmode($value) { $this->_state->listmode = (int)$value; }

	/***
	 * The name of the current team
	 *
	 * @access public
	 * @type string
	 * */
	 function get_name() { return $this->_state->name; }
	 function set_name($value) { $this->_state->name = stripslashes(trim($value)); }

	/***
	 * The URL for the team
	 *
	 * @access public
	 * @type string
	 * */
	 function get_url() { return $this->_state->url; }
	 function set_url($value) { return $this->_state->url = stripslashes(trim($value)); }

	/***
	 * The logo for the current team
	 *
	 * @access public
	 * @type string
	 * */
	 function get_logo() { return $this->_state->logo; }
	 function set_logo($value) { $this->_state->logo = stripslashes(trim($value)); }

	/***
	 * The description of the current team (in UBBCode)
	 *
	 * @access public
	 * @type string
	 * */
	 function get_description() { return $this->_state->description; }
	 function set_description($value) { $this->_state->description = stripslashes(trim($value)); }

	 function get_show_members() { return $this->_state->showmembers; }
	 function set_show_members($value) { $this->_state->showmembers = stripslashes(trim($value)); }

	 function get_show_password() { return $this->_state->showpassword; }
	 function set_show_password($value) { $this->_state->showpassword = stripslashes(trim($value)); }

        /***
         * Instantiates a new [empty] team object
         *
         * @access public
         * @return void
         * @param DBClass The database connectivity to use
         *        ProjectClass The current Project
         ***/
         function Team(&$dbPtr, &$prjPtr, $team_id = -1)
	 {
	    $this->_db =& $dbPtr;
	    $this->_project =& $prjPtr;
            $this->_authed = false;
            $team_id = (int) $team_id;

	    if($team_id != -1)
	    {
	      $this->load($team_id);
	    }
            else
            {
              // Initialize the object with reasonable defaults for non-required fields
              $this->_state->team = 0;
              $this->_state->listmode = 0;
              $this->_state->showmembers = "YES";
              $this->_state->showpassword = "";
              $this->_state->logo = "";
              $this->_state->url = "";
              $this->_state->description = "";
            }
	 }

        /***
         * Loads the requested team object using the current database connection
         *
         * @access public
         * @return bool
         * @param int The ID of the team to load
         ***/
         function load($team_id)
           {
             if($team_id > MAX_OLD_TEAM_ID)
               {
                 $this->_state = $this->_db->query_first("SELECT stats_team.* FROM stats_team INNER JOIN new_team_id ON stats_team.team = new_team_id.new_id WHERE new_team_id.old_id = $team_id");
               }
             else
               {
                 $this->_state = FALSE;
               }
             
             if($this->_state == FALSE)
               {
                 $this->_state = $this->_db->query_first("SELECT * FROM stats_team WHERE team = $team_id");
               }
             
             if($this->get_id() != $team_id)
               $this->_IDMismatch = true;
             else
               $this->_IDMismatch = false;

           }

        /***
         * Saves the current team to the database
         *
         * This routine saves the current team to the database, as a secondary result
         * it also refreshes the internal data for the team based on any new information
         * that may have appeared in the database since the last load.
         *
         * @access public
         * @return bool
         ***/
         function save() {
           $chkValid = $this->is_valid();
           if($chkValid != "")
           {
             return $chkValid;
           }

           // Otherwise, the object must be valid to save, so let's do it
           if($this->_state->team != 0)
           {
             // Update
             $sql = "UPDATE stats_team " .
                    "   SET name = $1," .
                    "       \"password\" = $2," .
                    "       url = $3," .
                    "       contactname = $4," .
                    "       contactemail = $5," .
                    "       logo = $6," .
                    "       showmembers = $7," .
                    "       showpassword = $8," .
                    "       listmode = $9," .
                    "       description = $10" .
                    " WHERE team = $11" .
                    " RETURNING *";
           }
           else
           {
             // Insert
             $sql = "INSERT INTO stats_team " .
                    " (name, password, url, contactname, contactemail, logo, showmembers, showpassword, listmode) " .
                    "VALUES" .
                    " ($1, $2, $3, $4, $5, $6, $7, $8, $9)" .
                    " RETURNING *";
           }

           // Execute the SQL statement
           $params = array(
                           $this->_state->name,          #1
                           $this->_state->password,      #2
                           $this->_state->url,           #3
                           $this->_state->contactname,   #4
                           $this->_state->contactemail,  #5
                           $this->_state->logo,          #6
                           $this->_state->showmembers,   #7
                           $this->_state->showpassword,  #8
                           (int)$this->_state->listmode  #9
                           );
           if($this->_state->team != 0)
           {
		// For updates, we need two additional values.
		array_push($params,
                           $this->_state->description,   #10
                           (int)$this->_state->team      #11
			   );
	   }

           $retVal = $this->_db->query_bound_first($sql, $params);

           if($retVal == FALSE)
           {
             return "Error Saving Team Data";
           }
           else
           {
             // Reset the state to that returned from the query
             $this->_state = $retVal;
           }

           return "";
         }

        /***
         * Returns a string containing the errors which prevent an object from being saved
         *
         * This routine enforces all the business rules for a given object
         * If the object's internal data conforms to the ruleset, then nothing
         * (empty string) is returned, else a string containing the rules which
         * were violated is returned.
         *
         * @access public
         * @return string
         ***/
         function is_valid()
         {
           // The Rules:
           //  password - required, max(8)
           //  name - required, unique, max(64)
           //  url - optional, max(128)
           //  contactname - required, max(64)
           //  contactemail - required, max(64)
           //  logo - optional, max(128)
           //  showmembers - optional, one of {'YES','NO','PAS'}
           //  showpassword - optional, max(16)
           //  description - optional

           // Setup the return value
           $retVal = "";

           if($this->_authed != true && $this->get_id() > 0)
           {
             $retVal = "You must be authenticated to edit team information.";
             return $retVal;
           }

           // Required Fields
           if($this->_state->password == '' ||
              $this->_state->password == null ||
              strlen($this->_state->password) != MAX_PASS_LEN)
           {
             $retVal .= "Password must be " . MAX_PASS_LEN . " characters in length\n";
           }

           if($this->_state->name == '' ||
              $this->_state->name == null ||
              strlen($this->_state->name) > 64)
           {
             $retVal .= "Team name is required and must be no longer than 64 characters.\n";
           }

           if($this->_state->contactname == '' ||
              $this->_state->contactname == null ||
              strlen($this->_state->contactname) > 64)
           {
             $retVal .= "Contact name is required and must be no longer than 64 characters.\n";
           }

           if($this->_state->contactemail == '' ||
              $this->_state->contactemail == null ||
              strlen($this->_state->contactemail) > 64)
           {
             $retVal .= "Contact email is required and must be no longer than 64 characters.\n";
           }

           // Uniqueness
           if($retVal == "")
           {
             $sql = "SELECT * FROM stats_team WHERE team != " . $this->_state->team . " AND lower(name) = '" . strtolower($this->_state->name) . "'::char(64)";
             $queryData = $this->_db->query_first($sql);

             if($queryData != FALSE)
             {
               $retVal .= "Team name must be unique\n";
             }
           }

           // Non-required fields
           if(strlen($this->_state->url) > 128)
           {
             $retVal .= "URL must be no longer than 128 characters\n";
           }

           if(strlen($this->_state->logo) > 128)
           {
             $retVal .= "Logo URL must be no longer than 128 characters\n";
           }

           if($this->_state->showmembers != "YES" &&
              $this->_state->showmembers != "NO" &&
              $this->_state->showmembers != "PAS")
           {
             $retVal .= "Show Members must be one of [\"YES\",\"NO\",\"PAS\"]\n";
           }

           if($this->_state->showmembers == "PAS" &&
              ($this->_state->showpassword == '' ||
               $this->_state->showpassword == null ||
               strlen($this->_state->showpassword) > 16))
           {
             $retVal .= "Member access password is required and must be no longer than 16 characters.\n";
           }

           return $retVal;
         }

        /***
         * This function checks that the passed password matches the team password.
         *
         * If the proper password is passed to this routine, the class becomes "authed" which enables
         * the save logic, and is part of the is valid check.
         *
         * @access public
         * @return boolean
         ***/
         function check_password($test_pass)
         {
           $pass = substr($test_pass, 0, MAX_PASS_LEN);
           if($pass == "")
             $this->_authed = false;
           else
           {
             if($pass == $this->get_password())
               $this->_authed = true;
             else
               $this->_authed = false;
           }

           return $this->_authed;
         }

        /***
         * This function returns an array of team objects representing the neighbors of the current team
         *
         * This function is a "load on demand" function, so the first call loads the
         * team's neighbors from the database, and subsequent calls access the local
         * data.
         *
         * @access public
         * @return Team[]
         ***/
         function &get_neighbors()
         {
           $this->get_current_stats();
           $sql = "SELECT r.overall_rank, r.overall_rank_previous, t.team, t.name, last_date - first_date AS days_working, work_total, work_today";
           $sql .= "    FROM stats_team t, team_rank r WHERE team = team_id AND overall_rank >= ($1 -5)";
           $sql .= "        AND overall_rank <= ($1 +5)";
           $sql .= "        AND project_id = $2";
           //$sql .= " AND team_id != $3";
           $sql .= "        AND listmode <= 9 ORDER BY overall_rank ASC ";

           $queryData = $this->_db->query_bound($sql, array(
                                                                (int)$this->_stats->get_stats_item('overall_rank'),
                                                                $this->_project->get_id()
                                                            ) );

           $result = $this->_db->fetch_paged_result($queryData);
           $cnt = count($result);
           for($i = 0; $i < $cnt; $i++)
           {
             $teamTmp = new Team($this->_db, $this->_project);
             $statsTmp = new TeamStats($this->_db, $this->_project);
             $statsTmp->explode($result[$i]);
             $teamTmp->explode($result[$i], $statsTmp);
             $retVal[] = $teamTmp;
           }

           return $retVal;
         }

        /***
         * This function returns an array of team objects representing the neighbors of the current team
         *
         * This function is a "load on demand" function, so the first call loads the
         * team's neighbors from the database, and subsequent calls access the local
         * data.
	 * NOTE: This routine returns a "full" neighbor object
	 *       as if each were the primary team
         *
         * @access public
         * @return Team[]
         ***/
         function &get_neighbors_full()
         {
           $this->get_current_stats();
           $sql = "SELECT r.overall_rank, r.overall_rank_previous, t.team, t.name, last_date - first_date AS days_working, work_total ";
	   $sql .= "	, t.showmembers, r.last_date, r.members_overall, r.members_current, r.members_today, r.work_today ";
	   $sql .= "    , r.day_rank, r.day_rank_previous ";
           $sql .= "    FROM stats_team t, team_rank r WHERE team = team_id AND overall_rank >= ($1 -5)";
           $sql .= "        AND overall_rank <= ($1 +5)";
           $sql .= "        AND project_id = $2";
           //$sql .= " AND team_id != $3";
           $sql .= "        AND listmode <= 9 ORDER BY overall_rank ASC ";

           $queryData = $this->_db->query_bound($sql, array(
                                                                (int)$this->_stats->get_stats_item('overall_rank'),
                                                                $this->_project->get_id()
                                                            ) );

           $result = $this->_db->fetch_paged_result($queryData);
           $cnt = count($result);
           for($i = 0; $i < $cnt; $i++)
           {
             $teamTmp = new Team($this->_db, $this->_project);
             $statsTmp = new TeamStats($this->_db, $this->_project);
             $statsTmp->explode($result[$i]);
             $teamTmp->explode($result[$i], $statsTmp);
             $retVal[] = $teamTmp;
           }

           return $retVal;
         }

        /***
         * Returns the current TeamStats object for this team
         *
         * This routine is "load-on-demand", meaning that the data is retrieved from the DB
         * on first access, and then from a local variable thereafter.
         *
         * @access public
         * @return TeamStats
         ***/
         var $_stats;
         function &get_current_stats()
         {
           if($this->_stats == null)
           {
             $this->_stats = new TeamStats($this->_db, $this->_project, $this->get_id());
           }
           return $this->_stats;
         }


        /***
         * This function returns a recordset representing all of the current team
         *
	 * NOTE: This routine does not return any records, only a recordset pointer
         *
         * @access public
         * @return RS<team> 
         ***/
         function get_current_stats_BOINC()
         {
           $sql = "SELECT r.overall_rank, r.overall_rank_previous, t.team, t.name, t.logo, t.description, t.contactname, last_date - first_date +1 AS days_working, work_total ";
	   $sql .= "	, t.showmembers, r.last_date, r.members_overall, r.members_current, r.members_today, r.work_today ";
	   $sql .= "    , r.day_rank, r.day_rank_previous ";
           $sql .= "    FROM stats_team t, team_rank r ";
           $sql .= "    WHERE t.team = r.team_id AND project_id = $1 ";
           $sql .= "        AND listmode <= 9 ";
	   $sql .= "    ORDER BY overall_rank ASC ";

           $queryData = $this->_db->query_bound($sql, array(
                                                                $this->_project->get_id()
                                                            ) );

           return $queryData;

         }

        /***
         * Returns the requested amount of historical stats information for this team
         *
         * This routine retrieves the requested number of previous days of stats information
         * for this team.  You specify the start date, and the number of previous days
         * to retrieve.
         *
         * @access public
         * @return TeamStats[]
         * @param date The date to start retrieval
         *        int The number of days prior to $start to retrieve data for
         ***/
         function get_stats_history($start, $getDays) { }

        /***
         * Returns a list of teams
         *
         * This routine retrieves a ranked list of teams (based on the source)
         * You specify the source (overall/yesterday) and the number to return
         *
         * @access public
         * @return Team[]
         * @param string The source (yesterday, overall, etc)
         *        int The rank to start with
         *        int The number to return (starting at rank)
         *        int [output] The total number of ranked teams
         ***/
         function &get_ranked_list($source = 'o', $start = 1, $limit = 100, &$total, &$db, &$project)
         {
           // First, we need to determine which query to run...
           if($source == 'y')
           {
             $qs = "SELECT st.*, name, to_char(first_date, 'dd-Mon-YYYY') AS first_date, to_char(last_date, 'dd-Mon-YYYY') AS last_date,
                           work_total, work_today, members_current, day_rank as rank,
                           last_date::DATE - first_date::DATE + 1 as days_working,
                           day_rank_previous - day_rank as rank_change
                      FROM stats_team st INNER JOIN team_rank tr ON st.team = tr.team_id
                     WHERE st.listmode <= 9
                       AND day_rank <= " . ($start + $limit -1) . "
                       AND day_rank >= $start
                       AND tr.project_id = " . $project->get_id() . "
                     ORDER BY day_rank ASC, work_total DESC LIMIT 100;";
           }
           else
           {
             $qs = "SELECT st.*, name, to_char(first_date, 'dd-Mon-YYYY') AS first_date, to_char(last_date, 'dd-Mon-YYYY') AS last_date,
                           work_total, work_today, members_current, overall_rank as rank,
                           last_date::DATE - first_date::DATE + 1 as days_working,
                           overall_rank_previous - overall_rank as rank_change
                      FROM stats_team st INNER JOIN team_rank tr ON st.team = tr.team_id
                     WHERE st.listmode <= 9
                       AND overall_rank <= " . ($start + $limit -1) . "
                       AND overall_rank >= $start
                       AND tr.project_id = " . $project->get_id() . "
                     ORDER BY overall_rank ASC, work_total DESC LIMIT 100;";
           }

           $queryData = $db->query($qs);
           $total = $db->num_rows($queryData);
           $result =& $db->fetch_paged_result($queryData, 0, $limit);
           $cnt = count($result);
           for($i = 0; $i < $cnt; $i++)
           {
             $teamTmp = new Team($db, $project);
             $statsTmp = new TeamStats($db, $project);
             $statsTmp->explode($result[$i]);
             $teamTmp->explode($result[$i], $statsTmp);
             $retVal[] = $teamTmp;
           }

           return $retVal;
         }

        /***
         * Returns a list of teams
         *
         * This routine retrieves a list of teams (based on the search string)
         * You specify the number to return
         *
         * @access public
         * @return Team[]
         * @param string The search string
         *        int The maximum number to return
         ***/
         function &get_search_list($sstr, $limit = 50, &$db, &$project)
         {
           $sstr = strtolower( trim( $sstr ) );

           // The query to run...
           $qs = "SELECT st.*, to_char(first_date, 'dd-Mon-YYYY') as first_date,
                         to_char(last_date, 'dd-Mon-YYYY') as last_date,
                         work_total, work_today, members_current, overall_rank,
                         last_date::DATE - first_date::DATE +1 AS days_working,
                         overall_rank_previous - overall_rank AS rank_change
                    FROM team_rank tr INNER JOIN stats_team st ON tr.team_id = st.team
                   WHERE (lower(name) like $2 OR CAST(st.team as varchar) like $2)
                     AND listmode <= 9
                     AND project_id = $1
                   ORDER BY overall_rank ASC
                   LIMIT $limit";

           // Actually run the query...
           $queryData = $db->query_bound($qs, array( (int)$project->get_id(), 
                                                     "%$sstr%") );
           $total = $db->num_rows($queryData);
           $result =& $db->fetch_paged_result($queryData, 1, $limit);
           $cnt = count($result);
           for($i = 0; $i < $cnt; $i++)
           {
             $teamTmp = new Team($db, $project);
             $statsTmp = new TeamStats($db, $project);
             $statsTmp->explode($result[$i]);
             $teamTmp->explode($result[$i], $statsTmp);
             $retVal[] = $teamTmp;
           }

           return $retVal;
         }

        /***
         * Explodes the object state from the database object
         *
         * @access public
         * @param object The database object to explode
         ***/
         function explode($obj, $stats = null) { $this->_state =& $obj; $this->_stats =& $stats; }
}
?>
