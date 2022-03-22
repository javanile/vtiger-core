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
 * $Header:  vtiger_crm/install/1checkSystem.php,v 1.7 2004/08/26 11:44:30 srk Exp $
 * Description:  Executes a step in the installation process.
 ********************************************************************************/

//get php configuration settings.  requires elaborate parsing of phpinfo() output
ob_start();
phpinfo(INFO_GENERAL);    
$string = ob_get_contents();
ob_end_clean();    

$pieces = explode("<h2", $string);
$settings = array();
foreach($pieces as $val)
{
   preg_match("/<a name=\"module_([^<>]*)\">/", $val, $sub_key);
   preg_match_all("/<tr[^>]*>
									   <td[^>]*>(.*)<\/td>
									   <td[^>]*>(.*)<\/td>/Ux", $val, $sub);
   preg_match_all("/<tr[^>]*>
									   <td[^>]*>(.*)<\/td>
									   <td[^>]*>(.*)<\/td>
									   <td[^>]*>(.*)<\/td>/Ux", $val, $sub_ext);
   foreach($sub[0] as $key => $val) {
		if (preg_match("/Configuration File \(php.ini\) Path /", $val)) { 
	   		$val = preg_replace("/Configuration File \(php.ini\) Path /", '', $val);
			$phpini = strip_tags($val);
	   	}
   }
   
}

$gd_info_alternate = 'function gd_info() {
$array = Array(
"GD Version" => "",
"FreeType Support" => 0,
"FreeType Support" => 0,
"FreeType Linkage" => "",
"T1Lib Support" => 0,
"GIF Read Support" => 0,
"GIF Create Support" => 0,
"JPG Support" => 0,
"PNG Support" => 0,
"WBMP Support" => 0,
"XBM Support" => 0
);
$gif_support = 0;

ob_start();
eval("phpinfo();");
$info = ob_get_contents();
ob_end_clean();

foreach(explode("\n", $info) as $line) {
if(strpos($line, "GD Version")!==false)
$array["GD Version"] = trim(str_replace("GD 
Version", "", strip_tags($line)));
if(strpos($line, "FreeType Support")!==false)
$array["FreeType Support"] = trim(str_replace
("FreeType Support", "", strip_tags($line)));
if(strpos($line, "FreeType Linkage")!==false)
$array["FreeType Linkage"] = trim(str_replace
("FreeType Linkage", "", strip_tags($line)));
if(strpos($line, "T1Lib Support")!==false)
$array["T1Lib Support"] = trim(str_replace
("T1Lib Support", "", strip_tags($line)));
if(strpos($line, "GIF Read Support")!==false)
$array["GIF Read Support"] = trim(str_replace
("GIF Read Support", "", strip_tags($line)));
if(strpos($line, "GIF Create Support")!==false)
$array["GIF Create Support"] = trim
(str_replace("GIF Create Support", "", strip_tags($line)));
if(strpos($line, "GIF Support")!==false)
$gif_support = trim(str_replace("GIF 
Support", "", strip_tags($line)));
if(strpos($line, "JPG Support")!==false)
$array["JPG Support"] = trim(str_replace("JPG 
Support", "", strip_tags($line)));
if(strpos($line, "PNG Support")!==false)
$array["PNG Support"] = trim(str_replace
("PNG Support", "", strip_tags($line)));
if(strpos($line, "WBMP Support")!==false)
$array["WBMP Support"] = trim(str_replace
("WBMP Support", "", strip_tags($line)));
if(strpos($line, "XBM Support")!==false)
$array["XBM Support"] = trim(str_replace
("XBM Support", "", strip_tags($line)));
}

if($gif_support==="enabled") {
$array["GIF Read Support"] = 1;
$array["GIF Create Support"] = 1;
}

if($array["FreeType Support"]==="enabled"){
$array["FreeType Support"] = 1; }

if($array["T1Lib Support"]==="enabled")
$array["T1Lib Support"] = 1; 

if($array["GIF Read Support"]==="enabled"){
$array["GIF Read Support"] = 1; }

if($array["GIF Create Support"]==="enabled")
$array["GIF Create Support"] = 1; 

if($array["JPG Support"]==="enabled")
$array["JPG Support"] = 1;

if($array["PNG Support"]==="enabled")
$array["PNG Support"] = 1;

if($array["WBMP Support"]==="enabled")
$array["WBMP Support"] = 1;

if($array["XBM Support"]==="enabled")
$array["XBM Support"] = 1;

return $array;
}';

?> 
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<HTML>
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>vtiger CRM Open Source Installer: Step 1</title>
<link rel="stylesheet" href="install/install.css" type="text/css" />
</head>
<body leftMargin="0" topMargin="0" marginheight="0" marginwidth="0" class="">
<table width="100%" border="0" cellpadding="3" cellspacing="0"><tbody>
  <tr>
      <td align="center"><a href="http://www.vtiger.com" target="_blank" title="vtiger CRM"><IMG alt="vtiger CRM" border="0" src="include/images/vtiger.jpg"/></a></a></td>
    </tr>
</tbody></table>
<table align="center" border="0" cellpadding="2" cellspacing="1" border="1" width="70%"><tbody>
    <tr> 
      <td width="100%" colspan="3">
		<table width=100% cellpadding="0" cellspacing="0" border="0"><tbody><tr>
			  <td>
			   <table cellpadding="0" cellspacing="0" border="0"><tbody><tr>
				<td class="formHeader" vAlign="top" align="left" height="20"> 
				 <IMG height="5" src="include/images/left_arc.gif" width="5" border="0"></td>
				<td class="formHeader" vAlign="middle" align="left" noWrap width="100%" height="20">Step 1: System Check</td>
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
    <tr><td colspan="3">In order for your vtiger CRM installation to function properly, please ensure all of the 
	system check items listed below are green.  If any are red, please take the necessary steps
	to fix them.</td></tr>
    <tr><td colspan="3" align="center">
	    <table cellpadding="1" cellspacing="1" border="0"><tbody>
		<tr>
			<td><strong>PHP version 4.2.x or 4.3.x.<BR><em><LI><font size=-2>NOTE: PHP version 5.0 not supported</font></em></strong></td>
			<td width="100">&nbsp;</td>
			<td align="right"><?php $php_version = phpversion(); echo $php_version < "4.2" && $php_version > "5.0" ?"<strong><font color=\"#FF0000\">Invalid version ($php_version) Installed</font></strong>":"<strong><font color=\"#00CC00\">Version $php_version Installed</font></strong>"; ?></td>
			<td>&nbsp;</td>
    	</tr>
		<tr>
			<td><strong>MySQL database</strong></td>
			<td width="100">&nbsp;</td>
        	<td align="right"><?php echo function_exists('mysql_connect')?"<strong><font color=\"#00CC00\">Available</font></strong>":"<strong><font color=\"#FF0000\">Not Available</font></strong>";?></td>
			<td>&nbsp;</td>
	    </tr>
		<tr> 
			<td><strong>config.php</strong></td>
			<td width="100">&nbsp;</td>
			<td align="right"><?php echo (is_writable('./config.php') || is_writable('.'))?"<strong><font color=\"#00CC00\">Writeable</font></strong>":"<strong><font color=\"#FF0000\">Not Writeable</font></strong>"; ?></td>
		</tr>
		<tr> 
			<td><strong>GD graphics library version 2.0 or later</strong></td>
			<td width="100">&nbsp;</td>
			<td align="right"><?php 
								if (!extension_loaded('gd')) {
									echo "<strong><font size=-1 color=\"#FF0000\">GD Library not configured in your PHP installation.<br>Check out our <a href='http://sourceforge.net/docman/?group_id=107819'>online documentation</a> for tips on enabling this library. You can ignore this error and continue your vtiger CRM installation, however the chart images simply won't work.</font></strong>";
								}
								else {
									if (function_exists('gd_info')) 
										$gd_info = gd_info();							 
									else
										eval($gd_info_alternate);
									
									if (isset($gd_info['GD Version'])) {
										$gd_version = $gd_info['GD Version']; 
										$gd_version=preg_replace('%[^0-9.]%', '', $gd_version); 
										if ($gd_version > "2.0") { 
											echo "<strong><font color=\"#00CC00\">Version $gd_version Installed</font></strong>";
										}
										else { 
											echo "<strong><font color=\"#FF0000\">Version $gd_version Installed.<br>Go to <a href='http://www.boutell.com/gd/'>http://www.boutell.com/gd/</a>, download the latest version, and configure php.ini to reference it.</font></strong>";
										}
									}
									else {
										echo "<strong><font size=-1 color=\"#FF0000\">GD Library available, but not properly configured in your PHP installation.<br>Check out our <a href='http://sourceforge.net/docman/?group_id=107819'>online documentation</a> for tips on enabling this library. You can ignore this error and continue your vtiger CRM installation, however the chart images simply won't work.</font></strong>";
									}
								}
								?>
			</td>
		</tr>
		<tr>
			<td align='center' colspan='4'><font size='2'><em><strong>Note:</strong> Your php configuration file (php.ini) 
			is located at <br><?php echo $phpini;?></em></font></td>
    	</tr>
       </tbody></table>
	</td></tr>
	<tr> 
       <td colspan="3" align="right">
	    <form action="install.php" method="post" name="form" id="form">
		<input type="hidden" name="file" value="2setConfig.php" />
		<input class="button" type="submit" name="next" value="Next" /></td>
    </tr>
	</tbody> 	
		
</form>
</body>
</html>