<?php
/**
 * This file creates a order and redirects to the checkout view
 *
 * @package Fjakkarin/NetsSample
 */

namespace Fjakkarin\NetsSample;

use Dotenv\Dotenv;

require_once __DIR__ . './../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . './../');
$dotenv->load();

/**
 * This simulates a order load
 */
$payload = file_get_contents('orders/payload.json');
assert(json_decode($payload) && json_last_error() == JSON_ERROR_NONE);

$ch = curl_init($_ENV["NETS_URL"]);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
	'Content-Type: application/json',
	'Accept: application/json',
	'Authorization: ' . $_ENV["NETS_SECRET_KEY"]));
$result = curl_exec($ch);

/**
 * Redirect to the checkout view.
 */
header('Location: /checkout.php?paymentId=' . json_decode($result)->paymentId, 301);
die();
