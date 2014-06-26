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

	'all' => [

		'httpMethod'     => 'GET',
		'uri'            => '/v1/application_fees',
		'summary'        => 'Returns details about all application fees that your account has collected.',
		'responseClass'  => 'Cartalyst\Stripe\Api\Response',
		'parameters'     => [

			'charge' => [
				'description' => 'The charge unique identifier.',
				'location'    => 'query',
				'type'        => 'string',
				'required'    => false,
			],

			'created' => [
				'description' => 'A filter on the list based on the object created field. The value can be a string with an integer Unix timestamp, or it can be a dictionary.',
				'location'    => 'query',
				'type'        => ['string', 'array'],
				'required'    => false,
			],

			'ending_before' => [
				'description' => 'A cursor to be used in pagination.',
				'location'    => 'query',
				'type'        => 'string',
				'required'    => false,
			],

			'limit' => [
				'description' => 'A limit on the number of objects to be returned. Limit can range between 1 and 100 items.',
				'location'    => 'query',
				'type'        => 'integer',
				'min'         => 1,
				'max'         => 100,
				'required'    => false,
			],

			'starting_after' => [
				'description' => 'A cursor to be used in pagination.',
				'location'    => 'query',
				'type'        => 'string',
				'required'    => false,
			],

			'expand' => [
				'description' => 'Allows to expand properties.',
				'location'    => 'query',
				'type'        => 'array',
				'required'    => false,
			],

			'include' => [
				'description' => 'Allow to include some additional properties',
				'location'    => 'query',
				'type'        => 'array',
				'required'    => false,
			],

		],

	],

   'find' => [

		'httpMethod'     => 'GET',
		'uri'            => '/v1/application_fees/{id}',
		'summary'        => 'Retrieves the details about an application fee that your account has collected.',
		'responseClass'  => 'Cartalyst\Stripe\Api\Response',
		'parameters'     => [

			'id' => [
				'description' => 'The application fee unique identifier.',
				'location'    => 'uri',
				'type'        => 'string',
				'required'    => true,
			],

			'expand' => [
				'description' => 'Allows to expand properties.',
				'location'    => 'query',
				'type'        => 'array',
				'required'    => false,
			],

		],

	],

	'refund' => [

		'httpMethod'     => 'POST',
		'uri'            => '/v1/application_fees/{id}/refund',
		'summary'        => 'Refunds an application fee that has previously been collected but not yet refunded.',
		'responseClass'  => 'Cartalyst\Stripe\Api\Response',
		'parameters'     => [

			'id' => [
				'description' => 'The application fee unique identifier.',
				'location'    => 'uri',
				'type'        => 'string',
				'required'    => true,
			],

			'amount' => [
				'description' => 'A positive integer in the smallest currency unit.',
				'location'    => 'query',
				'type'        => 'integer',
				'required'    => false,
			],

			'expand' => [
				'description' => 'Allows to expand properties.',
				'location'    => 'query',
				'type'        => 'array',
				'required'    => false,
			],

		],

	],

];
