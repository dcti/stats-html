<?php
// $Id: project.php,v 1.2 2003/05/20 19:28:03 paul Exp $
// ==========================================
// file: project.inc
// This file contains the classes which
// represent a project in the stats
// system.  It abstracts the concept of a
// project and its stats into objects.
// ==========================================
/**
 * This class represents a project
 * 
 * This class represents a project in the stats system.
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
 * 
 * @access public 
 */
class Project {
    /**
     * ** Internal Class variables go here **
     */
    // This needs to be filled in
    var $_db;
    /**
     * ** END Internal Class variables **
     */

    /**
     * The Id for the current project (read only)
     * 
     * @access public 
     * @type int
     */
    var $_id;

    /**
     * The type of the current project
     * 
     * @access public 
     * @type string
     */
    var $type;

    /**
     * The name of the current project
     * 
     * @access public 
     * @type string
     */
	 
	var $_state;
    var $_name;
    var $_totalunits;
    var $_scale;
    var $_scaled_unit_name;
    var $_unscaled_unit_name;

    /**
     * The status of the current project
     * 
     * @access public 
     * @type string
     */
    var $Status;

    /**
     * ... Other properties of the Project object (too numerous to list) ...
     */

    function getprize()
    {
        return $this -> _state -> prize;
    } 

    function get_scaled_unit_name()
    {
        return $this -> _scaled_unit_name;
    } 

    function get_unscaled_unit_name()
    {
        return $this -> _unscaled_unit_name;
    } 

    function get_scale()
    {
        return $this -> _scale;
    } 

    function get_type()
    {
        return $this -> _type;
    } 

    /**
     * Instantiates a new project object, and loads it with the specified project's information.
     * 
     * @access public 
     * @return void 
     * @param DBClass $ The database connectivity to use
     *            int The ID of the project to load
     */
    function Project($dbPtr, $id)
    {
        $this -> _db = $dbPtr;
        $this -> _id = $id;
        $this -> load($id);
    } 

    /**
     * Loads the requested participant object using the current database connection
     * 
     * @access public 
     * @return bool 
     * @param int $ The ID of the project to load
     */
    function load($id)
    {
        $qs = "	select * from Projects where PROJECT_ID = $id";
        $prj_info = $this -> _db -> query_first($qs);
        $this -> _name = $prj_info -> name;
        $this -> _type = $prj_info -> project_type;
        $this -> _totalunits = (double)$prj_info -> work_unit_qty;
        $this -> _scale = (double)$prj_info -> work_unit_disp_multiplier / $prj_info -> work_unit_disp_divisor;
        $this -> _scaled_unit_name = $prj_info -> scaled_work_unit_name;
        $this -> _unscaled_unit_name = $prj_info -> unscaled_work_unit_name;
        $this -> _state = $prj_info ;
    } 

    /**
     * Returns the current ProjectStats object for this project
     * 
     * This routine is "load-on-demand", meaning that the data is retrieved from the DB
     * on first access, and then from a local variable thereafter.
     * 
     * @access public 
     * @return ProjectStats 
     */
    function getCurrentStats()
    {
    } 

    /**
     * Returns the requested amount of historical stats information for this project
     * 
     * This routine retrieves the requested number of previous days of stats information
     * for this project.  You specify the start date, and the number of previous days
     * to retrieve.
     * 
     * @access public 
     * @return ProjectStats []
     * @param date $ The date to start retrieval
     *            int The number of days prior to $start to retrieve data for
     */
    function getStatsHistory($start, $getDays)
    {
    } 

    /**
     * Turns the current database-oriented object/array into an internal representation
     * 
     * This routine provides for an easy way to turn database-oriented objects/arrays
     * into the generic internal representation that we're using, avoiding a database hit
     * in cases where you already have the project's information.
     * This is functionally similar to object deserialization.
     * 
     * @access protected 
     * @return bool 
     * @param DBVariant $ This is the object/array from the database server which contains the data for the desired project
     */
    function explode($prjInfo)
    {
    } 
} 

?>
