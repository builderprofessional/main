<?php
require_once('config.php');
require_once('twigSetup.php');

global $config;

$dbhost = $config['database']['dbhost'];
$dbname = $config['database']['dbname'];
$dbuser = $config['database']['dbuser'];
$dbpass = $config['database']['dbpass'];

$conn = mysql_connect($dbhost, $dbuser, $dbpass);
mysql_select_db($dbname, $conn);

global $conn;

function startPage($template, $extra = [])
{
  global $twig;
  global $config;

  echo $twig->render($template, ['config' => $config, 'extra' => $extra]);
}