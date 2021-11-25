<?php
/**
 * This file creates a order and redirects to the checkout view
 *
 * @package Fjakkarin/NetsSample
 */

namespace Fjakkarin\NetsSample;

use Dotenv\Dotenv;
use Fjakkarin\NetsSample\inc\FormData;
use JetBrains\PhpStorm\Pure;
use stdClass;

require_once __DIR__ . './../vendor/autoload.php';

$dotenv = Dotenv::createImmutable( __DIR__ . './../' );
$dotenv->load();

if ( $_SERVER["REQUEST_METHOD"] == "POST" ) {
	$data   = validateFormData();
	$order  = createOrder( $data );
	$result = postOrder( $order );

	/**
	 * Redirect to the checkout view.
	 */
	header( 'Location: /checkout.php?paymentId=' . json_decode( $result )->paymentId, 301 );
	die();
}

/**
 * This function returns the FormData object.
 *
 * @return FormData
 */
function validateFormData(): FormData {
	$r = new FormData();
	$r->set_email( validateInputData( $_POST["email"] ) )
	  ->set_first_name( validateInputData( $_POST["firstName"] ) )
	  ->set_last_name( validateInputData( $_POST["lastName"] ) )
	  ->set_phone( validateInputData( $_POST["phone"] ) )
	  ->set_post_code( validateInputData( $_POST["postCode"] ) );

	return $r;
}

/**
 * This function creates a payload based on the FormData provided.
 *
 * @param FormData $data
 *
 * @return bool|string
 */
function createOrder( FormData $data ): bool|string {
	$payload = [
		"checkout" => [
			"integrationType"             => "EmbeddedCheckout",
			"merchantHandlesConsumerData" => true,
			"charge"                      => true,
			"url"                         => "http://localhost:8000/checkout.html",
			"termsUrl"                    => "http://localhost:8000/terms.html",
			"consumer"                    => [
				"email"           => $data->get_email(),
				"shippingAddress" => [
					"postalCode" => "0956"
				],
				"phoneNumber"     => [
					"prefix" => "+46",
					"number" => "0123456789"
				],
				"privatePerson"   => [
					"firstName" => "john",
					"lastName"  => "Doe"
				]
			]
		],
		"order"    => [
			"items"     => [
				[
					"reference"        => "ref42",
					"name"             => "Demo product",
					"quantity"         => 2,
					"unit"             => "hours",
					"unitPrice"        => 80000,
					"grossTotalAmount" => 160000,
					"netTotalAmount"   => 160000
				]
			],
			"amount"    => 160000,
			"currency"  => "SEK",
			"reference" => "Demo Order"
		]
	];

	return json_encode( $payload );
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

/**
 * @param $data
 *
 * @return string
 */
function validateInputData( $data ): string {
	$data = trim( $data );
	$data = stripslashes( $data );
	$data = htmlspecialchars( $data );

	return $data;
}
