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
 * $Header:  vtiger_crm/modules/Contacts/ContactOpportunityRelationship.php,v 1.1 2004/08/17 15:04:13 gjk Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/
include_once('config.php');
require_once('include/logging.php');
require_once('database/DatabaseConnection.php');
require_once('data/SugarBean.php');

// Contact is used to store customer information.
class ContactOpportunityRelationship extends SugarBean {
	var $log;

	// Stored fields
	var $id;
	var $contact_id;
	var $contact_role;
	var $opportunity_id;	

	// Related fields
	var $contact_name;
	var $opportunity_name;

	var $table_name = "opportunities_contacts";
	var $object_name = "ContactOpportunityRelationship";
	var $column_fields = Array("id"
		,"contact_id"
		,"opportunity_id"
		,"contact_role"
		);

	var $new_schema = true;

	var $additional_column_fields = Array();
		
	function ContactOpportunityRelationship() {
		$this->log = LoggerManager::getLogger('ContactOpportunityRelationship');
	}

	function fill_in_additional_detail_fields()
	{
		if(isset($this->contact_id) && $this->contact_id != "")
		{
			$query = "SELECT first_name, last_name from contacts where id='$this->contact_id' AND deleted=0";
			$result = mysql_query($query) or die("Error filling in additional detail fields: ".mysql_error());
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
	
			if($row != null)
			{
				$this->contact_name = return_name($row, 'first_name', 'last_name');				
			}
		}

		if(isset($this->opportunity_id) && $this->opportunity_id != "")
		{
			$query = "SELECT name from opportunities where id='$this->opportunity_id' AND deleted=0";
			$result = mysql_query($query) or die("Error filling in additional detail fields: ".mysql_error());
			// Get the id and the name.
			$row = mysql_fetch_assoc($result);
	
			if($row != null)
			{
				$this->opportunity_name = $row['name'];
			}
		}
		
	}

	function create_list_query(&$order_by, &$where)
	{
		$query = "SELECT id, yahoo_id, first_name, last_name, phone_work, title, email1 FROM contacts ";
		$where_auto = "deleted=0";
		
		if($where != "")
			$query .= "where $where AND ".$where_auto;
		else 
			$query .= "where ".$where_auto;		

		$query .= " ORDER BY last_name, first_name";

		return $query;
	}
}



?>
