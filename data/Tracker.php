<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header:  vtiger_crm/data/Tracker.php,v 1.1 2004/08/17 13:18:39 gjk Exp $
 * Description:  Updates entries for the Last Viewed functionality tracking the 
 * last viewed records on a per user basis.
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('database/DatabaseConnection.php');

/** This class is used to track the recently viewed items on a per user basis.
 * It is intended to be called by each module when rendering the detail form.
*/
class Tracker {
    var $log;

    var $table_name = "tracker";

    // Tracker table
    var $column_fields = Array(
        "id",
        "user_id",
        "module_name",
        "item_id",
        "item_summary"
    );

    function Tracker()
    {
        $this->log = LoggerManager::getLogger('Tracker');
    }

    /**
     * Add this new item to the tracker table.  If there are too many items (global config for now)
     * then remove the oldest item.  If there is more than one extra item, log an error.
     * If the new item is the same as the most recent item then do not change the list
     */
    function track_view($user_id, $module_name, $item_id, $item_summary)
    {
        $this->delete_history($user_id, $item_id);
        
        // Add a new item to the user's list

        $esc_item_id = addslashes($item_id);
        $esc_item_summary = addslashes($item_summary);
        
        $query = "INSERT into $this->table_name (user_id, module_name, item_id, item_summary) values ('$user_id', '$module_name', '$esc_item_id', '$esc_item_summary')";

        $this->log->info("Track Item View: ".$query);

        mysql_query($query)
            or die("MySQL error: ".mysql_error());

		$this->prune_history($user_id);
    }

    /**
     * param $user_id - The id of the user to retrive the history for
     * param $module_name - Filter the history to only return records from the specified module.  If not specified all records are returned
     * return - return the array of result set rows from the query.  All of the table fields are included
     */
    function get_recently_viewed($user_id, $module_name = "")
    {
        $query = "SELECT * from $this->table_name WHERE user_id='$user_id' ORDER BY id DESC";
        $this->log->debug("About to retrieve list: $query");
        $result = mysql_query($query)
            or die("MySQL error: ".mysql_error());

        $list = Array();
        while(true)
        {
            $row = mysql_fetch_assoc($result);
            if($row == false)
                break;
            
            // If the module was not specified or the module matches the module of the row, add the row to the list
            if($module_name == "" || $row[module_name] == $module_name)
            {
            	$list[] = $row;
            }
        }

        return $list;
    }

    
    
    /**
     * INTERNAL -- This method cleans out any entry for a record for a user.
     * It is used to remove old occurances of previously viewed items.
     */
    function delete_history( $user_id, $item_id)
    {
        $query = "DELETE from $this->table_name WHERE user_id='$user_id' and item_id='$item_id'";
        mysql_query($query)
            or die("MySQL error: ".mysql_error());
    }
    
    /**
     * INTERNAL -- This method cleans out any entry for a record.
     */
    function delete_item_history($item_id)
    {
        $query = "DELETE from $this->table_name WHERE item_id='$item_id'";
        mysql_query($query)
            or die("MySQL error: ".mysql_error());
    }
    
    /**
     * INTERNAL -- This function will clean out old history records for this user if necessary.
     */
    function prune_history($user_id)
    {
        global $history_max_viewed;

        // Check to see if the number of items in the list is now greater than the config max.
        $query = "SELECT count(*) from $this->table_name WHERE user_id='$user_id'";

        $this->log->debug("About to verify history size: $query");
        $result = mysql_query($query)
            or die("MySQL error: ".mysql_error());

        $count = mysql_result($result,0);

        $this->log->debug("history size: (current, max)($count, $history_max_viewed)");
        while($count > $history_max_viewed)
        {
            // delete the last one.  This assumes that entries are added one at a time.
            // we should never add a bunch of entries
            $query = "SELECT * from $this->table_name WHERE user_id='$user_id' ORDER BY id ASC LIMIT 1";
            $this->log->debug("About to try and find oldest item: $query");
            $result = mysql_query($query);

            $oldest_item = mysql_fetch_assoc($result);
            $query = "DELETE from $this->table_name WHERE id='{$oldest_item['id']}'";
            $this->log->debug("About to delete oldest item: $query");

            $result = mysql_query($query)
                or die("MySQL error: ".mysql_error());
                
            $count--;    
        }
    }
    
	function create_tables() {
		$query = 'CREATE TABLE '.$this->table_name.' (';
		$query = $query.'id int( 11 ) NOT NULL auto_increment';
		$query = $query.', user_id char(36)';
		$query = $query.', module_name char(25)';
		$query = $query.', item_id char(36)';
		$query = $query.', item_summary char(255)';
		$query = $query.', PRIMARY KEY ( ID ) )';
	
		$this->log->info($query);
	
		mysql_query($query);
	
	}

	function drop_tables () {
		$query = 'DROP TABLE IF EXISTS '.$this->table_name;

		$this->log->info($query);
			
		mysql_query($query);

		//TODO Clint 4/27 - add exception handling logic here if the table can't be dropped.

	}
}



?>