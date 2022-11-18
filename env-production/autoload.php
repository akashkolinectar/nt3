<?php
//
// File generated on 2018-10-03T14:16:23+0200
// Please do not edit manually
//
MetaModel::IncludeModule(MODULESROOT.'/core/main.php');
MetaModel::IncludeModule(MODULESROOT.'/authent-external/model.authent-external.php');
MetaModel::IncludeModule(MODULESROOT.'/authent-local/model.authent-local.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-attachments/model.nt3-attachments.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-attachments/main.attachments.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-backup/main.nt3-backup.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-backup/model.nt3-backup.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-config-mgmt/model.nt3-config-mgmt.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-config-mgmt/main.nt3-config-mgmt.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-config/model.nt3-config.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-datacenter-mgmt/model.nt3-datacenter-mgmt.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-endusers-devices/model.nt3-endusers-devices.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-hub-connector/menus.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-hub-connector/model.nt3-hub-connector.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-portal-base/portal/src/controllers/abstractcontroller.class.inc.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-portal-base/portal/src/controllers/brickcontroller.class.inc.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-portal-base/portal/src/entities/abstractbrick.class.inc.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-portal-base/portal/src/entities/portalbrick.class.inc.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-portal-base/portal/src/routers/abstractrouter.class.inc.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-portal/main.nt3-portal.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-profiles-itil/model.nt3-profiles-itil.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-sla-computation/main.nt3-sla-computation.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-storage-mgmt/model.nt3-storage-mgmt.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-tickets/main.nt3-tickets.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-tickets/model.nt3-tickets.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-virtualization-mgmt/model.nt3-virtualization-mgmt.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-welcome-itil/main.nt3-welcome-itil.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-welcome-itil/model.nt3-welcome-itil.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-bridge-virtualization-storage/model.nt3-bridge-virtualization-storage.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-change-mgmt-itil/model.nt3-change-mgmt-itil.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-incident-mgmt-itil/model.nt3-incident-mgmt-itil.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-knownerror-mgmt/model.nt3-knownerror-mgmt.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-problem-mgmt/model.nt3-problem-mgmt.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-request-mgmt-itil/model.nt3-request-mgmt-itil.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-request-mgmt-itil/main.nt3-request-mgmt-itil.php');
MetaModel::IncludeModule(MODULESROOT.'/nt3-service-mgmt/model.nt3-service-mgmt.php');
function GetModulesInfo()
{
$sCurrEnv = 'env-'.utils::GetCurrentEnvironment();
return array (
  'dictionaries' => 
  array (
    'root_dir' => '',
    'version' => '1.0',
  ),
  'core' => 
  array (
    'root_dir' => '',
    'version' => '1.0',
  ),
  'application' => 
  array (
    'root_dir' => '',
    'version' => '1.0',
  ),
  'authent-external' => 
  array (
    'root_dir' => $sCurrEnv.'/authent-external',
    'version' => '2.5.0',
  ),
  'authent-local' => 
  array (
    'root_dir' => $sCurrEnv.'/authent-local',
    'version' => '2.5.0',
  ),
  'nt3-attachments' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-attachments',
    'version' => '2.5.0',
  ),
  'nt3-backup' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-backup',
    'version' => '2.5.0',
  ),
  'nt3-config-mgmt' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-config-mgmt',
    'version' => '2.5.0',
  ),
  'nt3-config' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-config',
    'version' => '2.5.0',
  ),
  'nt3-datacenter-mgmt' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-datacenter-mgmt',
    'version' => '2.5.0',
  ),
  'nt3-endusers-devices' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-endusers-devices',
    'version' => '2.5.0',
  ),
  'nt3-hub-connector' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-hub-connector',
    'version' => '2.5.0',
  ),
  'nt3-portal-base' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-portal-base',
    'version' => '2.5.0',
  ),
  'nt3-portal' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-portal',
    'version' => '2.5.0',
  ),
  'nt3-profiles-itil' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-profiles-itil',
    'version' => '2.5.0',
  ),
  'nt3-sla-computation' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-sla-computation',
    'version' => '2.5.0',
  ),
  'nt3-storage-mgmt' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-storage-mgmt',
    'version' => '2.5.0',
  ),
  'nt3-tickets' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-tickets',
    'version' => '2.5.0',
  ),
  'nt3-virtualization-mgmt' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-virtualization-mgmt',
    'version' => '2.5.0',
  ),
  'nt3-welcome-itil' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-welcome-itil',
    'version' => '2.5.0',
  ),
  'nt3-bridge-virtualization-storage' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-bridge-virtualization-storage',
    'version' => '2.5.0',
  ),
  'nt3-change-mgmt-itil' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-change-mgmt-itil',
    'version' => '2.5.0',
  ),
  'nt3-incident-mgmt-itil' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-incident-mgmt-itil',
    'version' => '2.5.0',
  ),
  'nt3-knownerror-mgmt' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-knownerror-mgmt',
    'version' => '2.5.0',
  ),
  'nt3-problem-mgmt' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-problem-mgmt',
    'version' => '2.5.0',
  ),
  'nt3-request-mgmt-itil' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-request-mgmt-itil',
    'version' => '2.5.0',
  ),
  'nt3-service-mgmt' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-service-mgmt',
    'version' => '2.5.0',
  ),
  'nt3-full-itil' => 
  array (
    'root_dir' => $sCurrEnv.'/nt3-full-itil',
    'version' => '2.5.0',
  ),
);
}
