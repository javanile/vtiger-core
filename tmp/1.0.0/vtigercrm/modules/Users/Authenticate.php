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
 * $Header:  vtiger_crm/modules/Users/Authenticate.php,v 1.1 2004/08/17 15:06:40 gjk Exp $
 * Description:  TODO: To be written.
 ********************************************************************************/

require_once('modules/Users/User.php');
require_once('include/logging.php');

global $mod_strings;

$local_log =& LoggerManager::getLogger('authenticate');

$focus = new User();

// Add in defensive code here.
$focus->user_name = $_REQUEST['user_name'];
$user_password = $_REQUEST['user_password'];

$focus->load_user($user_password);

if($focus->is_authenticated())
{
	// save the user information into the session
	// go to the home screen
	header("Location: index.php?action=index&module=Home");
	session_unregister('login_password');
	session_unregister('login_error');
	session_unregister('login_user_name');

	$_SESSION['authenticated_user_id'] = $focus->id;

	// store the user's theme in the session
	$authenticated_user_theme = $focus->theme;
	
	// store the user's language in the session
	$authenticated_user_language = $focus->language;

	// If this is the default user and the default user theme is set to reset, reset it to the default theme value on each login
	if($reset_theme_on_default_user && $focus->user_name == $default_user_name)
	{
		$authenticated_user_theme = $default_theme;
	}
	if(isset($reset_language_on_default_user) && $reset_language_on_default_user && $focus->user_name == $default_user_name)
	{
		$authenticated_user_language = $default_language;	
	}

	$_SESSION['authenticated_user_theme'] = $authenticated_user_theme;
	$_SESSION['authenticated_user_language'] = $authenticated_user_language;
}
else
{
	$_SESSION['login_user_name'] = $focus->user_name;
	$_SESSION['login_password'] = $user_password;
	$_SESSION['login_error'] = $mod_strings['ERR_INVALID_PASSWORD'];
	
	// go back to the login screen.	
	// create an error message for the user.
	header("Location: index.php");
}

?>