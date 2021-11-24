<?php
/**
 * This file renders the default view and loads the dependencies.
 *
 * @package Fjakkarin/NetsSample
 */

namespace Fjakkarin\NetsSample;

use Dotenv\Dotenv;
use Fjakkarin\NetsSample\Inc\Template;

require_once __DIR__ . './../vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__ . './../');
$dotenv->load();

Template::view('../views/checkout.html',
	[
		'checkout_token' => $_ENV['NETS_CHECKOUT_KEY']
	]
);
