<?php
require_once('code/setup.php');
global $config;

if (!isset($_REQUEST['token']) || $_REQUEST['token'] != '1123581321')
{
  // Make sure this submission is actually coming from our form, not just some asshole spamming us.
  die('Invalid submission');
}

$template = "
Name: %s \n
Email: %s \n
Phone: %s \n\n

Best Time to Contact: %s \n\n

Message: %s \n\n
";

$message = sprintf($template, $_REQUEST['name'], $_REQUEST['email'], $_REQUEST['phone'], $_REQUEST['contactTime'], $_REQUEST['message']);
mail($config['contactRequestEmail'], 'Online Form Submission', $message, "From: " . $_REQUEST['email'] . "\r\n");
