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

		'httpMethod' => 'GET',
		'uri'        => '/v1/coupons/{id}',
		'summary'    => 'Returns all the existing coupons.',
		'parameters' => [

			'limit' => [
				'description' => 'Limit of how many coupons are retrieved.',
				'location'    => 'query',
				'type'        => 'integer',
				'min'         => 1,
				'max'         => 100,
				'required'    => false,
			],

			'starting_after' => [
				'description' => 'A cursor to be used in the pagination.',
				'location'    => 'query',
				'type'        => 'string',
				'required'    => false,
			],

			'ending_before' => [
				'description' => 'A cursor to be used in the pagination.',
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
				'description' => 'Allows to include additional properties.',
				'location'    => 'query',
				'type'        => 'array',
				'required'    => false,
			],

		],

	],

	'find' => [

		'httpMethod' => 'GET',
		'uri'        => '/v1/coupons/{id}',
		'summary'    => 'Returns an existing coupon.',
		'parameters' => [

			'id' => [
				'description' => 'Coupon unique identifier.',
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

	'create' => [

		'httpMethod' => 'POST',
		'uri'        => '/v1/coupons',
		'summary'    => 'Creates a new coupon.',
		'parameters' => [

			'id' => [
				'description' => 'Unique string to identify the coupon (you can specify none and it will be auto-generated).',
				'location'    => 'query',
				'type'        => 'string',
				'required'    => false,
			],

			'duration' => [
				'description' => 'Specifies how long the discount will be in effect (can be "forever", "once" or "repeating").',
				'location'    => 'query',
				'type'        => 'string',
				'required'    => true,
				'enum'        => ['forever', 'once', 'repeating')
			],

			'amount_off' => [
				'description' => 'A positive integer representing the amount to subtract from an invoice total (required if "percent_off" is not passed).',
				'location'    => 'query',
				'type'        => 'integer',
				'required'    => false,
			],

			'currency' => [
				'description' => 'Currency of the amount_off parameter (required if "amount_off" is passed).',
				'location'    => 'query',
				'type'        => 'string',
				'required'    => false,
			],

			'duration_in_months' => [
				'description' => 'If "duration" is repeating, a positive integer that specifies the number of months the discount will be in effect.',
				'location'    => 'query',
				'type'        => 'integer',
				'required'    => false,
			],

			'max_redemptions' => [
				'description' => 'A positive integer specifying the number of times the coupon can be redeemed before it\'s no longer valid.',
				'location'    => 'query',
				'type'        => 'integer',
				'required'    => false,
			],

			'percent_off' => [
				'description' => 'A positive integer between 1 and 100 that represents the discount the coupon will apply (required if amount_off is not passed).',
				'location'    => 'query',
				'type'        => 'integer',
				'required'    => false,
			],

			'redeem_by' => [
				'description' => 'UTC timestamp specifying the last time at which the coupon can be redeemed.',
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

	'delete' => [

		'httpMethod' => 'DELETE',
		'uri'        => '/v1/coupons/{id}',
		'summary'    => 'Deletes an existing coupon.',
		'parameters' => [

			'id' => [
				'description' => 'Coupon unique identifier.',
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

];
