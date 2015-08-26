<?php

# Copyright (C) 2010-2015 Magima Co Ltd. All rights reserved.

# @description

# @file           metaData.php
# @author         0294
# @created date   2015-01-30 15:52
# @version        1.0.1

$config_content = array(
    "host_type", 
    "host_ip", 
    "host_user", 
    "host_password", 
    "ftp_user", 
    "workstation",
    "datastore",
);

$host_type = array(
    "sourceHost",
    "storageHost",
    "workstation",
);

$backupType = array(
    'linux_file',
    'linux_mysqldb',
    'win_file',
);

$os_type = array(
    'ubuntu',
    'redhat',
    'centos',
    'windows server',
);

$app_type = array(
    'file',
    'database',
    //'directory',
);

$app_name = array(
    'mysqldb',
    'mongodb',
    'redis',
    'zip',
    'tar',
    'raw',
    'regular',
);

define("TMP_DIR", "~/workdir");
// configuration file path and content 
define("TASK_PATH", "../../conf/task.ini");
define("CONFIG_PATH", "../../conf/backup.ini");
define("SPEC_PATH", "../../conf/specification/host_spec.ini");
define("CREDENTIAL_PATH", "../../conf/credential.ini");
define("CONFIG_CONTENT", serialize($config_content));
define("BACKUP_TYPE", serialize($backupType));
define("HOST_TYPE", serialize($host_type));
define("OS_TYPE", serialize($os_type));
define("APP_TYPE", serialize($app_type));
define("APP_NAME", serialize($app_name));

//log
define("LOG_FILE_PATH", "/var/log/backup/");
define("LOG_SWITCH", 1);
define("LOG_MAX_LEN", 105000000);
define("WORK_DIR", "~/backup");
define("CREDENTIAL_TYPE0", "password");

// lifecycle time to run
define("LIFECYCLE_TIME", 86400);
define("AUTHENTICATION_PATH", '../../conf/authentication.ini');
