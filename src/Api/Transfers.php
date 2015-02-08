<?php

/**
 * Part of the Stripe package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the Cartalyst PSL License.
 *
 * This source file is subject to the Cartalyst PSL License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Stripe
 * @version    1.0.0
 * @author     Cartalyst LLC
 * @license    Cartalyst PSL
 * @copyright  (c) 2011-2015, Cartalyst LLC
 * @link       http://cartalyst.com
 */

namespace Cartalyst\Stripe\Api;

class Transfers extends Api
{
    /**
     * Creates a new transfer.
     *
     * @param  array  $parameters
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function create(array $parameters = [])
    {
        return $this->_post('v1/transfers', $parameters);
    }

    /**
     * Retrieves an existing transfer.
     *
     * @param  string  $transferId
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function find($transferId)
    {
        return $this->_get("v1/transfers/{$transferId}");
    }

    /**
     * Updates an existing transfer.
     *
     * @param  string  $transferId
     * @param  array  $parameters
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function update($transferId, array $parameters = [])
    {
        return $this->_post("v1/transfers/{$transferId}", $parameters);
    }

    /**
     * Cancels an existing transfer.
     *
     * @param  string  $transferId
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function cancel($transferId)
    {
        return $this->_post("v1/transfers/{$transferId}/cancel");
    }

    /**
     * Lists all transfers.
     *
     * @param  array  $parameters
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function all(array $parameters = [])
    {
        return $this->_get('v1/transfers', $parameters);
    }
}
