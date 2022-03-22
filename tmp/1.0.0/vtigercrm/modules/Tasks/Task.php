<?php
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.mozilla.org/MPL
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
 * $Header:  vtiger_crm/modules/Tasks/Task.php,v 1.1 2004/08/17 15:06:23 gjk Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('database/DatabaseConnection.php');
require_once('data/SugarBean.php');

// Task is used to store customer information.
class Task extends SugarBean {
	var $log;

	// Stored fields
	var $id;
	var $date_entered;
	var $date_modified;
	var $assigned_user_id;
	var $description;
	var $name;
	var $status;
	var $date_due_flag;
	var $date_due;
	var $time_due;
	var $priority;
	var $parent_type;
	var $parent_id;
	var $contact_id;

	var $parent_name;
	var $contact_name;
	var $contact_phone;
	var $contact_email;
	var $assigned_user_name;
	
	var $default_task_name_values = array('Assemble catalogs', 'Make travel arrangements', 'Send a letter', 'Send contract', 'Send fax', 'Send a follow-up letter', 'Send literature', 'Send proposal', 'Send quote');

	var $table_name = "tasks";

	var $object_name = "Task";

	var $column_fields = Array("id"
		, "date_entered"
		, "date_modified"
		, "assigned_user_id"
		, "description"
		, "name"
		, "status"
		, "date_due"
		, "time_due"
		, "priority"
		, "date_due_flag"
		, "parent_type"
		, "parent_id"
		, "contact_id"
		);

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'assigned_user_id', 'contact_name', 'contact_phone', 'contact_email', 'parent_name');		

	// This is the list of fields that are in the lists.
	var $list_fields = Array('id', 'status', 'name', 'parent_type', 'parent_name', 'parent_id', 'date_due', 'contact_id', 'contact_name', 'assigned_user_name', 'assigned_user_id');
		
	function Task() {
		$this->log = LoggerManager::getLogger('task');
	}

	var $new_schema = true;

	function create_tables () {
		global $app_strings;
		
		$query = 'CREATE TABLE '.$this->table_name.' ( ';
		$query .='id char(36) NOT NULL';
		$query .=', date_entered datetime NOT NULL';
		$query .=', date_modified datetime NOT NULL';
		$query .=', assigned_user_id char(36)';
		$query .=', name char(50)';
		$query .=', status char(25)';
		$query .=', date_due_flag char(5) default \'on\''; 
		$query .=', date_due date'; 
		$query .=', time_due time'; 
		$query .=', parent_type char(25)';  
		$query .=', parent_id char(36)';
		$query .=', contact_id char(36)';
		$query .=', priority char(25)';  
		$query .=', description char(255)';
		$query .=', deleted bool NOT NULL default 0';
		$query .=', PRIMARY KEY ( ID ) )';

		$this->log->info($query);
		
		mysql_query($query) or die($app_strings['ERR_CREATING_TABLE'].mysql_error());

		// Create the indexes
		$this->create_index("create index idx_tsk_name on tasks (name)");
	}

	function drop_tables () {
		$query = 'DROP TABLE IF EXISTS '.$this->table_name;

		$this->log->info($query);
			
		mysql_query($query);

		//TODO Clint 4/27 - add exception handling logic here if the table can't be dropped.

	}
	
	function get_summary_text()
	{
		return "$this->name";
	}

	function create_list_query(&$order_by, &$where)
	{
		$contact_required = ereg("contacts", $where);

		if($contact_required)
		{
			$query = "SELECT tasks.id, tasks.assigned_user_id, tasks.status, tasks.name, tasks.parent_type, tasks.parent_id, tasks.contact_id, tasks.date_due, contacts.first_name, contacts.last_name FROM contacts, tasks ";
			$where_auto = "tasks.contact_id = contacts.id AND tasks.deleted=0 AND contacts.deleted=0";
		}
		else 
		{
			$query = 'SELECT id, assigned_user_id, status, name, parent_type, parent_id, contact_id, date_due FROM tasks ';
			$where_auto = "deleted=0";
		}
		
		if($where != "")
			$query .= "where $where AND ".$where_auto;
		else 
			$query .= "where ".$where_auto;		

		if($order_by != "")
			$query .= " ORDER BY $order_by";
		else 
			$query .= " ORDER BY name";			

		return $query;
	}


	function fill_in_additional_list_fields()
	{
		$this->fill_in_additional_detail_fields();	
	}
	
	function fill_in_additional_detail_fields()
	{
		// Fill in the assigned_user_name
		$this->assigned_user_name = get_assigned_user_name($this->assigned_user_id);

		global $app_strings;
		
		if (isset($this->contact_id)) {
			require_once("modules/Contacts/Contact.php");
			$contact = new Contact();
			$query = "SELECT first_name, last_name, phone_work, email1 from $contact->table_name where id = '$this->contact_id'";
			$result = mysql_query($query) or die($app_strings['ERR_CREATING_FIELDS'].mysql_error());
	
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
			
			if($row != null)
			{
				$this->contact_name = return_name($row, 'first_name', 'last_name');				
				if ($row['phone_work'] != '') $this->contact_phone = $row['phone_work'];
				if ($row['email1'] != '') $this->contact_email = $row['email1'];
			}
		}
		if ($this->parent_type == "Opportunity") {
			require_once("modules/Opportunities/Opportunity.php");
			$parent = new Opportunity();
			$query = "SELECT name from $parent->table_name where id = '$this->parent_id'";
			$result = mysql_query($query) or die($app_strings['ERR_CREATING_FIELDS'].mysql_error());
	
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
			
			if($row != null)
			{
				if ($row['name'] != '') $this->parent_name = stripslashes($row['name']);
			}
		}
		if ($this->parent_type == "Case") {
			require_once("modules/Cases/Case.php");
			$parent = new aCase();
			$query = "SELECT name from $parent->table_name where id = '$this->parent_id'";
			$result = mysql_query($query) or die($app_strings['ERR_CREATING_FIELDS'].mysql_error());
	
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
			
			if($row != null)
			{
				if ($row['name'] != '') $this->parent_name = stripslashes($row['name']);
			}
		}
		if ($this->parent_type == "Account") {
			require_once("modules/Accounts/Account.php");
			$parent = new Account();
			$query = "SELECT name from $parent->table_name where id = '$this->parent_id'";
			$result = mysql_query($query) or die($app_strings['ERR_CREATING_FIELDS'].mysql_error());
	
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
			
			if($row != null)
			{
				if ($row['name'] != '') $this->parent_name = stripslashes($row['name']);
			}
		}
	}
	
	



	function get_list_view_data(){
		global $action, $currentModule, $focus, $app_list_strings;
		$today = date("Y-m-d", time());
		$task_fields =    Array(
				'ID' => $this->id,
				'NAME' => $this->name,
				'STATUS' => $this->status,
				'CONTACT_NAME' => $this->contact_name,
				'CONTACT_ID' => $this->contact_id,
				'PARENT_NAME' => $this->parent_name,
				'PARENT_ID' => $this->parent_id,
				'DATE_DUE' => $this->date_due
			);
		if (isset($this->parent_type)) 
			$task_fields['PARENT_MODULE'] = $this->parent_type;
		if ($this->status != "Completed") {
			$task_fields['SET_COMPLETE'] = "<a href='index.php?return_module=$currentModule&return_action=$action&return_id=$focus->id&action=Save&module=Tasks&record=$this->id&status=Completed'>X</a>";
		}	
	
		if ($this->date_due	== '0000-00-00') $task_fields['DATE_DUE'] = '';
		if ($this->date_due	< $today) {
			$task_fields['DATE_DUE'] = "<font class='overdueTask'>".$task_fields['DATE_DUE']."</font>";
		}
		return $task_fields;
	}
	function list_view_pare_additional_sections(&$list_form){
		return $list_form;
	}
}
?>
