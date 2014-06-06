<?php namespace Cartalyst\Stripe\Charge;
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

use Illuminate\Database\Eloquent\Model;

class IlluminateRefund extends Model {

	/**
	 * {@inheritDoc}
	 */
	public $table = 'refunds';

	/**
	 * Returns the charge associated to this refund.
	 *
	 * @return \Carbon\Stripe\Charge\IlluminateCharge
	 */
	public function charge()
	{
		return $this->belongsTo('Cartalyst\Stripe\Charge\IlluminateCharge');
	}

	/**
	 * Get mutator for the "amount" attribute.
	 *
	 * @param  int  $amount
	 * @return float
	 */
	public function getAmountAttribute($amount)
	{
		return number_format($amount / 100, 2);
	}

}
