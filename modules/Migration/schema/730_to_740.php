<?php
/*+********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *********************************************************************************/

if (defined('VTIGER_UPGRADE')) {
	global $current_user, $adb;
    $db = PearDatabase::getInstance();
 
    $eventManager = new VTEventsManager($db);
    $className = 'Vtiger_RecordLabelUpdater_Handler';
    $eventManager->unregisterHandler($className);
    echo "Unregistered record label update handler.<br>";

    $moduleName = 'Users';
    $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
    $fieldName = 'userlabel';
    $blockModel = Vtiger_Block_Model::getInstance('LBL_MORE_INFORMATION', $moduleModel);
    if ($blockModel) {
        $fieldModel = Vtiger_Field_Model::getInstance($fieldName, $moduleModel);
        if (!$fieldModel) {
            $fieldModel				= new Vtiger_Field();
            $fieldModel->name		= $fieldName;
            $fieldModel->label		= 'User Label';
            $fieldModel->table		= 'vtiger_users';
            $fieldModel->columntype = 'VARCHAR(255)';
            $fieldModel->typeofdata = 'V~O';
            $fieldModel->displaytype= 3;
            $blockModel->addField($fieldModel);
            echo "<br>Successfully added <b>$fieldName</b> field to <b>$moduleName</b><br>";
        }
    }
    
    $entityFields = Vtiger_Functions::getEntityModuleInfo($moduleName);
    $entityFieldNames  = explode(',', $entityFields['fieldname']);
    $sql = "UPDATE vtiger_users SET $fieldName = TRIM(CONCAT_WS(' ',".implode(',', $entityFieldNames)."))";
    $db->pquery($sql, array());
    
    Vtiger_Access::syncSharingAccess();
}