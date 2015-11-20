<?php
// Pull the base config from the file
$configFile = dirname(__FILE__) . '/../../config.' . $_SERVER['ENVIRONMENT_NAME'] . '.json';
$configFileContents = file_get_contents($configFile);
$config = json_decode($configFileContents, true);


// Add config entries here in code
$config['page_name'] = basename($_SERVER['PHP_SELF']);

global $config;