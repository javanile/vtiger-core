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
 * $Header:  vtiger_crm/modules/Calls/index.php,v 1.1 2004/08/17 15:03:41 gjk Exp $
 * Description: TODO:  To be written.
 ********************************************************************************/

global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
require_once ($theme_path."layout_utils.php");

echo get_module_title("Calls", "Calls: Home", true); 
echo "\n<BR>\n";
include ('modules/Calls/ListView.php'); 

?>