<?php
require_once('code/setup.php');
if (!isset($_REQUEST['StripeToken'])) {
//    die();
}

global $conn;
global $config;

$emailMessage = "
Signup for %s\n\n
Company: %s\n
Name: %s %s\n\n

Address\n
%s\n
%s, %s %s\n\n

Phone: %s\n
Email: %s\n

Stripe Customer ID: %s\n
Initial Charge ID: %s\n
";


$query = "SELECT * FROM recurring_plan WHERE id=%s";
$query = sprintf($query, mysql_real_escape_string($_REQUEST['PlanId']));

$dbResult = mysql_query($query);
$plan = mysql_fetch_assoc($dbResult);


\Stripe\Stripe::setApiKey($config['stripeSecretKey']);
$stripeToken = $_POST['StripeToken'];

$stripeCustomerId = "";
$stripeCustomerSuccess = true;

try {
  $customer = \Stripe\Customer::Create([
    'source' => $stripeToken,
    'plan' => $plan['stripe_plan_id'],
    'email' => $_REQUEST['Email'],
    'description' => $_REQUEST['CompanyName'],
  ]);

  $stripeCustomerId = $customer->id;
} catch (Exception $e) {
  $stripeCustomerId = "Error: " . $e->getMessage();
  $stripeCustomerSuccess = false;
}

if (!$stripeCustomerSuccess) {
  startPage('payment.twig', ['plan' => $plan, 'errorMessage' => $stripeCustomerId, 'postedData' => $_REQUEST]);
  exit;
}

$initialChargeNote = "";
if ($stripeCustomerSuccess && $plan['one_time_fee'] > 0)
{
  $initialChargeNote = "Ran Charge for " . $plan['one_time_total'];

  try {
    \Stripe\Charge::create([
      'amount' => intval($plan['one_time_total'] * 100),
      'currency' => 'usd',
      'customer' => $customer->id,
    ]);
  } catch (Exception $e) {
    $initialChargeNote = "Error: " . $e->getMessage();
  }
}

$emailMessage = sprintf($emailMessage,
  $plan['name'], $_REQUEST['CompanyName'], $_REQUEST['FirstName'], $_REQUEST['LastName'], $_REQUEST['Address1'], $_REQUEST['City'],
  $_REQUEST['State'], $_REQUEST['PostalCode'], $_REQUEST['Phone'], $_REQUEST['Email'], $stripeCustomerId, $initialChargeNote);

$subject = 'Recurring Signup';
if ($_SERVER['ENVIRONMENT_NAME'] == 'dev')
{
  $subject .= ' - TESTING ENVIRONMENT NOT REAL CUSTOMER';
}

mail('billing@builderprofessional.com', $subject, $emailMessage, "From: billing@builderprofessional.com\r\n");
startPage('processPayment.twig', ['plan' => $plan]);