#!/bin/bash
set -e

download_url=http://sourceforge.net/projects/vtigercrm/files/

declare -a versions
versions=(
  "7.5.0=vtiger%20CRM%207.5.0/Core%20Product/vtigercrm7.5.0.tar.gz"
  "6.0.0=vtiger%20CRM%206.0.0/Core%20Product/vtigercrm6.0.0.tar.gz"
  "5.4.0=vtiger%20CRM%205.4.0/Core%20Product/vtigercrm-5.4.0.tar.gz"
  "5.3.0=vtiger%20CRM%205.3.0/Core%20Product/vtigercrm-5.3.0.tar.gz"
  "1.0.0=vtiger%20CRM%20Release%20Archive/vtiger%20CRM%201.0/vtigercrm_source_1_0.zip"
)

## Supported versions
# ["7.2.0"]=vtiger%20CRM%207.2.0/Core%20Product/vtigercrm7.2.0.tar.gz
# ["7.1.0"]=vtiger%20CRM%207.1.0/Core%20Product/vtigercrm7.1.0.tar.gz
# ["7.1.0-RC"]=vtiger%20CRM%207.1.0%20RC/Core%20Product/vtigercrm7.1.0rc.tar.gz
# ["7.0.1"]=vtiger%20CRM%207.0.1/Core%20Product/vtigercrm7.0.1.tar.gz
# ["7.0.0"]=vtiger%20CRM%207.0/Core%20Product/vtigercrm7.0.0.tar.gz
# ["6.5.0"]=vtiger%20CRM%206.5.0/Core%20Product/vtigercrm6.5.0.tar.gz
# ["6.4.0"]=vtiger%20CRM%206.4.0/Core%20Product/vtigercrm6.4.0.tar.gz
# ["6.3.0"]=vtiger%20CRM%206.3.0/Core%20Product/vtigercrm6.3.0.tar.gz
# ["6.2.0"]=vtiger%20CRM%206.2.0/Core%20Product/vtigercrm6.2.0.tar.gz
# ["6.1.0"]=vtiger%20CRM%206.1.0/Core%20Product/vtigercrm6.1.0.tar.gz
# ["6.1.0-Beta"]=Vtiger%20CRM%206.1.0%20Beta/Core%20Product/vtigercrm-6.1.0-ea.tar.gz
# ["6.0.0-RC"]=vtiger%20CRM%206.0%20RC/Core%20Product/vtigercrm-6.0.0rc.tar.gz
# ["6.0.0-Beta"]=vtiger%20CRM%206.0%20Beta/Core%20Product/vtigercrm-6.0Beta.tar.gz
# ["5.4.0"]=vtiger%20CRM%205.4.0/Core%20Product/vtigercrm-5.4.0.tar.gz
# ["5.4.0-RC"]=vtiger%20CRM%205.4.0%20RC/Core%20Product/vtigercrm-5.4.0-RC.tar.gz
# ["5.3.0"]=vtiger%20CRM%205.3.0/Core%20Product/vtigercrm-5.3.0.tar.gz
# ["5.3.0-RC"]=vtiger%20CRM%205.3.0%20RC/Core%20Product/vtigercrm-5.3.0-RC.tar.gz
# ["5.2.1"]=vtiger%20CRM%205.2.1/Core%20Product/vtigercrm-5.2.1.tar.gz
# ["5.2.0"]=vtiger%20CRM%205.2.0/Core%20Product/vtigercrm-5.2.0.tar.gz
# ["5.2.0-RC"]=vtiger%20CRM%205.2.0%20RC/vtigercrm-5.2.0-RC.tar.gz
# ["5.2.0-VB2"]=vtiger%20CRM%205.2.0%20VB2/vtigercrm-5.2.0-vb2.tar.gz
# ["5.2.0-VB1"]=vtiger%20CRM%205.2.0%20VB1/vtigercrm-5.2.0-vb1.tar.gz
# ["5.1.0"]=vtiger%20CRM%205.1.0/Core%20Product/vtigercrm-5.1.0.tar.gz
