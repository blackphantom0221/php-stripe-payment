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

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationCartalystStripeCreateTables extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cards', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('stripe_id')->unique();
			$table->integer('last_four');
			$table->integer('exp_month');
			$table->integer('exp_year');
			$table->boolean('default')->default(0);
			$table->timestamps();
		});

		Schema::create('payments', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('stripe_id')->unique();
			$table->string('description')->nullable();
			$table->float('amount');
			$table->boolean('captured')->default(0);
			$table->boolean('refunded')->default(0);
			$table->timestamps();
		});

		Schema::create('refunds', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('payment_id');
			$table->string('transaction_id');
			$table->float('amount');
			$table->timestamps();
		});

		Schema::create('subscriptions', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->string('stripe_id')->unique();
			$table->string('plan', 25)->nullable();
			$table->boolean('active')->default(0);
			$table->timestamps();
			$table->timestamp('ends_at')->nullable();
			$table->timestamp('ended_at')->nullable();
			$table->timestamp('canceled_at')->nullable();
			$table->timestamp('trial_ends_at')->nullable();
		});

		Schema::table('users', function(Blueprint $table)
		{
			$table->string('stripe_id')->nullable()->unique();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		$tables = [
			'cards',
			'payments',
			'refunds',
			'subscriptions',
		];

		foreach ($tables as $table)
		{
			Schema::drop($table);
		}

		Schema::table('users', function(Blueprint $table)
		{
			$table->dropColumn('stripe_id');
		});
	}

}
