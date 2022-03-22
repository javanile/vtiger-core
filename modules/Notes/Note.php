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
 * $Header:  vtiger_crm/modules/Notes/Note.php,v 1.1 2004/08/17 15:05:43 gjk Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('database/DatabaseConnection.php');
require_once('data/SugarBean.php');

// Note is used to store customer information.
class Note extends SugarBean {
	var $log;

	// Stored fields
	var $id;
	var $date_entered;
	var $date_modified;
	var $description;
	var $name;
	var $parent_type;
	var $parent_id;
	var $contact_id;

	var $parent_name;
	var $contact_name;
	var $contact_phone;
	var $contact_email;
	
	var $default_note_name_dom = array('Meeting notes', 'Reminder');

	var $table_name = "notes";

	var $object_name = "Note";

	var $column_fields = Array("id"
		, "date_entered"
		, "date_modified"
		, "description"
		, "name"
		, "parent_type"
		, "parent_id"
		, "contact_id"
		);

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('contact_name', 'contact_phone', 'contact_email', 'parent_name');		

	// This is the list of fields that are in the lists.
	var $list_fields = Array('id', 'name', 'parent_type', 'parent_name', 'parent_id', 'date_modified', 'contact_id', 'contact_name');
		
	function Note() {
		$this->log = LoggerManager::getLogger('note');
	}

	var $new_schema = true;

	function create_tables () {
		$query = 'CREATE TABLE '.$this->table_name.' ( ';
		$query .='id char(36) NOT NULL';
		$query .=', date_entered datetime NOT NULL';
		$query .=', date_modified datetime NOT NULL';
		$query .=', name char(255)';
		$query .=', parent_type char(25)';  
		$query .=', parent_id char(36)';
		$query .=', contact_id char(36)';
		$query .=', description text';
		$query .=', deleted bool NOT NULL default 0';
		$query .=', PRIMARY KEY ( ID ) )';

		$this->log->info($query);
		
		mysql_query($query) or die("Error creating table: ".mysql_error());

		//TODO Clint 4/27 - add exception handling logic here if the table can't be created.
		
		// Create the indexes
		$this->create_index("create index idx_note_name on notes (name)");
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
		$contact_required = ereg("contacts\.first_name", $where);

		if($contact_required)
		{
			$query = "SELECT notes.id, notes.name, notes.parent_type, notes.parent_id, notes.contact_id, notes.date_modified, contacts.first_name, contacts.last_name FROM contacts, notes ";
			$where_auto = "notes.contact_id = contacts.id AND notes.deleted=0 AND contacts.deleted=0";
		}
		else 
		{
			$query = 'SELECT id, name, parent_type, parent_id, contact_id, date_modified FROM notes ';
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
		# TODO:  Seems odd we need to clear out these values so that list views don't show the previous rows value if current value is blank
		$this->contact_name = '';
		$this->contact_phone = '';
		$this->contact_email = '';
		$this->parent_name = '';

		if (isset($this->contact_id) && $this->contact_id != '') {
			require_once("modules/Contacts/Contact.php");
			$contact = new Contact();
			$query = "SELECT first_name, last_name, phone_work, email1 from $contact->table_name where id = '$this->contact_id'";
			$result = mysql_query($query) or die("Error filling in additional detail fields: ".mysql_error());
	
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
			
			if($row != null)
			{
				$this->contact_name = return_name($row, 'first_name', 'last_name');				
				if ($row['phone_work'] != '') $this->contact_phone = $row['phone_work'];
				else $this->contact_phone = '';
				if ($row['email1'] != '') $this->contact_email = $row['email1'];
				else $this->contact_email = '';
			}
		}

		if ($this->parent_type == "Opportunity") {
			require_once("modules/Opportunities/Opportunity.php");
			$parent = new Opportunity();
			$query = "SELECT name from $parent->table_name where id = '$this->parent_id'";
			$result = mysql_query($query) or die("Error filling in additional detail fields: ".mysql_error());
	
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
			
			if($row != null)
			{
				if ($row['name'] != '') stripslashes($this->parent_name = $row['name']);
			}
		}
		elseif ($this->parent_type == "Case") {
			require_once("modules/Cases/Case.php");
			$parent = new aCase();
			$query = "SELECT name from $parent->table_name where id = '$this->parent_id'";
			$result = mysql_query($query) or die("Error filling in additional detail fields: ".mysql_error());
	
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
			
			if($row != null)
			{
				if ($row['name'] != '') $this->parent_name = stripslashes($row['name']);
			}
		}
		elseif ($this->parent_type == "Account") {
			require_once("modules/Accounts/Account.php");
			$parent = new Account();
			$query = "SELECT name from $parent->table_name where id = '$this->parent_id'";
			$result = mysql_query($query) or die("Error filling in additional detail fields: ".mysql_error());
	
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
			
			if($row != null)
			{
				if ($row['name'] != '') $this->parent_name = stripslashes($row['name']);
			}
		}
	}
	
	
}
?>
