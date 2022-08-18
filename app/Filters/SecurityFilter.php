<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Security Filter are filter interface that runs before controller executed.
 * 
 * This Security Filter firstly, run checkAuth method.
 * @see https://codeigniter.com/user_guide for complete instructions
 * 
 * @package KartasolvApp\Filters
 */
class SecurityFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        return checkAuth();
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}
