<?php namespace Cartalyst\Stripe;
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

use Cartalyst\Stripe\Card\CardGateway;
use Cartalyst\Stripe\Charge\ChargeGateway;
use Cartalyst\Stripe\Subscription\SubscriptionGateway;
use Illuminate\Support\Facades\Config;

trait BillableTrait {

	/**
	 * The Stripe gateway instance.
	 *
	 * @var \Cartalyst\Stripe\StripeGateway
	 */
	protected $gateway = null;

	/**
	 * The Stripe Customer instance.
	 *
	 * @var \Stripe_Customer
	 */
	protected $customer = null;

	/**
	 * The Stripe API key.
	 *
	 * @var string
	 */
	protected static $stripeKey;

	/**
	 * {@inheritDoc}
	 */
	public function card($card = null)
	{
		return new CardGateway($this, $card);
	}

	/**
	 * {@inheritDoc}
	 */
	public function cards()
	{
		return $this->hasMany('Cartalyst\Stripe\Card\IlluminateCard');
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateDefaultCard($token)
	{
		$customer = $this->getStripeCustomer();
		$customer->card = $token;
		$customer->save();

		$this->last_four = $customer->cards->retrieve($customer->default_card)->last4;
		$this->save();
	}

	/**
	 * {@inheritDoc}
	 */
	public function charge($charge = null)
	{
		return new ChargeGateway($this, $charge);
	}

	/**
	 * {@inheritDoc}
	 */
	public function charges()
	{
		return $this->hasMany('Cartalyst\Stripe\Charge\IlluminateCharge');
	}

	/**
	 * {@inheritDoc}
	 */
	public function subscription($subscription = null)
	{
		return new SubscriptionGateway($this, $subscription);
	}

	/**
	 * {@inheritDoc}
	 */
	public function subscriptions()
	{
		return $this->hasMany('Cartalyst\Stripe\Subscription\IlluminateSubscription');
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSubscribed()
	{
		return (bool) $this->subscriptions()->whereActive(1)->count();
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasActiveCard()
	{
		return (bool) $this->cards->count();
	}

	/**
	 * {@inheritDoc}
	 */
	public function applyCoupon($coupon)
	{
		$customer = $this->getStripeCustomer();

		$customer->coupon = $coupon;

		$customer->save();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStripeId()
	{
		return $this->stripe_id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStripeCustomer()
	{
		return $this->customer ?: $this->gateway()->getStripeCustomer();
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getStripeKey()
	{
		return static::$stripeKey ?: Config::get('services.stripe.secret');
	}

	/**
	 * {@inheritDoc}
	 */
	public static function setStripeKey($key)
	{
		static::$stripeKey = $key;
	}

	/**
	 * {@inheritDoc}
	 */
	public function syncWithStripe()
	{
		$this->card()->syncWithStripe();

		$this->charge()->syncWithStripe();

		$this->subscription()->syncWithStripe();
	}

	/**
	 * Returns the Stripe gateway.
	 *
	 * @return \Cartalyst\Stripe\StripeGateway
	 */
	protected function gateway()
	{
		return $this->gateway ?: new StripeGateway($this);
	}

}
