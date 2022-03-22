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
 * $Header:  vtiger_crm/modules/Opportunities/Forms.php,v 1.1 2004/08/17 15:06:09 gjk Exp $
 * Description:  Contains a variety of utility functions used to display UI 
 * components such as form headers and footers.  Intended to be modified on a per 
 * theme basis.
 ********************************************************************************/

/**
 * Create javascript to validate the data entered into a record.
 */
function get_validate_record_js () {
global $mod_strings;
global $app_strings;

$lbl_name = $mod_strings['LBL_LIST_OPPORTUNITY_NAME'];
$lbl_account_name = $mod_strings['LBL_LIST_ACCOUNT_NAME'];
$lbl_date_closed = $mod_strings['LBL_LIST_DATE_CLOSED'];
$lbl_sales_stage = $mod_strings['LBL_LIST_SALES_STAGE'];
$err_missing_required_fields = $app_strings['ERR_MISSING_REQUIRED_FIELDS'];
$err_invalid_email_address = $app_strings['ERR_INVALID_EMAIL_ADDRESS'];
$err_invalid_date_format = $app_strings['ERR_INVALID_DATE_FORMAT'];
$err_invalid_month = $app_strings['ERR_INVALID_MONTH'];
$err_invalid_day = $app_strings['ERR_INVALID_DAY'];
$err_invalid_year = $app_strings['ERR_INVALID_YEAR'];
$err_invalid_date = $app_strings['ERR_INVALID_DATE'];
$err_invalid_time = $app_strings['ERR_INVALID_TIME'];

$the_script  = <<<EOQ

<script type="text/javascript" language="Javascript">
<!--  to hide script contents from old browsers
/**
 * DHTML date validation script. Courtesy of SmartWebby.com (http://www.smartwebby.com/dhtml/)
 */
// Declaring valid date character, minimum year and maximum year
var dtCh= "-";
var minYear=1900;
var maxYear=2100;

function isInteger(s){
	var i;
    for (i = 0; i < s.length; i++){   
        // Check that current character is number.
        var c = s.charAt(i);
        if (((c < "0") || (c > "9"))) return false;
    }
    // All characters are numbers.
    return true;
}

function stripCharsInBag(s, bag){
	var i;
    var returnString = "";
    // Search through string's characters one by one.
    // If character is not in bag, append to returnString.
    for (i = 0; i < s.length; i++){   
        var c = s.charAt(i);
        if (bag.indexOf(c) == -1) returnString += c;
    }
    return returnString;
}

function daysInFebruary (year){
	// February has 29 days in any year evenly divisible by four,
    // EXCEPT for centurial years which are not also divisible by 400.
    return (((year % 4 == 0) && ( (!(year % 100 == 0)) || (year % 400 == 0))) ? 29 : 28 );
}
function DaysArray(n) {
	for (var i = 1; i <= n; i++) {
		this[i] = 31
		if (i==4 || i==6 || i==9 || i==11) {this[i] = 30}
		if (i==2) {this[i] = 29}
   } 
   return this
}

function isDate(dtStr){
	var daysInMonth = DaysArray(12)
	var pos1=dtStr.indexOf(dtCh)
	var pos2=dtStr.indexOf(dtCh,pos1+1)
	var strYear=dtStr.substring(0,pos1)
	var strMonth=dtStr.substring(pos1+1,pos2)
	var strDay=dtStr.substring(pos2+1)
	strYr=strYear
	if (strDay.charAt(0)=="0" && strDay.length>1) strDay=strDay.substring(1)
	if (strMonth.charAt(0)=="0" && strMonth.length>1) strMonth=strMonth.substring(1)
	for (var i = 1; i <= 3; i++) {
		if (strYr.charAt(0)=="0" && strYr.length>1) strYr=strYr.substring(1)
	}
	month=parseInt(strMonth)
	day=parseInt(strDay)
	year=parseInt(strYr)
	if (pos1==-1 || pos2==-1){
		alert("$err_invalid_date_format")
		return false
	}
	if (strMonth.length<1 || month<1 || month>12){
		alert("$err_invalid_month")
		return false
	}
	if (strDay.length<1 || day<1 || day>31 || (month==2 && day>daysInFebruary(year)) || day > daysInMonth[month]){
		alert("$err_invalid_day")
		return false
	}
	if (strYear.length != 4 || year==0 || year<minYear || year>maxYear){
		alert("$err_invalid_year")
		return false
	}
	if (dtStr.indexOf(dtCh,pos2+1)!=-1 || isInteger(stripCharsInBag(dtStr, dtCh))==false){
		alert("$err_invalid_date")
		return false
	}
return true
}


function verify_data(form) {
	var isError = false;
	var errorMessage = "";
	if (form.name.value == "") {
		isError = true;
		errorMessage += "\\n$lbl_name";
	}
	if (form.account_name.value == "") {
		isError = true;
		errorMessage += "\\n$lbl_account_name"; 
	}
	// TODO:  Clint - needs to be cleaned up
	if (form.account_name.value == "skip_me") {
		form.account_name.value = '';
	}
	if (isDate(form.date_closed.value)==false) {
		isError = true;
		errorMessage += "\\n$lbl_date_closed"; 
	}
	if (form.sales_stage.selected == "") {
		isError = true;
		errorMessage += "\\n$lbl_sales_stage";
	}
	// Here we decide whether to submit the form.
	if (isError == true) {
		alert("$err_missing_required_fields" + errorMessage);
		return false;
	}
	return true;
}
// end hiding contents from old browsers  -->
</script>

EOQ;

return $the_script;
}

/**
 * Create HTML form to enter a new record with the minimum necessary fields.
 */
function get_new_record_form () {
global $mod_strings;
global $app_strings;
global $app_list_strings;
global $current_user;

$lbl_required_symbol = $app_strings['LBL_REQUIRED_SYMBOL'];
$lbl_opportunity_name = $mod_strings['LBL_OPPORTUNITY_NAME'];
$lbl_sales_stage = $mod_strings['LBL_SALES_STAGE'];
$lbl_date_closed = $mod_strings['LBL_DATE_CLOSED'];
$lbl_amount = $mod_strings['LBL_AMOUNT'];
$ntc_date_format = $app_strings['NTC_DATE_FORMAT'];
$lbl_save_button_title = $app_strings['LBL_SAVE_BUTTON_TITLE'];
$lbl_save_button_key = $app_strings['LBL_SAVE_BUTTON_KEY'];
$lbl_save_button_label = $app_strings['LBL_SAVE_BUTTON_LABEL'];
$user_id = $current_user->id;

$the_form = get_left_form_header($mod_strings['LBL_NEW_FORM_TITLE']);
$the_form .= <<<EOQ

		<form name="OppSave" onSubmit="return verify_data(OppSave)" method="POST" action="index.php">
			<input type="hidden" name="module" value="Opportunities">
			<input type="hidden" name="record" value="">			
			<input type="hidden" name="account_name" value="skip_me">			
			<input type="hidden" name="assigned_user_id" value='${user_id}'>
			<input type="hidden" name="action" value="Save">
		<FONT class="required">$lbl_required_symbol</FONT>$lbl_opportunity_name<br>
		<input name='name' type="text" value=""><br>
		<FONT class="required">$lbl_required_symbol</FONT>$lbl_date_closed <font size="1"><em>$ntc_date_format</em></font><br>
		<input name='date_closed' type="text" value="">&nbsp;<br>
		<FONT class="required">$lbl_required_symbol</FONT>$lbl_sales_stage<br>
		<select name='sales_stage'>
EOQ;
$the_form .= get_select_options($app_list_strings['sales_stage_dom'], "");
$the_form .= <<<EOQ
		</select><br>
		<FONT class="required">$lbl_required_symbol</FONT>$lbl_amount<br>
		<input name='amount' type="text"><br><br>
		<input title="$lbl_save_button_title" accessKey="$lbl_save_button_key" class="button" type="submit" name="button" value="  $lbl_save_button_label  " >
		</form>
		
EOQ;

$the_form .= get_left_form_footer();
$the_form .= get_validate_record_js();

return $the_form;
}

?>