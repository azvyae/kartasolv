<?php

namespace App\Libraries;

use CodeIgniter\HTTP\RedirectResponse as BaseRedirectResponse;

/**
 * Override RedirectResponse class.
 * 
 * This class has function to override redirect()->to() function, instead of using site_url(),
 * this class use base_url() instead.
 * 
 * @package KartasolvApp\Libraries
 */
class RedirectResponse extends BaseRedirectResponse
{
    /**
     * Sets the URI to redirect to and, optionally, the HTTP status code to use.
     * If no code is provided it will be automatically determined.
     *
     * @param string   $uri  The URI to redirect to
     * @param int|null $code HTTP status code
     *
     * @return $this
     */
    public function to(string $uri, ?int $code = null, string $method = 'auto')
    {
        // If it appears to be a relative URL, then convert to full URL
        // for better security.
        if (strpos($uri, 'http') !== 0) {
            $uri = base_url($uri);
        }

        return $this->redirect($uri, $method, $code);
    }
}
