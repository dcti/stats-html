<?php
// $Id: projectstats.php,v 1.1 2003/04/20 21:38:40 paul Exp $

//==========================================
// file: projectstats.inc
// This file contains the classes which
// represent projects in the stats
// system.  It abstracts the concept of a
// project and their stats into objects.
//==========================================

/***
 * This class represents a project stats entry
 *
 * This class represents a specific instance of the stats
 * for a given project in the stats system.
 * Although PHP supports it, member variables should _not_
 * be accessed directly, please adhere to the published
 * public interface, as private methods and signatures
 * can change at any time.
 *
 * @access public
 ***/
class ProjectStats
{
     /*** Internal Class variables go here ***/
     // This needs to be filled in
     /*** END Internal Class variables ***/
     
     /***
      * The Id for the current project stats info (read only)
      *
      * @access public
      * @type int
      ***/
      var $ID;
     
     /***
      * The StatsDate for the current project stats info
      *
      * @access public
      * @type date
      ***/
      var $StatsDate;

     /***
      * Instantiates a new [empty] project stats object
      *
      * @access public
      * @return void
      * @param DBClass The database connectivity to use
      ***/
      function ProjectStats($dbPtr) { }
	 
     /***
      * Instantiates a new project stats object, and loads it with the specified project stats information.
      *
      * @access public
      * @return void
      * @param DBClass The database connectivity to use
      *        int The ID of the project to load
      *        date The stats date to load
      ***/
      function ParticipantStats($dbPtr, $id, $project, $date) { }
	 
     /***
      * Loads the requested project stats object using the current database connection
      *
      *  This function loads the requested date's stats data.
      *
      * @access publiC
      * @return bool
      * @param int The ID of the project to load
      *        date The date to load for
      ***/
      function load($id, $date) { }
 
     /***
      * Loads the requested project stats object using the current database connection.
      *
      *  This function loads the most current stats data available.
      *
      * @access public
      * @return bool
      * @param int The ID of the project to load
      ***/
      function loadCurrent($id) { }
     
     /***
      * Loads the requested project stats object using the current database connection.
      *
      *  This function loads the requested historical stats data available.
      *
      * @access public
      * @return ProjectStats[]
      * @param int The ID of the project to load
      *        date date to start from
      *        int Number of days prior to start (including start) to retrieve
      ***/
      function loadHistorical($id, $start, $days_back) { }
     
     
     /***
      * Returns the requested stats item for this ProjectStats instance
      *
      * This routine retrieves the requested stats item (based on string index)
      *
      * @access public
      * @return variant
      * @param string The stats item to retrieve (i.e. FirstBlock)
      ***/
      function getStatsItem($name) { }
     
     /***
      * Returns the available stats items
      *
      * This routine retrieves the available stats items
      *
      * @access public
      * @return string[]
      ***/
      function getStatsItems() { }
     
     /***
      * Turns the current database-oriented object/array into an internal representation
      * 
      * This routine provides for an easy way to turn database-oriented objects/arrays
      * into the generic internal representation that we're using, avoiding a database hit
      * in cases where you already have the participants information (i.e. when loading
      * friends/neighbors).  This is functionally similar to object deserialization.
      *
      * @access protected
      * @return bool
      * @param DBVariant This is the object/array from the database server which contains the data for the desired participant stats object
      ***/
      function explode($parStatsInfo) { }	       
}
?>
