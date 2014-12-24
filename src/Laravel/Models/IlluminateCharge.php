<?php namespace Cartalyst\Stripe\Laravel\Models;
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

class IlluminateCharge extends IlluminateModel {

	/**
	 * {@inheritDoc}
	 */
	public $table = 'stripe_payments';

	/**
	 * {@inheritDoc}
	 */
	protected $fillable = [
		'paid',
		'amount',
		'failed',
		'captured',
		'currency',
		'refunded',
		'stripe_id',
		'created_at',
		'invoice_id',
		'description',
	];

	/**
	 * The Eloquent invoice model.
	 *
	 * @var string
	 */
	protected static $invoiceModel = 'Cartalyst\Stripe\Models\IlluminateInvoice';

	/**
	 * The Eloquent refund model.
	 *
	 * @var string
	 */
	protected static $refundModel = 'Cartalyst\Stripe\Models\IlluminateChargeRefund';

	/**
	 * Accessor for the "amount_refunded" attribute.
	 *
	 * @return float
	 */
	public function getAmountRefundedAttribute()
	{
		return $this->refunds()->sum('amount');
	}

	/**
	 * Accessor for the "captured" attribute.
	 *
	 * @param  string  $captured
	 * @return bool
	 */
	public function getCapturedAttribute($captured)
	{
		return (bool) $captured;
	}

	/**
	 * Accessor for the "failed" attribute.
	 *
	 * @param  string  $failed
	 * @return bool
	 */
	public function getFailedAttribute($failed)
	{
		return (bool) $failed;
	}

	/**
	 * Accessor for the "paid" attribute.
	 *
	 * @param  string  $paid
	 * @return bool
	 */
	public function getPaidAttribute($paid)
	{
		return (bool) $paid;
	}

	/**
	 * Accessor for the "refunded" attribute.
	 *
	 * @param  string  $refunded
	 * @return bool
	 */
	public function getRefundedAttribute($refunded)
	{
		return (bool) $refunded;
	}

	/**
	 * Checks if the charge has been captured.
	 *
	 * @return bool
	 */
	public function isCaptured()
	{
		return $this->captured === true;
	}

	/**
	 * Checks if the charge has been paid.
	 *
	 * @return bool
	 */
	public function isPaid()
	{
		return $this->paid === true;
	}

	/**
	 * Checks if the charge is partially refunded.
	 *
	 * @return bool
	 */
	public function isPartialRefunded()
	{
		return ($this->refunded === false && $this->refunds->count());
	}

	/**
	 * Checks if the charge is fully refunded.
	 *
	 * @return bool
	 */
	public function isRefunded()
	{
		return ($this->refunded === true && $this->refunds->count());
	}

	/**
	 * Checks if the charge can be captured.
	 *
	 * @return bool
	 */
	public function canBeCaptured()
	{
		return ($this->captured === false && $this->failed === false);
	}

	/**
	 * Returns the invoice associated to this charge.
	 *
	 * @return \Cartalyst\Stripe\Models\IlluminateInvoice
	 */
	public function invoice()
	{
		return $this->hasOne(static::$invoiceModel, 'stripe_id', 'invoice_id');
	}

	/**
	 * Returns the Eloquent model to be used for the invoice relationship.
	 *
	 * @return string
	 */
	public static function getInvoiceModel()
	{
		return static::$invoiceModel;
	}

	/**
	 * Sets the Eloquent model to be used for the invoice relationship.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public static function setInvoiceModel($model)
	{
		static::$invoiceModel = $model;
	}

	/**
	 * Returns all the refunds associated to this charge.
	 *
	 * @return \Cartalyst\Stripe\Models\IlluminateChargeRefund
	 */
	public function refunds()
	{
		return $this->hasMany(static::$refundModel, 'payment_id');
	}

	/**
	 * Returns the Eloquent model to be used for the refund relationship.
	 *
	 * @return string
	 */
	public static function getRefundModel()
	{
		return static::$refundModel;
	}

	/**
	 * Sets the Eloquent model to be used for the refund relationship.
	 *
	 * @param  string  $model
	 * @return void
	 */
	public static function setRefundModel($model)
	{
		static::$refundModel = $model;
	}

}
