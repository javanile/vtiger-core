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
 * $Header:  vtiger_crm/modules/Emails/Email.php,v 1.1 2004/08/17 15:04:39 gjk Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/

include_once('config.php');
require_once('include/logging.php');
require_once('database/DatabaseConnection.php');
require_once('data/SugarBean.php');
require_once('modules/Contacts/Contact.php');
require_once('modules/Users/User.php');

// Email is used to store customer information.
class Email extends SugarBean {
	var $log;

	// Stored fields
	var $id;
	var $date_entered;
	var $date_modified;
	var $assigned_user_id;
	var $description;
	var $name;
	var $duration_hours;
	var $duration_minutes;
	var $date_start;
	var $time_start;
	var $parent_type;
	var $parent_id;
	var $contact_id;
	var $user_id;
	
	var $parent_name;
	var $contact_name;
	var $contact_phone;
	var $contact_email;
	var $account_id;
	var $opportunity_id;
	var $case_id;
	var $assigned_user_name;
	
	
	var $ddefault_email_name_values = array('Assemble catalogs', 'Make travel arrangements', 'Send a letter', 'Send contract', 'Send fax', 'Send a follow-up letter', 'Send literature', 'Send proposal', 'Send quote');
	var $minutes_values = array('00', '15', '30', '45');

	var $table_name = "emails";
	var $rel_users_table = "emails_users";
	var $rel_contacts_table = "emails_contacts";

	var $object_name = "Email";

	var $column_fields = Array("id"
		, "date_entered"
		, "date_modified"
		, "assigned_user_id"
		, "description"
		, "name"
		, "date_start"
		, "time_start"
		, "parent_type"
		, "parent_id"
		);

	// This is used to retrieve related fields from form posts.
	var $additional_column_fields = Array('assigned_user_name', 'assigned_user_id', 'contact_id', 'user_id', 'contact_name');		

	// This is the list of fields that are in the lists.
	var $list_fields = Array('id', 'name', 'parent_type', 'parent_name', 'parent_id', 'date_start', 'assigned_user_name', 'assigned_user_id');
		
	function Email() {
		$this->log = LoggerManager::getLogger('email');
	}

	var $new_schema = true;

	function create_tables () {
		$query = 'CREATE TABLE '.$this->table_name.' ( ';
		$query .='id char(36) NOT NULL';
		$query .=', date_entered datetime NOT NULL';
		$query .=', date_modified datetime NOT NULL';
		$query .=', assigned_user_id char(36)';
		$query .=', name char(255)';
		$query .=', date_start date'; 
		$query .=', time_start time'; 
		$query .=', parent_type char(25)';  
		$query .=', parent_id char(36)';
		$query .=', description text';
		$query .=', deleted bool NOT NULL default 0';
		$query .=', PRIMARY KEY ( ID ) )';

		$this->log->info($query);
		
		mysql_query($query) or die("Error creating table: ".mysql_error());

		//TODO Clint 4/27 - add exception handling logic here if the table can't be created.

		$query = "CREATE TABLE $this->rel_users_table (";
		$query .='id char(36) NOT NULL';
		$query .=', email_id char(36)';
		$query .=', user_id char(36)';
		$query .=', deleted bool NOT NULL default 0';
		$query .=', PRIMARY KEY ( ID ) )';
	
		$this->log->info($query);
		mysql_query($query) or die("Error creating email/user relationship table: ".mysql_error());
		
		$query = "CREATE TABLE $this->rel_contacts_table (";
		$query .='id char(36) NOT NULL';
		$query .=', email_id char(36)';
		$query .=', contact_id char(36)';
		$query .=', deleted bool NOT NULL default 0';
		$query .=', PRIMARY KEY ( ID ) )';
	
		$this->log->info($query);
		mysql_query($query) or die("Error creating email/contact relationship table: ".mysql_error());

		// Create the indexes
		$this->create_index("create index idx_email_name on emails (name)");
		$this->create_index("create index idx_usr_email_email on $this->rel_users_table (email_id)");
		$this->create_index("create index idx_usr_email_usr on $this->rel_users_table (user_id)");
		$this->create_index("create index idx_con_email_email on $this->rel_contacts_table (email_id)");
		$this->create_index("create index idx_con_email_con on $this->rel_contacts_table (contact_id)");
	}

	function drop_tables () {
		$query = 'DROP TABLE IF EXISTS '.$this->table_name;

		$this->log->info($query);
			
		mysql_query($query);

		$query = 'DROP TABLE IF EXISTS '.$this->rel_users_table;

		$this->log->info($query);
			
		mysql_query($query);

		$query = 'DROP TABLE IF EXISTS '.$this->rel_contacts_table;

		$this->log->info($query);
			
		mysql_query($query);

		//TODO Clint 4/27 - add exception handling logic here if the table can't be dropped.

	}
	
	/** Returns a list of the associated contacts
	*/
	function get_contacts()
	{
		// First, get the list of IDs.
		$query = "SELECT contact_id as id from emails_contacts where email_id='$this->id' AND deleted=0";

		return $this->build_related_list($query, new Contact());
	}
	
	/** Returns a list of the associated users
	*/
	function get_users()
	{
		// First, get the list of IDs.
		$query = "SELECT user_id as id from emails_users where email_id='$this->id' AND deleted=0";

		return $this->build_related_list($query, new User());
	}
	
	function save_relationship_changes($is_update)
    {
		if($this->account_id != "")
    	{
    		$this->set_emails_account_relationship($this->id, $this->account_id);    	
    	}
		if($this->opportunity_id != "")
    	{
    		$this->set_emails_opportunity_relationship($this->id, $this->opportunity_id);    	
    	}
		if($this->case_id != "")
    	{
    		$this->set_emails_case_relationship($this->id, $this->case_id);    	
    	}
		if($this->contact_id != "")
    	{
    		$this->set_emails_contact_invitee_relationship($this->id, $this->contact_id);    	
    	}
		if($this->user_id != "")
    	{
    		$this->set_emails_user_invitee_relationship($this->id, $this->user_id);    	
    	}
    }
	
	function set_emails_account_relationship($email_id, $account_id)
	{
		$query = "update $this->table_name set parent_id='$account_id', parent_type='Account' where _id='$email_id'";
		mysql_query($query) or die("Error setting account to email relationship: ".mysql_error()."<BR>$query");
	}

	function set_emails_opportunity_relationship($email_id, $opportunity_id)
	{
		$query = "update $this->table_name set parent_id='$opportunity_id', parent_type='Opportunity' where _id='$email_id'";
		mysql_query($query) or die("Error setting opportunity to email relationship: ".mysql_error()."<BR>$query");
	}

	function set_emails_case_relationship($email_id, $case_id)
	{
		$query = "update $this->table_name set parent_id='$case_id', parent_type='Case' where _id='$email_id'";
		mysql_query($query) or die("Error setting case to email relationship: ".mysql_error()."<BR>$query");
	}

	function set_emails_contact_invitee_relationship($email_id, $contact_id)
	{
		$query = "insert into $this->rel_contacts_table set id='".create_guid()."', contact_id='$contact_id', email_id='$email_id'";
		mysql_query($query) or die("Error setting email to contact relationship: ".mysql_error()."<BR>$query");
	}
	
	function set_emails_user_invitee_relationship($email_id, $user_id)
	{
		$query = "insert into $this->rel_users_table set id='".create_guid()."', user_id='$user_id', email_id='$email_id'";
		mysql_query($query) or die("Error setting email to user relationship: ".mysql_error()."<BR>$query");
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
			$query = "SELECT emails.id, emails.assigned_user_id, emails.name, emails.parent_type, emails.parent_id, emails.date_start, emails.time_start, contacts.first_name, contacts.last_name FROM contacts, emails, emails_contacts ";
			$where_auto = "emails_contacts.contact_id = contacts.id AND emails_contacts.email_id = emails.id AND emails.deleted=0 AND contacts.deleted=0";
		}
		else 
		{
			$query = 'SELECT id, name, assigned_user_id, parent_type, parent_id, date_start, time_start FROM emails ';
			$where_auto = "deleted=0";
		}
		
		if($where != "")
			$query .= "where $where AND ".$where_auto;
		else 
			$query .= "where ".$where_auto;		

		if($order_by != "")
			$query .= " ORDER BY $order_by";
		else 
			$query .= " ORDER BY emails.name";			

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

		$query  = "SELECT contacts.first_name, contacts.last_name, contacts.phone_work, contacts.email1, contacts.id FROM contacts, emails_contacts ";
		$query .= "WHERE emails_contacts.contact_id=contacts.id AND emails_contacts.email_id='$this->id' AND emails_contacts.deleted=0 AND contacts.deleted=0";
		$result = mysql_query($query) or die("Error filling in additional detail fields: ".mysql_error());

		// Get the id and the name.
		$row = mysql_fetch_assoc($result);
		
		$this->log->info($row);
		
		if($row != null)
		{
			$this->contact_name = return_name($row, 'first_name', 'last_name');				
			$this->contact_phone = $row['phone_work'];
			$this->contact_id = $row['id'];
			$this->contact_email = $row['email1'];
			$this->log->debug("Call($this->id): contact_name = $this->contact_name");
			$this->log->debug("Call($this->id): contact_phone = $this->contact_phone");
			$this->log->debug("Call($this->id): contact_id = $this->contact_id");
			$this->log->debug("Call($this->id): contact_email1 = $this->contact_email");
		}
		else {
			$this->contact_name = '';
			$this->contact_phone = '';
			$this->contact_id = '';
			$this->contact_email = '';
			$this->log->debug("Call($this->id): contact_name = $this->contact_name");
			$this->log->debug("Call($this->id): contact_phone = $this->contact_phone");
			$this->log->debug("Call($this->id): contact_id = $this->contact_id");
			$this->log->debug("Call($this->id): contact_email1 = $this->contact_email");
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
				$this->parent_name = stripslashes($row['name']);
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
				$this->parent_name = stripslashes($row['name']);
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
				$this->parent_name = stripslashes($row['name']);
			}
		}	
		else {
			$this->parent_name = '';
		}
	}
	
	function mark_relationships_deleted($id)
	{
		$query = "UPDATE $this->rel_users_table set deleted=1 where email_id='$id'";
		mysql_query($query) or die("Error marking record deleted: ".mysql_error());

		$query = "UPDATE $this->rel_contacts_table set deleted=1 where email_id='$id'";
		mysql_query($query) or die("Error marking record deleted: ".mysql_error());
	}
	
	function mark_email_contact_relationship_deleted($contact_id, $email_id)
	{
		$query = "UPDATE $this->rel_contacts_table set deleted=1 where contact_id='$contact_id' and email_id='$email_id' and deleted=0";
		mysql_query($query) or die("Error clearing email to contact relationship: ".mysql_error());
	}

	function mark_email_user_relationship_deleted($user_id, $email_id)
	{
		$query = "UPDATE $this->rel_users_table set deleted=1 where user_id='$user_id' and email_id='$email_id' and deleted=0";
		mysql_query($query) or die("Error clearing email to user relationship: ".mysql_error());
	}
}
?>
