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
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Stripe\Models;

use Guzzle\Service\Command\ResponseClassInterface;

class Charge extends Collection implements ResponseClassInterface
{
    /**
     * {@inheritDoc}
     */
    protected $collections = [
        'card',
        'refunds' => 'refunds.data',
    ];

    /**
     * Checks if the charge is paid.
     *
     * @return bool
     */
    public function isPaid()
    {
        return $this->paid;
    }

    /**
     * Checks if the charge has been refunded.
     *
     * @return bool
     */
    public function isRefunded()
    {
        return $this->refunded;
    }
}
