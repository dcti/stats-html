<?php
// $Id: project.php,v 1.1 2003/04/20 21:38:40 paul Exp $

//==========================================
// file: project.inc
// This file contains the classes which
// represent a project in the stats
// system.  It abstracts the concept of a
// project and its stats into objects.
//==========================================

/***
 * This class represents a project
 *
 * This class represents a project in the stats system.
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
 *
 * @access public
 ***/
class Project
{
     /*** Internal Class variables go here ***/
     // This needs to be filled in
     /*** END Internal Class variables ***/
     
     /***
      * The Id for the current project (read only)
      *
      * @access public
      * @type int
      ***/
     var $ID;
     
     /***
      * The type of the current project
      *
      * @access public
      * @type string
      ***/
     var $Type;

     /***
      * The name of the current project
      *
      * @access public
      * @type string
      ***/
     var $Name;

     /***
      * The status of the current project
      *
      * @access public
      * @type string
      ***/
     var $Status;
     
     /***
      * ... Other properties of the Project object (too numerous to list) ...
      ***/
      
     /***
      * Instantiates a new project object, and loads it with the specified project's information.
      *
      * @access public
      * @return void
      * @param DBClass The database connectivity to use
      *        int The ID of the project to load
      ***/
      function Project($dbPtr, $id) { }
	 
      /***
       * Loads the requested participant object using the current database connection
       *
       * @access public
       * @return bool
       * @param int The ID of the project to load
       ***/
       function load($id) { }
	 
      /***
       * Returns the current ProjectStats object for this project
       *
       * This routine is "load-on-demand", meaning that the data is retrieved from the DB
       * on first access, and then from a local variable thereafter.
       *
       * @access public
       * @return ProjectStats
       ***/
       function getCurrentStats() { }
     
      /***
       * Returns the requested amount of historical stats information for this project
       *
       * This routine retrieves the requested number of previous days of stats information
       * for this project.  You specify the start date, and the number of previous days
       * to retrieve.
       *
       * @access public
       * @return ProjectStats[]
       * @param date The date to start retrieval
       *        int The number of days prior to $start to retrieve data for
       ***/
       function getStatsHistory($start, $getDays) { }
     
      /***
       * Turns the current database-oriented object/array into an internal representation
       * 
       * This routine provides for an easy way to turn database-oriented objects/arrays
       * into the generic internal representation that we're using, avoiding a database hit
       * in cases where you already have the project's information.
       * This is functionally similar to object deserialization.
       *
       * @access protected
       * @return bool
       * @param DBVariant This is the object/array from the database server which contains the data for the desired project
       ***/
       function explode($prjInfo) { }	       
}
?>
