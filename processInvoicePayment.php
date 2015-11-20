<?php
require_once('code/setup.php');

global $conn;
global $config;

$emailMessage = "
Payment for %s %s\n\n
Company: %s\n
Name: %s %s\n\n

Address\n
%s\n
%s, %s %s\n\n

Phone: %s\n
Email: %s\n

Amount Charged: %s\n
Success: %s\n
";


$query = "SELECT * FROM recurring_plan WHERE id=%s";
$query = sprintf($query, mysql_real_escape_string($_REQUEST['PlanId']));

$dbResult = mysql_query($query);
$plan = mysql_fetch_assoc($dbResult);


\Stripe\Stripe::setApiKey($config['stripeSecretKey']);
$stripeToken = $_POST['StripeToken'];

$success = true;
$errorMessage = '';

try {
  \Stripe\Charge::create([
    'amount' => intval($_REQUEST['Amount'] * 100),
    'currency' => 'usd',
    'source' => $stripeToken,
    'description' => $_REQUEST['InvoiceNumber'],
  ]);
} catch (Exception $e) {
  $success = false;
  $errorMessage = $e->getMessage();
}

if (!$success) {
  startPage('payment.twig', ['plan' => $plan, 'errorMessage' => $errorMessage, 'postedData' => $_REQUEST]);
  exit;
}

$emailMessage = sprintf($emailMessage,
  $plan['name'], $_REQUEST['InvoiceNumber'], $_REQUEST['CompanyName'], $_REQUEST['FirstName'], $_REQUEST['LastName'], $_REQUEST['Address1'],
  $_REQUEST['City'], $_REQUEST['State'], $_REQUEST['PostalCode'], $_REQUEST['Phone'], $_REQUEST['Email'], $_REQUEST['Amount'], $success ? 'yes' : 'no');

$subject = 'Invoice Paid';
if ($_SERVER['ENVIRONMENT_NAME'] == 'stage')
{
  $subject .= ' - TESTING ENVIRONMENT NOT REAL CUSTOMER';
}

mail('billing@builderprofessional.com', $subject, $emailMessage, "From: billing@builderprofessional.com\r\n");
startPage('processPayment.twig', ['plan' => $plan]);