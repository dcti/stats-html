<?php
// $Id: team.php,v 1.10 2003/09/05 15:47:32 thejet Exp $

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
     
     /***
	 * The Name for the current team captain
	 * 
	 * @access public
	 * @type string
	 * */
	 function get_contact_name() { return $this->_state->contactname; }
	 function set_contact_name($value) { 
           //print("Got contact name: $value<br>");
           $this->_state->contactname = trim($value);
           //print('Result: ' . $this->_state->contactname . '<br>');
         }

        /***
         * The Email for the current team captain
         *
         * @access public
         * @type string
         ***/
	  function get_contact_email() { return $this->_state->contactemail; }
	  function set_contact_email($value) { $this->_state->contactemail = trim($value); }

        /***
         * The Password for the current team
         *
         * @access public
         * @type string
         ***/
         function get_password() { return $this->_state->password; }
	 function set_password($value) { $this->_state->password = trim($value); }
     
        /***
	 * The listmode for the current team
	 * 
	 * @access public
	 * @type int
	 * */
	 function get_listmode() { return $this->_state->listmode; }
	 function set_listmode($value) { $this->_state->listmode = $value; }
     
	/***
	 * The name of the current team
	 * 
	 * @access public
	 * @type string
	 * */
	 function get_name() { return $this->_state->name; }
	 function set_name($value) { $this->_state->name = trim($value); }
	 
	/***
	 * The URL for the team
	 * 
	 * @access public
	 * @type string
	 * */
	 function get_url() { return $this->_state->url; }
	 function set_url($value) { return $this->_state->url = trim($value); }
	 
	/***
	 * The logo for the current team
	 * 
	 * @access public
	 * @type string
	 * */
	 function get_logo() { return $this->_state->logo; }
	 function set_logo($value) { $this->_state->logo = trim($value); }
	 
	/***
	 * The description of the current team (in UBBCode)
	 * 
	 * @access public
	 * @type string
	 * */
	 function get_description() { return $this->_state->description; }
	 function set_description($value) { $this->_state->description = trim($value); }
	 
	 function get_show_members() { return $this->_state->showmembers; }
	 function set_show_members($value) { $this->_state->showmembers = $value; }
	 
	 function get_show_password() { return $this->_state->showpassword; }
	 function set_show_password($value) { $this->_state->showpassword = $value; }
	 
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
         * @param int The ID of the participant to load
         ***/
         function load($team_id)
	 {
	    $this->_state = $this->_db->query_first("SELECT * FROM stats_team WHERE team = $team_id");
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
                    "   SET name = '" . $this->_state->name . "'," .
                    "       \"password\" = '" . $this->_state->password . "'," .
                    "       url = '" . $this->_state->url . "'," .
                    "       contactname = '" . $this->_state->contactname . "'," .
                    "       contactemail = '" . $this->_state->contactemail . "'," .
                    "       logo = '" . $this->_state->logo . "'," .
                    "       showmembers = '" . $this->_state->showmembers . "'," .
                    "       showpassword = '" . $this->_state->showpassword . "'," .
                    "       listmode = " . $this->_state->listmode . "," .
                    "       description = '" . $this->_state->description . "'" .
                    " WHERE team = " . $this->_state->team . "; SELECT * FROM stats_team WHERE team = " . $this->_state->team . ";";
           }
           else
           {
             // Insert
             $sql = "INSERT INTO stats_team " .
                    " (name, password, url, contactname, contactemail, logo, showmembers, showpassword, listmode) " .
                    "VALUES" .
                    " ('" . $this->_state->name . "'," .
                    "'" . $this->_state->password . "'," .
                    "'" . $this->_state->url . "'," .
                    "'" . $this->_state->contactname . "'," .
                    "'" . $this->_state->contactemail . "'," .
                    "'" . $this->_state->logo . "'," .
                    "'" . $this->_state->showmembers . "'," .
                    "'" . $this->_state->showpassword . "'," .
                    "" . $this->_state->listmode . "); SELECT * FROM stats_team WHERE team = currval('public.stats_team_team_seq'::text);";
           }

           // Execute the SQL statement
           // ** throw away the result of the first operation (if it's false)
           $retVal = $this->_db->query_first($sql);
           
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

           // Required Fields
           if($this->_state->password == '' || 
              $this->_state->password == null ||
              strlen($this->_state->password) != 8)
           {
             $retVal .= "Password must be 8 characters in length\n";
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
             // make sure we've got proper escaping here...
             $tmp = stripslashes($this->_state->name);
             ini_alter("magic_quotes_sybase",0);
             $tmp = addslashes($tmp);

             $sql = "SELECT * FROM stats_team WHERE team != " . $this->_state->team . " AND lower(name) = '" . strtolower($tmp) . "'::char(64)";
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
           $sql = "SELECT t.* FROM stats_team t, team_rank r WHERE team = team_id AND overall_rank >= (".$this->_stats->get_stats_item('overall_rank')." -5)";
           $sql .= " AND overall_rank <= (".$this->_stats->get_stats_item('overall_rank')." +5)"; 
           $sql .= " AND project_id = " . $this->_project->get_id();
           //$sql .= " AND team_id != " . $this->get_id();
           $sql .= " AND listmode <= 9 ORDER BY overall_rank ASC ";

           $queryData = $this->_db->query($sql);

           $result = $this->_db->fetch_paged_result($queryData);
           $cnt = count($result);
           for($i = 0; $i < $cnt; $i++)
           {
             $teamTmp = new Team($this->_db, $this->_project);
             $teamTmp->explode($result[$i]);
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
           $sstr = strtolower($sstr);

           // The query to run...
           $qs = "SELECT st.*, to_char(first_date, 'dd-Mon-YYYY') as first_date,
                         to_char(last_date, 'dd-Mon-YYYY') as last_date,
                         work_total, work_today, members_current, overall_rank,
                         last_date::DATE - first_date::DATE +1 AS days_working,
                         overall_rank_previous - overall_rank AS rank_change
                    FROM team_rank tr INNER JOIN stats_team st ON tr.team_id = st.team
                   WHERE (lower(name) like '%$sstr%' OR CAST(st.team as varchar) like '%$sstr%')
                     AND listmode <= 9
                     AND project_id = " . $project->get_id() . "
                   ORDER BY overall_rank ASC
                   LIMIT $limit";

           // Actually run the query...
           $queryData = $db->query($qs);
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
