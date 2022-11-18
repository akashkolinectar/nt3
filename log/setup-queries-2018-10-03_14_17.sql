CREATE TABLE `ntlogicalinterface` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `virtualmachine_id` INT(11) DEFAULT 0, INDEX `virtualmachine_id` (`virtualmachine_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkvirtualdevicetovolume` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `volume_id` INT(11) DEFAULT 0, INDEX `volume_id` (`volume_id`), `virtualdevice_id` INT(11) DEFAULT 0, INDEX `virtualdevice_id` (`virtualdevice_id`), `size_used` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntchange` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `status` ENUM('approved','assigned','closed','implemented','monitored','new','notapproved','plannedscheduled','rejected','validated') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'new', `reason` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `requestor_id` INT(11) DEFAULT 0, INDEX `requestor_id` (`requestor_id`), `creation_date` DATETIME, `impact` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `supervisor_group_id` INT(11) DEFAULT 0, INDEX `supervisor_group_id` (`supervisor_group_id`), `supervisor_id` INT(11) DEFAULT 0, INDEX `supervisor_id` (`supervisor_id`), `manager_group_id` INT(11) DEFAULT 0, INDEX `manager_group_id` (`manager_group_id`), `manager_id` INT(11) DEFAULT 0, INDEX `manager_id` (`manager_id`), `outage` ENUM('no','yes') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no', `fallback` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `parent_id` INT(11) DEFAULT 0, INDEX `parent_id` (`parent_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntchange_routine` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntchange_approved` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `approval_date` DATETIME, `approval_comment` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntchange_normal` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `acceptance_date` DATETIME, `acceptance_comment` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntchange_emergency` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntticket_incident` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `status` ENUM('assigned','closed','escalated_tto','escalated_ttr','new','pending','resolved') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'new', `impact` ENUM('1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1', `priority` ENUM('1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '4', `urgency` ENUM('1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '4', `origin` ENUM('mail','monitoring','phone','portal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'phone', `service_id` INT(11) DEFAULT 0, INDEX `service_id` (`service_id`), `servicesubcategory_id` INT(11) DEFAULT 0, INDEX `servicesubcategory_id` (`servicesubcategory_id`), `escalation_flag` ENUM('no','yes') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no', `escalation_reason` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `assignment_date` DATETIME, `resolution_date` DATETIME, `last_pending_date` DATETIME, `cumulatedpending_timespent` INT(11) UNSIGNED, `cumulatedpending_started` DATETIME, `cumulatedpending_laststart` DATETIME, `cumulatedpending_stopped` DATETIME, `tto_timespent` INT(11) UNSIGNED, `tto_started` DATETIME, `tto_laststart` DATETIME, `tto_stopped` DATETIME, `tto_75_deadline` DATETIME, `tto_75_passed` TINYINT(1) UNSIGNED, `tto_75_triggered` TINYINT(1), `tto_75_overrun` INT(11) UNSIGNED, `tto_100_deadline` DATETIME, `tto_100_passed` TINYINT(1) UNSIGNED, `tto_100_triggered` TINYINT(1), `tto_100_overrun` INT(11) UNSIGNED, `ttr_timespent` INT(11) UNSIGNED, `ttr_started` DATETIME, `ttr_laststart` DATETIME, `ttr_stopped` DATETIME, `ttr_75_deadline` DATETIME, `ttr_75_passed` TINYINT(1) UNSIGNED, `ttr_75_triggered` TINYINT(1), `ttr_75_overrun` INT(11) UNSIGNED, `ttr_100_deadline` DATETIME, `ttr_100_passed` TINYINT(1) UNSIGNED, `ttr_100_triggered` TINYINT(1), `ttr_100_overrun` INT(11) UNSIGNED, `time_spent` INT(11) UNSIGNED, `resolution_code` ENUM('assistance','bug fixed','hardware repair','other','software patch','system update','training') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'assistance', `solution` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `pending_reason` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `parent_incident_id` INT(11) DEFAULT 0, INDEX `parent_incident_id` (`parent_incident_id`), `parent_problem_id` INT(11) DEFAULT 0, INDEX `parent_problem_id` (`parent_problem_id`), `parent_change_id` INT(11) DEFAULT 0, INDEX `parent_change_id` (`parent_change_id`), `public_log` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `public_log_index` BLOB, `user_satisfaction` ENUM('1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1', `user_commment` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntknownerror` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `name` (`name` (95)), `cust_id` INT(11) DEFAULT 0, INDEX `cust_id` (`cust_id`), `problem_id` INT(11) DEFAULT 0, INDEX `problem_id` (`problem_id`), `symptom` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `rootcause` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `workaround` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `solution` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `error_code` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `domain` ENUM('Application','Desktop','Network','Server') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Application', `vendor` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `model` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `version` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkerrortofunctionalci` (`link_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `functionalci_id` INT(11) DEFAULT 0, INDEX `functionalci_id` (`functionalci_id`), `error_id` INT(11) DEFAULT 0, INDEX `error_id` (`error_id`), `dummy` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkdocumenttoerror` (`link_id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `document_id` INT(11) DEFAULT 0, INDEX `document_id` (`document_id`), `error_id` INT(11) DEFAULT 0, INDEX `error_id` (`error_id`), `link_type` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `link_type` (`link_type` (95))) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntfaq` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `title` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `title` (`title` (95)), `summary` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `description` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `category_id` INT(11) DEFAULT 0, INDEX `category_id` (`category_id`), `error_code` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `key_words` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntfaqcategory` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `nam` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntticket_problem` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `status` ENUM('assigned','closed','new','resolved') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'new', `service_id` INT(11) DEFAULT 0, INDEX `service_id` (`service_id`), `servicesubcategory_id` INT(11) DEFAULT 0, INDEX `servicesubcategory_id` (`servicesubcategory_id`), `product` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `impact` ENUM('1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1', `urgency` ENUM('1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1', `priority` ENUM('1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1', `related_change_id` INT(11) DEFAULT 0, INDEX `related_change_id` (`related_change_id`), `assignment_date` DATETIME, `resolution_date` DATETIME) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntticket_request` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `status` ENUM('approved','assigned','closed','escalated_tto','escalated_ttr','new','pending','rejected','resolved','waiting_for_approval') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'new', `request_type` ENUM('service_request') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'service_request', `impact` ENUM('1','2','3') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1', `priority` ENUM('1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '4', `urgency` ENUM('1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '4', `origin` ENUM('mail','phone','portal') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'phone', `approver_id` INT(11) DEFAULT 0, INDEX `approver_id` (`approver_id`), `service_id` INT(11) DEFAULT 0, INDEX `service_id` (`service_id`), `servicesubcategory_id` INT(11) DEFAULT 0, INDEX `servicesubcategory_id` (`servicesubcategory_id`), `escalation_flag` ENUM('no','yes') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'no', `escalation_reason` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `assignment_date` DATETIME, `resolution_date` DATETIME, `last_pending_date` DATETIME, `cumulatedpending_timespent` INT(11) UNSIGNED, `cumulatedpending_started` DATETIME, `cumulatedpending_laststart` DATETIME, `cumulatedpending_stopped` DATETIME, `tto_timespent` INT(11) UNSIGNED, `tto_started` DATETIME, `tto_laststart` DATETIME, `tto_stopped` DATETIME, `tto_75_deadline` DATETIME, `tto_75_passed` TINYINT(1) UNSIGNED, `tto_75_triggered` TINYINT(1), `tto_75_overrun` INT(11) UNSIGNED, `tto_100_deadline` DATETIME, `tto_100_passed` TINYINT(1) UNSIGNED, `tto_100_triggered` TINYINT(1), `tto_100_overrun` INT(11) UNSIGNED, `ttr_timespent` INT(11) UNSIGNED, `ttr_started` DATETIME, `ttr_laststart` DATETIME, `ttr_stopped` DATETIME, `ttr_75_deadline` DATETIME, `ttr_75_passed` TINYINT(1) UNSIGNED, `ttr_75_triggered` TINYINT(1), `ttr_75_overrun` INT(11) UNSIGNED, `ttr_100_deadline` DATETIME, `ttr_100_passed` TINYINT(1) UNSIGNED, `ttr_100_triggered` TINYINT(1), `ttr_100_overrun` INT(11) UNSIGNED, `time_spent` INT(11) UNSIGNED, `resolution_code` ENUM('assistance','bug fixed','hardware repair','other','software patch','system update','training') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'assistance', `solution` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `pending_reason` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `parent_request_id` INT(11) DEFAULT 0, INDEX `parent_request_id` (`parent_request_id`), `parent_incident_id` INT(11) DEFAULT 0, INDEX `parent_incident_id` (`parent_incident_id`), `parent_problem_id` INT(11) DEFAULT 0, INDEX `parent_problem_id` (`parent_problem_id`), `parent_change_id` INT(11) DEFAULT 0, INDEX `parent_change_id` (`parent_change_id`), `public_log` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `public_log_index` BLOB, `user_satisfaction` ENUM('1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '1', `user_commment` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntcontracttype` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntcontract` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `name` (`name` (95)), `org_id` INT(11) DEFAULT 0, INDEX `org_id` (`org_id`), `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `start_date` DATE, `end_date` DATE, `cost` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `cost_currency` ENUM('dollars','euros') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `contracttype_id` INT(11) DEFAULT 0, INDEX `contracttype_id` (`contracttype_id`), `billing_frequency` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `cost_unit` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `provider_id` INT(11) DEFAULT 0, INDEX `provider_id` (`provider_id`), `status` ENUM('implementation','obsolete','production') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `finalclass` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Contract', INDEX `finalclass` (`finalclass` (95))) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntcustomercontract` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntprovidercontract` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `sla` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', `coverage` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkcontacttocontract` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `contract_id` INT(11) DEFAULT 0, INDEX `contract_id` (`contract_id`), `contact_id` INT(11) DEFAULT 0, INDEX `contact_id` (`contact_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkcontracttodocument` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `contract_id` INT(11) DEFAULT 0, INDEX `contract_id` (`contract_id`), `document_id` INT(11) DEFAULT 0, INDEX `document_id` (`document_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkfunctionalcnt3rovidercontract` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `providercontract_id` INT(11) DEFAULT 0, INDEX `providercontract_id` (`providercontract_id`), `functionalci_id` INT(11) DEFAULT 0, INDEX `functionalci_id` (`functionalci_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntservicefamily` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `name` (`name` (95)), `icon_data` LONGBLOB, `icon_mimetype` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `icon_filename` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntservice` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `name` (`name` (95)), `org_id` INT(11) DEFAULT 0, INDEX `org_id` (`org_id`), `servicefamily_id` INT(11) DEFAULT 0, INDEX `servicefamily_id` (`servicefamily_id`), `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `status` ENUM('implementation','obsolete','production') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `icon_data` LONGBLOB, `icon_mimetype` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `icon_filename` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkdocumenttoservice` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `service_id` INT(11) DEFAULT 0, INDEX `service_id` (`service_id`), `document_id` INT(11) DEFAULT 0, INDEX `document_id` (`document_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkcontacttoservice` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `service_id` INT(11) DEFAULT 0, INDEX `service_id` (`service_id`), `contact_id` INT(11) DEFAULT 0, INDEX `contact_id` (`contact_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntservicesubcategory` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `name` (`name` (95)), `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `service_id` INT(11) DEFAULT 0, INDEX `service_id` (`service_id`), `request_type` ENUM('incident','service_request') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'incident', `status` ENUM('implementation','obsolete','production') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntsla` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `name` (`name` (95)), `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `org_id` INT(11) DEFAULT 0, INDEX `org_id` (`org_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntslt` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `name` (`name` (95)), `priority` ENUM('1','2','3','4') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `request_type` ENUM('incident','service_request') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `metric` ENUM('tto','ttr') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci, `value` INT(11), `unit` ENUM('hours','minutes') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkslatoslt` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `sla_id` INT(11) DEFAULT 0, INDEX `sla_id` (`sla_id`), `slt_id` INT(11) DEFAULT 0, INDEX `slt_id` (`slt_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkcustomercontracttoservice` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `customercontract_id` INT(11) DEFAULT 0, INDEX `customercontract_id` (`customercontract_id`), `service_id` INT(11) DEFAULT 0, INDEX `service_id` (`service_id`), `sla_id` INT(11) DEFAULT 0, INDEX `sla_id` (`sla_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkprovidercontracttoservice` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `service_id` INT(11) DEFAULT 0, INDEX `service_id` (`service_id`), `providercontract_id` INT(11) DEFAULT 0, INDEX `providercontract_id` (`providercontract_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkfunctionalcitoservice` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `service_id` INT(11) DEFAULT 0, INDEX `service_id` (`service_id`), `functionalci_id` INT(11) DEFAULT 0, INDEX `functionalci_id` (`functionalci_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntdeliverymodel` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `name` (`name` (95)), `org_id` INT(11) DEFAULT 0, INDEX `org_id` (`org_id`), `description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntlnkdeliverymodeltocontact` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `deliverymodel_id` INT(11) DEFAULT 0, INDEX `deliverymodel_id` (`deliverymodel_id`), `contact_id` INT(11) DEFAULT 0, INDEX `contact_id` (`contact_id`), `role_id` INT(11) DEFAULT 0, INDEX `role_id` (`role_id`)) ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntpriv_urp_profiles` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '', INDEX `name` (`name` (95)), `description` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntpriv_urp_userprofile` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `userid` INT(11) DEFAULT 0, INDEX `userid` (`userid`), `profileid` INT(11) DEFAULT 0, INDEX `profileid` (`profileid`), `description` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
CREATE TABLE `ntpriv_urp_userorg` (`id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY, `userid` INT(11) DEFAULT 0, INDEX `userid` (`userid`), `allowed_org_id` INT(11) DEFAULT 0, INDEX `allowed_org_id` (`allowed_org_id`), `reason` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '') ENGINE = innodb CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
