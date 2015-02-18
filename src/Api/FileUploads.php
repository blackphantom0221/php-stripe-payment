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

use GuzzleHttp\Post\PostFile;

class FileUploads extends Api
{
    /**
     * {@inheritDoc}
     */
    public function baseUrl()
    {
        return 'https://uploads.stripe.com';
    }

    /**
     * Creates a file upload.
     *
     * @param  string  $file
     * @param  string  $purpose
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function create($file, $purpose)
    {
        $file = new PostFile($file);

        return $this->_post('files', compact('purpose'), compact('file'));
    }

    /**
     * Retrieves an existing file upload.
     *
     * @param  string  $fileId
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function find($fileId)
    {
        return $this->_get("files/{$fileId}");
    }

    /**
     * Lists all file uploads.
     *
     * @param  array  $parameters
     * @return \GuzzleHttp\Message\ResponseInterface
     */
    public function all(array $parameters = [])
    {
        return $this->_get('files', $parameters);
    }
}
