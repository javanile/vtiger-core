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
 * $Header:  vtiger_crm/modules/Accounts/DetailView.php,v 1.1 2004/08/17 15:02:56 gjk Exp $
 * Description:  TODO To be written.
 ********************************************************************************/

require_once('XTemplate/xtpl.php');
require_once('data/Tracker.php');
require_once('modules/Accounts/Account.php');
global $mod_strings;
global $app_strings;
global $app_list_strings;

$focus = new Account();
//$focus->set_strings();
//var_dump($focus);

if(isset($_REQUEST['record']) && isset($_REQUEST['record'])) {
    $focus->retrieve($_REQUEST['record']);
}
if(isset($_REQUEST['isDuplicate']) && $_REQUEST['isDuplicate'] == 'true') {
	$focus->id = "";
} 

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once($theme_path.'layout_utils.php');

$log->info("Account detail view");

$xtpl=new XTemplate ('modules/Accounts/DetailView.html');
$xtpl->assign("MOD", $mod_strings);
$xtpl->assign("APP", $app_strings);

$xtpl->assign("THEME", $theme);
$xtpl->assign("IMAGE_PATH", $image_path);
$xtpl->assign("PRINT_URL", "phprint.php?jt=".session_id());
if ($focus->annual_revenue != '') $xtpl->assign("ANNUAL_REVENUE", $language['LBL_CURRENCY_SYMBOL'].$focus->annual_revenue);
$xtpl->assign("BILLING_ADDRESS_STREET", $focus->billing_address_street);
$xtpl->assign("BILLING_ADDRESS_CITY", $focus->billing_address_city);
$xtpl->assign("BILLING_ADDRESS_STATE", $focus->billing_address_state);
$xtpl->assign("BILLING_ADDRESS_POSTALCODE", $focus->billing_address_postalcode);
$xtpl->assign("BILLING_ADDRESS_COUNTRY", $focus->billing_address_country);
$xtpl->assign("DATE_ENTERED", $focus->date_entered); 
$xtpl->assign("ASSIGNED_TO", $focus->assigned_user_name);
$xtpl->assign("DESCRIPTION", $focus->description);
$xtpl->assign("EMAIL1", $focus->email1);
$xtpl->assign("EMAIL2", $focus->email2);
$xtpl->assign("EMPLOYEES", $focus->employees);
$xtpl->assign("ID", $focus->id);
$xtpl->assign("INDUSTRY", $app_list_strings['industry_dom'][$focus->industry]);
$xtpl->assign("NAME", $focus->name);
$xtpl->assign("OWNERSHIP", $focus->ownership);
$xtpl->assign("PARENT_ID", $focus->parent_id);
$xtpl->assign("PARENT_NAME", $focus->parent_name);
$xtpl->assign("PHONE_ALTERNATE", $focus->phone_alternate);
$xtpl->assign("PHONE_FAX", $focus->phone_fax);
$xtpl->assign("PHONE_OFFICE", $focus->phone_office);
$xtpl->assign("RATING", $focus->rating);
$xtpl->assign("SHIPPING_ADDRESS_STREET", $focus->shipping_address_street);
$xtpl->assign("SHIPPING_ADDRESS_CITY", $focus->shipping_address_city);
$xtpl->assign("SHIPPING_ADDRESS_STATE", $focus->shipping_address_state);
$xtpl->assign("SHIPPING_ADDRESS_COUNTRY", $focus->shipping_address_country);
$xtpl->assign("SHIPPING_ADDRESS_POSTALCODE", $focus->shipping_address_postalcode);
$xtpl->assign("SIC_CODE", $focus->sic_code);
$xtpl->assign("TICKER_SYMBOL", $focus->ticker_symbol);
$xtpl->assign("ACCOUNT_TYPE", $app_list_strings['account_type_dom'][$focus->account_type]);
if ($focus->website != '') $xtpl->assign("WEBSITE", $focus->website);

$xtpl->parse("main");
$xtpl->out("main");

echo "<BR>\n";

// Now get the list of contacts that match this one.
$focus_list = & $focus->get_contacts();

include('modules/Contacts/SubPanelView.php');

echo "<BR>\n";

// Now get the list of member accounts that match this one.
$focus_list = & $focus->get_member_accounts();

include('modules/Accounts/SubPanelView.php');

echo "<BR>\n";

// Now get the list of opportunities that match this one.
$focus_list = & $focus->get_opportunities();

include('modules/Opportunities/SubPanelView.php');

echo "<BR>\n";

// Now get the list of cases that match this one.
$focus_list = & $focus->get_cases();

include('modules/Cases/SubPanelView.php');

echo "<BR>\n";

// Now get the list of activities that match this account.
$focus_tasks_list = & $focus->get_tasks();
$focus_meetings_list = & $focus->get_meetings();
$focus_calls_list = & $focus->get_calls();
$focus_emails_list = & $focus->get_emails();
$focus_notes_list = & $focus->get_notes();

include('modules/Activities/SubPanelView.php');

echo "</td></tr>\n";

?>