<?php
// $Id: participant.php,v 1.3 2003/05/25 20:20:01 paul Exp $

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
     * The Id for the current participant (read only)
     * 
     * @access public (readonly)
     * @type int
     */
    var $_ID;
	var $_state;
	
    function get_id()
    {
        return $this -> _ID;
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
     * Which team does this participant belong to? (ID)
     * 
     * @access public 
     * @type int
     */
/*    function getTeamID()
    {
        return $this -> _teamID;
    } 
    function setTeamID($value)
    {
        $this -> _teamID = $value;
    } 
*/
    /**
     * Has this participant retired their record to another participant?
     * 
     * @access public 
     * @type int
     */
    function get_retire_to()
    {
        return $this -> _state ->retire_to;
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
    var $_friends;
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
    function setYearOfBirth($value)
    {
        $this -> _state ->dem_yob = $value;
    } 

    /**
     * Demographic: How participant learned about distributed.net
     * 
     * @access public 
     * @type smallint
     */
    function getHeard()
    {
        return $this -> _state -> dem_Heard;
    } 
    function setHeard($value)
    {
        $this -> _state -> dem_heard = $value;
    } 

    /**
     * Demographic: Gender of participant
     * 
     * @access public 
     * @type string
     */
    var $_demoGender;
    function getGender()
    {
        return $this -> _demoGender;
    } 
    function setGender($value)
    {
        $this -> _demoGender = $value;
    } 

    /**
     * Demographic: Motivation for running distributed.net client
     * 
     * @access public 
     * @type smallint
     */
    var $_demoMotivation;
    function getMotivation()
    {
        return $this -> _demoMotivation;
    } 
    function setMotivation($value)
    {
        $this -> _demoMotivation = int($value);
    } 

    /**
     * Demographic: Country of origin
     * 
     * @access public 
     * @type string
     */
    var $_demoCountry;
    function getCountry()
    {
        return $this -> _demoCountry;
    } 
    function setCountry($value)
    {
        $this -> _demoCountry = $value;
    } 

    /**
     * Contact name for the participant
     * 
     * @access public 
     * @type string
     */
    var $_contactName;
    function getContactName()
    {
        return $this -> _contactName;
    } 
    function setContactName($value)
    {
        $this -> _contactName = $value;
    } 

    /**
     * Contact phone for the participant
     * 
     * @access public 
     * @type string
     */
    var $_contactPhone;
    function getContactPhone()
    {
        return $this -> _contactPhone;
    } 
    function setContactPhone($value)
    {
        $this -> _contactPhone = $value;
    } 

    /**
     * Participant motto
     * 
     * @access public 
     * @type string
     */
    var $_motto;
    function getMotto()
    {
        return $this -> _motto;
    } 
    function setMotto($value)
    {
        $this -> _motto = $value;
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

    /**
     * ** Internal Class Variables **
     */
    var $_db;
    var $_project;
    /**
     * ** End Internal class variables **
     */

    function getDisplayName()
    {
        if ($this -> _state -> listmode == 0 || $this -> _state -> listmode == 8 || $this -> _state -> listmode == 9) 
            $listas = $this -> get_email();
         else if ($this -> _state -> listmode == 1) 
            $listas = "Participant #" . number_style_convert($this -> get_id());
         else if ($this -> _state -> listmode == 2) 
            if ($this ->getContactName() == "")
                $listas = "Participant #" . number_style_convert($this -> get_id());
            else
                $listas = $this -> getContactName();
            else
                $listas = "Record error for #" . number_style_convert($this -> get_id()) . "!";
        return $listas;
    } 
    /**
     * Instantiates a new participant object, and loads it with the specified participant's information.
     * 
     * @access public 
     * @return void 
     * @param DBClass $ The database connectivity to use
     *                      ProjectClass The current project
     *                      int The ID of the participant to load
     */
    function Participant($dbPtr, $prjPtr, $id)
    {
        $this -> _db = $dbPtr;
        $this -> _project = $prjPtr;
		$this -> _ID = $id;
        $this -> load($id);
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
    function getCurrentStats()
    {
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
     *                      int The number of days prior to $start to retrieve data for
     */
    function getStatsHistory($start, $getDays)
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
     * @param DBVariant $ This is the object/array from the database server which contains the data for the desired participant
     */
    function explode($ptPtr)
    {
        $this -> _ID = $ptPtr -> ID;
        $this -> _email = $ptPtr -> email;
        $this -> _password = $ptPtr -> password;
        $this -> _listmode = $ptPtr -> listmode;
        $this -> _nonProfit = $ptPtr -> nonprofit;
        $this -> _teamID = $ptPtr -> team;
        $this -> _retireTo = $ptPtr -> retire_to;
        $this -> _friends[0] = $ptPtr -> friend_a;
        $this -> _friends[1] = $ptPtr -> friend_b;
        $this -> _friends[2] = $ptPtr -> friend_c;
        $this -> _friends[3] = $ptPtr -> friend_d;
        $this -> _friends[4] = $ptPtr -> friend_e;
        $this -> _demoYOB = $ptPtr -> dem_yob;
        $this -> _demoHeard = $ptPtr -> dem_heard;
        $this -> _demoGender = $ptPtr -> dem_gender;
        $this -> _demoMotivation = $ptPtr -> dem_motivation;
        $this -> _demoCountry = $ptPtr -> dem_country;
        $this -> _contactName = $ptPtr -> contact_name;
        $this -> _contactPhone = $ptPtr -> contact_phone;
        $this -> _retireDate = $ptPtr -> retire_date;

        return true;
    } 
} 

?>
