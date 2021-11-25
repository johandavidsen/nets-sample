<?php
/**
 * This file creates a order and redirects to the checkout view
 *
 * @package Fjakkarin/NetsSample
 */

namespace Fjakkarin\NetsSample;

require_once __DIR__ . './../vendor/autoload.php';

use Dotenv\Dotenv;
use Rakit\Validation\Validator;

$dotenv = Dotenv::createImmutable( __DIR__ . './../' );
$dotenv->load();

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
	$validator = new Validator;

	$validation = $validator->validate( $_POST + $_FILES, [
		'package'   => 'required',
		'firstName' => 'required',
		'lastName'  => 'required',
		'email'     => 'required|email',
		'phone-code'=> 'required|digits:3',
		'phone'     => 'required|digits:6',
		'postCode'  => 'required|digits:4|in:3900,3905,3910,3911,3912,3913,3915,3919,3920,3921,3922,3923,3924,3930,3932,3940,3950,3951,3952,3953,3955,3961,3962,3964,3970,3971,3980,3984,3985,3992',
	] );

	if ( $validation->fails() ) {
		// handling errors
		$errors = $validation->errors();
		echo "<pre>";
		print_r( $errors->firstOfAll() );
		echo "</pre>";
		exit;
	} else {

		$order  = createOrder( $validation->getValidData() );
		$result = postOrder( $order );

		/**
		 * Redirect to the checkout view.
		 */
		header( 'Location: /checkout.php?paymentId=' . json_decode( $result )->paymentId, 301 );
		die();
	}
}

/**
 * This function creates a payload based on the FormData provided.
 *
 * @param array $data
 *
 * @return bool|string
 */
function createOrder( array $data ): bool|string {
	$package = getPackage($data["package"]);

	$payload = [
		"checkout" => [
			"integrationType"             => "EmbeddedCheckout",
			"merchantHandlesConsumerData" => true,
			"charge"                      => true,
			"url"                         => "http://localhost:8000/checkout.html",
			"termsUrl"                    => "http://localhost:8000/terms.html",
			"consumer"                    => [
				"email"           => $data["email"],
				"shippingAddress" => [
					"postalCode" => "0956"
				],
				"phoneNumber"     => [
					"prefix" => "+" . $data["phone-code"],
					"number" => $data["phone"]
				],
				"privatePerson"   => [
					"firstName" => $data["firstName"],
					"lastName"  => $data["lastName"]
				]
			]
		],
		"order"    => [
			"items"     => [
				[
					"reference"        => uniqid("fjakkarin_"),
					"name"             => $package['name'],
					"quantity"         => 1,
					"unit"             => "hours",
					"unitPrice"        => $package['unitPrice'],
					"grossTotalAmount" => $package['unitPrice'],
					"netTotalAmount"   => $package['unitPrice']
				]
			],
			"amount"    => $package['unitPrice'],
			"currency"  => "EUR",
			"reference" => $package['name']
		]
	];

	return json_encode( $payload );
}

/**
 * @param string $package
 *
 * @return array
 */
function getPackage( string $package ): array {
	if (strcasecmp($package, 'small') === 0) {
		return [
			'name' => "Small Package (100 €)",
			'unitPrice' => 10000
		];
	}

	if (strcasecmp($package, 'medium') === 0) {
		return [
			'name' => "Medium Package (200 €)",
			'unitPrice' => 20000
		];
	}

	return [
		'name' => "Large Package (300 €)",
		'unitPrice' => 30000
	];
}

/**
 * This simulates a order load
 */
function postOrder( $payload ): bool|string {
	$ch = curl_init( $_ENV["NETS_URL"] );
	curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'POST' );
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Accept: application/json',
		'Authorization: ' . $_ENV["NETS_SECRET_KEY"]
	) );

	return curl_exec( $ch );
}

