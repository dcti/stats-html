<?php
// $Id: team.php,v 1.3 2003/05/18 20:37:47 thejet Exp $

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
	 function set_ontact_name($value) { $this->_state->contactname = $value; }

        /***
         * The Email for the current team captain
         *
         * @access public
         * @type string
         ***/
	  function get_contact_email() { return $this->_state->contactemail; }
	  function set_contact_email($value) { $this->_state->contactemail = $value; }

        /***
         * The Password for the current team
         *
         * @access public
         * @type string
         ***/
         function get_password() { return $this->_state->password; }
	 function set_password($value) { $this->_state->password = $value; }
     
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
	 function set_name($value) { $this->_state->name = $value; }
	 
	/***
	 * The URL for the team
	 * 
	 * @access public
	 * @type string
	 * */
	 function get_url() { return $this->_state->URL; }
	 function set_url($value) { return $this->_state->URL = $value; }
	 
	/***
	 * The logo for the current team
	 * 
	 * @access public
	 * @type string
	 * */
	 function get_logo() { return $this->_state->logo; }
	 function set_logo($value) { $this->_state->logo = $value; }
	 
	/***
	 * The description of the current team (in UBBCode)
	 * 
	 * @access public
	 * @type string
	 * */
	 function get_description() { return $this->_state->description; }
	 function set_description($value) { $this->_state->description = $value; }
	 
	 function get_show_members() { return $this->_state->showmembers; }
	 function set_show_members($value) { $this->_state->showmembers = $value; }
	 
	 function get_show_password() { if($this->_state->showpassword == "YES") { return true; } else { return false; }}
	 function set_show_password($value) { if($value == true){$this->_state->showpassword = "YES";} else { $this->_state->showpassword = ""; }}
	 
        /***
         * Instantiates a new [empty] team object
         *
         * @access public
         * @return void
         * @param DBClass The database connectivity to use
         *        ProjectClass The current Project
         ***/
         function Team($dbPtr, $prjPtr, $team_id = -1)
	 {
	    $this->_db = $dbPtr;
	    $this->_project = $prjPtr;
		
	    if($team_id != -1)
	    {
	      $this->load($team_id);
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
         function save() { print('Team::save() ==> Not currently implemented');}
  
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
         function get_neighbors()
         {
           $this->get_current_stats();
           $sql = "SELECT t.* FROM stats_team t, team_rank r WHERE team = team_id AND overall_rank >= (".$this->_stats->get_stats_item('overall_rank')." -5)";
           $sql .= " AND overall_rank <= (".$this->_stats->get_stats_item('overall_rank')." +5)"; 
           $sql .= " AND project_id = " . $this->_project->ID;
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
         function get_current_stats()
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
         function get_ranked_list($source = 'o', $start = 1, $limit = 100, &$total)
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
                       AND tr.project_id = " . $this->_project->ID . "
                     ORDER BY day_rank ASC, work_total DESC;";
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
                       AND tr.project_id = " . $this->_project->ID . "
                     ORDER BY overall_rank ASC, work_total DESC;";
           }

           $queryData = $this->_db->query($qs);
           $total = $this->_db->num_rows($queryData);
           $result =& $this->_db->fetch_paged_result($queryData, $start, $limit);
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
         function get_search_list($sstr, $limit = 50)
         {
           // Ensure that the string is safe to pass to pgsql...
           $sstr = stripslashes($sstr);
           ini_alter("magic_quotes_sybase",0);
           $sstr = addslashes($sstr);
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
                     AND project_id = " . $this->_project->ID . "
                   ORDER BY overall_rank ASC
                   LIMIT $limit";

           // Actually run the query...
           $queryData = $this->_db->query($qs);
           $total = $this->_db->num_rows($queryData);
           $result =& $this->_db->fetch_paged_result($queryData, 1, $limit);
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
         * Explodes the object state from the database object
         *
         * @access public
         * @param object The database object to explode
         ***/
         function explode($obj, $stats = null) { $this->_state =& $obj; $this->_stats =& $stats; }
}
?>
