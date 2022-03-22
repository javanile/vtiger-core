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
 * $Header:  vtiger_crm/modules/Notes/ListView.php,v 1.1 2004/08/17 15:05:43 gjk Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/

require_once('XTemplate/xtpl.php');
require_once("data/Tracker.php");
require_once('modules/Notes/Note.php');
require_once('themes/'.$theme.'/layout_utils.php');
require_once('include/logging.php');

global $app_strings;
global $app_list_strings;
global $mod_strings;

global $list_max_entries_per_page;
global $urlPrefix;

$log = LoggerManager::getLogger('note_list');

global $currentModule;

global $image_path;
global $theme;

// focus_list is the means of passing data to a ListView.
global $focus_list;

if (!isset($_REQUEST['search_form']) || $_REQUEST['search_form'] != 'false') {
	// Stick the form header out there.
	$search_form=new XTemplate ('modules/Notes/SearchForm.html');
	$search_form->assign("MOD", $mod_strings);
	$search_form->assign("APP", $app_strings);
	
	if(isset($_REQUEST['query'])) {
		if(isset($_REQUEST['name'])) $search_form->assign("NAME", $_REQUEST['name']);
		if(isset($_REQUEST['contact_name'])) $search_form->assign("CONTACT_NAME", $_REQUEST['contact_name']);
	}
	$search_form->parse("main");
	
	echo get_form_header($mod_strings['LBL_SEARCH_FORM_TITLE'], "", false);
	$search_form->out("main");
	echo get_form_footer();
	echo "\n<BR>\n";
}

$list_form=new XTemplate ('modules/Notes/ListView.html');
$list_form->assign("MOD", $mod_strings);
$list_form->assign("APP", $app_strings);
$list_form->assign("THEME", $theme);
$list_form->assign("IMAGE_PATH", $image_path);
$list_form->assign("MODULE_NAME", $currentModule);

$where = "";

$seedNote = new Note();
if(isset($_REQUEST['query']))
{
	// we have a query
	if (isset($_REQUEST['name'])) $name = $_REQUEST['name'];
	if (isset($_REQUEST['contact_name'])) $contact_name = $_REQUEST['contact_name'];

	$where_clauses = Array();

	if(isset($name) && $name != '')
	{
		array_push($where_clauses, "notes.name like '$name%'");
	}
	if(isset($contact_name) && $contact_name != '')
	{
		$contact_names = explode(" ", $contact_name);
		foreach ($contact_names as $name) {
			array_push($where_clauses, "(contacts.first_name like '$name%' OR contacts.last_name like '$name%')");
		}
	}

	$where = "";
	if (isset($where_clauses)) {
		foreach($where_clauses as $clause)
		{
			if($where != "")
			$where .= " and ";
			$where .= $clause;
		}
	}
	$log->info("Here is the where clause for the list view: $where");

}

$current_offset = 0;
if(isset($_REQUEST['current_offset']))
    $current_offset = $_REQUEST['current_offset'];

$response = $seedNote->get_list("notes.date_entered DESC", $where, $current_offset);

$noteList = $response['list'];
$row_count = $response['row_count'];
$next_offset = $response['next_offset'];
$previous_offset = $response['previous_offset'];

$start_record = $current_offset + 1;

// Set the start row to 0 if there are no rows (adding one looks bad)
if($row_count == 0)
    $start_record = 0;

$end_record = $start_record + $list_max_entries_per_page;

// back up the the last page.
if($end_record > $row_count+1)
{
    $end_record = $row_count+1;
}

// Deterime the start location of the last page
if($row_count == 0)
	$number_pages = 0;
else
	$number_pages = floor(($row_count - 1) / $list_max_entries_per_page);

$last_page_offset = $number_pages * $list_max_entries_per_page;


// Create the base URL without the current offset.
// Check to see if the current offset is already there
// If not, add it to the end.

// All of the other values should use a regular expression search
$base_URL = $_SERVER['REQUEST_URI'].'?'.$_SERVER['QUERY_STRING']."&current_offset=";
$start_URL = $base_URL."0";
$previous_URL  = $base_URL.$previous_offset;
$next_URL  = $base_URL.$next_offset;
$end_URL  = $base_URL.$last_page_offset;

$sort_URL_base = $base_URL.$current_offset."&sort_order=";

$log->debug("Offsets: (start, previous, next, last)(0, $previous_offset, $next_offset, $last_page_offset)");

if(0 == $current_offset)
    $start_link = $app_strings['LNK_LIST_START'];
else
    $start_link = "<a href=\"$start_URL\" class=\"listFormHeaderLinks\">".$app_strings['LNK_LIST_START']."</a>";

if($previous_offset < 0)
    $previous_link = $app_strings['LNK_LIST_PREVIOUS'];
else
    $previous_link = "<a href=\"$previous_URL\" class=\"listFormHeaderLinks\">".$app_strings['LNK_LIST_PREVIOUS']."</a>";

if($next_offset >= $end_record)
    $next_link = $app_strings['LNK_LIST_NEXT'];
else
    $next_link = "<a href=\"$next_URL\" class=\"listFormHeaderLinks\">".$app_strings['LNK_LIST_NEXT']."</a>";

if($last_page_offset <= $current_offset)
    $end_link = $app_strings['LNK_LIST_END'];
else
    $end_link = "<a href=\"$end_URL\" class=\"listFormHeaderLinks\">".$app_strings['LNK_LIST_END']."</a>";
	
$log->info("Offset (next, current, prev)($next_offset, $current_offset, $previous_offset)");
$log->info("Start/end records ($start_record, $end_record)");

$list_form->assign("START_RECORD", $start_record);
$list_form->assign("END_RECORD", $end_record-1);
$list_form->assign("ROW_COUNT", $row_count);
if ($start_link !== "") $list_form->assign("START_LINK", "[ ".$start_link." ]");
if ($end_link !== "") $list_form->assign("END_LINK", "[ ".$end_link." ]");
if ($next_link !== "") $list_form->assign("NEXT_LINK", "[ ".$next_link." ]");
if ($previous_link !== "") $list_form->assign("PREVIOUS_LINK", "[ ".$previous_link." ]");
$list_form->parse("main.list_nav_row");


$oddRow = true;
foreach($noteList as $note)
{
	$note_fields = array(
		'ID' => $note->id,
		'NAME' => $note->name,
		'CONTACT_NAME' => $note->contact_name,
		'CONTACT_ID' => $note->contact_id,
		'PARENT_NAME' => $note->parent_name,
		'PARENT_ID' => $note->parent_id,
		'DATE_MODIFIED' => substr($note->date_modified, 0, 10)
	);

	if (isset($note->parent_type)) 
		$note_fields['PARENT_MODULE'] = $app_list_strings['record_type_module'][$note->parent_type];
	
	$list_form->assign("NOTE", $note_fields);
	
	if($oddRow)
    {
        //todo move to themes
		$list_form->assign("ROW_COLOR", 'oddListRow');
    }
    else
    {
        //todo move to themes
		$list_form->assign("ROW_COLOR", 'evenListRow');
    }
    $oddRow = !$oddRow;

	$list_form->parse("main.row");
// Put the rows in.
}

$list_form->parse("main");

global $note_title;

if ($note_title) echo get_form_header($note_title, "", false);
else echo get_form_header($mod_strings['LBL_LIST_FORM_TITLE'], "", false);
$list_form->out("main");
echo get_form_footer();

echo "</td></tr>\n</table>\n";

?>
