<?php
/**
 * Part of the Stripe package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the license.txt file.
 *
 * @package    Stripe
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2014, Cartalyst LLC
 * @link       http://cartalyst.com
 */

return [
	'name'        => 'Stripe',
	'apiVersion'  => '2014-05-19',
	'baseUrl'     => 'https://api.stripe.com',
	'description' => 'Stripe is a payment system',
	'operations'  => [],
	'models' => [
		'Response' => [
			'type' => 'object',
			'additionalProperties' => [
				'location' => 'json'
			],
		],
	],
];
