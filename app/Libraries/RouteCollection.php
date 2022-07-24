<?php

namespace App\Libraries;

use CodeIgniter\Router\RouteCollection as BaseRouteCollection;

class RouteCollection extends BaseRouteCollection
{
    /**
     * Specifies a route that is only available to GET requests.
     *
     * @param array|Closure|string $to
     */
    public function get(string $from, $to, ?array $options = null): RouteCollection
    {
        if (config('Feature')->autoRoutesImproved && $this->autoRoute) {
            [$controller, $method] = array_pad(explode('::', $to), 2, null);
            $to = $controller . "::get" . ucfirst($method);
        }
        $this->create('get', $from, $to, $options);

        return $this;
    }

    /**
     * Specifies a route that is only available to POST requests.
     *
     * @param array|Closure|string $to
     */
    public function post(string $from, $to, ?array $options = null): RouteCollection
    {
        if (config('Feature')->autoRoutesImproved && $this->autoRoute) {
            [$controller, $method] = array_pad(explode('::', $to), 2, null);
            $to = $controller . "::post" . ucfirst($method);
        }
        $this->create('post', $from, $to, $options);

        return $this;
    }

    /**
     * Specifies a route that is only available to PUT requests.
     *
     * @param array|Closure|string $to
     */
    public function put(string $from, $to, ?array $options = null): RouteCollection
    {
        if (config('Feature')->autoRoutesImproved && $this->autoRoute) {
            [$controller, $method] = array_pad(explode('::', $to), 2, null);
            $to = $controller . "::put" . ucfirst($method);
        }
        $this->create('put', $from, $to, $options);

        return $this;
    }

    /**
     * Specifies a route that is only available to DELETE requests.
     *
     * @param array|Closure|string $to
     */
    public function delete(string $from, $to, ?array $options = null): RouteCollection
    {
        if (config('Feature')->autoRoutesImproved && $this->autoRoute) {
            [$controller, $method] = array_pad(explode('::', $to), 2, null);
            $to = $controller . "::delete" . ucfirst($method);
        }
        $this->create('delete', $from, $to, $options);

        return $this;
    }

    /**
     * Specifies a route that is only available to PATCH requests.
     *
     * @param array|Closure|string $to
     */
    public function patch(string $from, $to, ?array $options = null): RouteCollection
    {
        if (config('Feature')->autoRoutesImproved && $this->autoRoute) {
            [$controller, $method] = array_pad(explode('::', $to), 2, null);
            $to = $controller . "::patch" . ucfirst($method);
        }
        $this->create('patch', $from, $to, $options);

        return $this;
    }
}
