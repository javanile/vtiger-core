<?php 
/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the 
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is: SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/
/*********************************************************************************
 * $Header:  vtiger_crm/install/4createConfigFile.php,v 1.9 2004/08/26 11:44:30 srk Exp $
 * Description:  Executes a step in the installation process.
 ********************************************************************************/

require_once('include/utils.php');
 
session_start(); 

 
if (isset($_REQUEST['db_host_name'])) 	$db_host_name = 	$_REQUEST['db_host_name'];
if (isset($_REQUEST['db_user_name'])) 	$db_user_name = 	$_REQUEST['db_user_name'];
if (isset($_REQUEST['db_password'])) 	$db_password = 		$_REQUEST['db_password'];
if (isset($_REQUEST['db_name'])) 		$db_name  	= 		$_REQUEST['db_name'];
if (isset($_REQUEST['db_drop_tables'])) $db_drop_tables = 	$_REQUEST['db_drop_tables'];
if (isset($_REQUEST['db_create'])) 		$db_create = 		$_REQUEST['db_create'];
if (isset($_REQUEST['db_populate']))	$db_populate = 		$_REQUEST['db_populate'];
if (isset($_REQUEST['site_URL'])) 		$site_URL = 		$_REQUEST['site_URL'];
if (isset($_REQUEST['admin_email'])) 	$admin_email = 		$_REQUEST['admin_email'];
if (isset($_REQUEST['admin_password'])) $admin_password = 	$_REQUEST['admin_password'];

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>vtiger CRM Open Source Installer: Step 4</title>
<link rel="stylesheet" href="install/install.css" type="text/css" />
</head>
<body leftMargin="0" topMargin="0" marginheight="0" marginwidth="0">
<table width="100%" border="0" cellpadding="5" cellspacing="0"><tbody>
  <tr><td align="center"><a href="http://www.vtiger.com" target="_blank" title="vtiger CRM"><IMG alt="vtiger CRM" border="0" src="include/images/vtiger.jpg"/></a></td></tr>
</tbody></table>
<P></P>
<table align="center" border="0" cellpadding="2" cellspacing="2" border="1" width="60%"><tbody><tr> 
   <tr>
      <td width="100%">
		<table width=100% cellpadding="0" cellspacing="0" border="0"><tbody><tr>
			  <td>
			   <table cellpadding="0" cellspacing="0" border="0"><tbody><tr>
				<td class="formHeader" vAlign="top" align="left" height="20"> 
				 <IMG height="5" src="include/images/left_arc.gif" width="5" border="0"></td>
				<td class="formHeader" vAlign="middle" align="left" noWrap width="100%" height="20">Step 4: Create Config File</td>
				<td  class="formHeader" vAlign="top" align="right" height="20">
				  <IMG height="5" src="include/images/right_arc.gif" width="5" border="0"></td>
				</tr></tbody></table>
			  </td>
			  <td width="100%" align="right">&nbsp;</td>
			  </tr><tr>
			  <td colspan="2" width="100%" class="formHeader"><IMG width="100%" height="2" src="include/images/blank.gif"></td>
			  </tr>
		</tbody></table>
	  </td>
          </tr>
	<tr><td>&nbsp;</td></tr>
          <tr>
            <td width="100%">

<?php
if (isset($_REQUEST['root_directory'])) {
	if (get_magic_quotes_gpc()) { $root_directory = stripslashes($_REQUEST['root_directory']); }
	else { $root_directory = $_REQUEST['root_directory']; }
}
if (is_file('config.php')) {
	$is_writable = is_writable('config.php');
} 
else {
	$is_writable = is_writable('.');
}

$config = "<?php \n";
$config .= "/*********************************************************************************\n";
$config .= " * The contents of this file are subject to the SugarCRM Public License Version 1.1.2\n";
$config .= " * (\"License\"); You may not use this file except in compliance with the \n";
$config .= " * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL\n";
$config .= " * Software distributed under the License is distributed on an  \"AS IS\"  basis,\n";
$config .= " * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for\n";
$config .= " * the specific language governing rights and limitations under the License.\n";
$config .= " * The Original Code is: SugarCRM Open Source\n";
$config .= " * The Initial Developer of the Original Code is SugarCRM, Inc.\n";
$config .= " * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;\n";
$config .= " * All Rights Reserved.\n";
$config .= " * Contributor(s): ______________________________________.\n";
$config .= "********************************************************************************/\n\n";
$config .= "/* Database configuration\n";
$config .= "      db_host_name:     MySQL Database Hostname\n";
$config .= "      db_user_name:    	MySQL Username\n";
$config .= "      db_password:     	MySQL Password\n";
$config .= "      db_name:     		MySQL Database Name\n*/\n";
$config .= "\$dbconfig['db_host_name'] = 	'".$db_host_name."';\n";
$config .= "\$dbconfig['db_user_name'] = 	'".$db_user_name."';\n";
$config .= "\$dbconfig['db_password'] = 		'".$db_password."';\n";
$config .= "\$dbconfig['db_name'] = 			'".$db_name."';\n\n";
$config .= "\$host_name = '".$db_host_name."';\n\n";
$config .= "\$site_URL = '".$site_URL."';\n\n";
$config .= "\$root_directory = '".$root_directory."';\n\n";
$config .= "// This is the full path to the include directory including the trailing slash\n";
$config .= "\$includeDirectory = \$root_directory.'include/';\n\n";
$config .= "\$list_max_entries_per_page = '20';\n\n";
$config .= "\$history_max_viewed = '10';\n\n";
$config .= "//define list of menu tabs\n";
$config .= "\$moduleList = Array('Home', 'Dashboard', 'Contacts', 'Accounts', 'Opportunities', 'Cases', 'Notes', 'Calls', 'Emails', 'Meetings', 'Tasks');\n\n";
$config .= "\$default_module = 'Home';\n";
$config .= "\$default_action = 'index';\n\n";
$config .= "//set default theme\n";
$config .= "\$default_theme = 'orange';\n\n";
$config .= "// If true, the time to compose each page is placed in the browser.\n";
$config .= "\$calculate_response_time = true;\n";
$config .= "// Default Username - The default text that is placed initially in the login form for user name.\n";
$config .= "\$default_user_name = '';\n";
$config .= "// Default Password - The default text that is placed initially in the login form for password.\n";
$config .= "\$default_password = '';\n";
$config .= "// Create default user - If true, a user with the default username and password is created.\n";
$config .= "\$create_default_user = false;\n";
$config .= "\$default_user_is_admin = false;\n";
$config .= "// disable persistent connections - If your MySQL/PHP configuration does not support persistent connections set this to true to avoid a large performance slowdown\n";
$config .= "\$disable_persistent_connections = ".return_session_value_or_default('disable_persistent_connections', 'false').";\n";
$config .= "// Defined languages available.  The key must be the language file prefix.  E.g. 'en_us' is the prefix for every 'en_us.lang.php' file. \n";

$language_value = "Array('en_us'=>'US English',)";
if(isset($_SESSION['language_keys']) && isset($_SESSION['language_values']))
{
	$language_value = 'Array(';
	$language_keys = explode(',', urldecode($_SESSION['language_keys']));
	$language_values = explode(',', urldecode($_SESSION['language_values']));
	
	$size = count($language_keys);
	for($i = 0; $i < $size; $i+=1)
	{
		$language_value .= "'$language_keys[$i]'=>'$language_values[$i]',";
	}

	$language_value .= ')';
}

$config .= "\$languages = $language_value;\n";
$config .= "// Default charset if the language specific character set is not found.\n";
$config .= "\$default_charset = '".return_session_value_or_default('default_charset','ISO-8859-1')."';\n";
$config .= "// Default language in case all or part of the user's language pack is not available.\n";
$config .= "\$default_language = '".return_session_value_or_default('default_language','en_us')."';\n";
$config .= "// Translation String Prefix - This will add the language pack name to every translation string in the display.\n";
$config .= "\$translation_string_prefix = ".return_session_value_or_default('translation_string_prefix','false').";\n";
$config .= "?>";

if ($is_writable && ($config_file = @ fopen("config.php", "w"))) {
	fputs($config_file, $config, strlen($config));
	fclose($config_file);
	echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tbody><tr><td align=\"left\">";
	echo "<P><P><P>Successfully created config file <b>config.php</b> in</td>";
	echo "<td align=\"right\"><font color=\"00CC00\">".$root_directory."</font>\n";
	echo "</td></tr>";
} 
else {
	echo "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tbody><tr><td align=\"left\">";
	echo "Cannot write to config.php file in the directory <font color=red>".$root_directory."</font> file.\n";
	echo "<P>You can continue this installation by manually creating the config.php file and pasting the configuration information below into the config.php file.  However, you <strong>must </strong>create the config.php file before you continue to the next step.  <P>\n";
	echo  "<TEXTAREA class=\"dataInput\" rows=\"15\" cols=\"80\">".$config."</TEXTAREA>";
	echo "<P>Did you remember to create the config.php file?</td></tr>";
}

?>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td colspan="2" align="right">
	 <form action="install.php" method="post" name="form" id="form">
	 <input type="hidden" name="file" value="5createTables.php">
	 <input type="hidden" class="dataInput" name="db_host_name" value="<?php if (isset($db_host_name)) echo "$db_host_name"; ?>" />
     <input type="hidden" class="dataInput" name="db_user_name" value="<?php if (isset($db_user_name)) echo "$db_user_name"; ?>" />
     <input type="hidden" class="dataInput" name="db_password" value="<?php if (isset($db_password)) echo "$db_password"; ?>" />
     <input type="hidden" class="dataInput" name="db_name" value="<?php if (isset($db_name)) echo "$db_name"; ?>" />
     <input type="hidden" class="dataInput" name="db_drop_tables" value="<?php if (isset($db_drop_tables)) echo "$db_drop_tables"; ?>" />
     <input type="hidden" class="dataInput" name="db_create" value="<?php if (isset($db_create)) echo "$db_create"; ?>" />
     <input type="hidden" class="dataInput" name="db_populate" value="<?php if (isset($db_populate)) echo "$db_populate"; ?>" />
     <input type="hidden" class="dataInput" name="admin_email" value="<?php if (isset($admin_email)) echo "$admin_email"; ?>" />
     <input type="hidden" class="dataInput" name="admin_password" value="<?php if (isset($admin_password)) echo "$admin_password"; ?>" />
	 <input class="button" type="submit" name="next" value="Next" />			
	 </form>
	 </td></tr>
	 </tbody></table>
</td></tr></tbody></table>
</body>
</html>
