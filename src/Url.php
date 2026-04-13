<?php

declare(strict_types=1);

namespace Marwa\Support;

use InvalidArgumentException;

class Url
{
    /**
     * Parse a URL and return its components.
     *
     * @param string $url
     * @return array
     * @throws InvalidArgumentException
     */
    public static function parse(string $url): array
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false && !preg_match('/^[a-zA-Z][a-zA-Z0-9+.-]*:\/?/', $url)) {
            throw new InvalidArgumentException("Invalid URL: {$url}");
        }

        $components = parse_url($url);

        if ($components === false) {
            throw new InvalidArgumentException("Invalid URL: {$url}");
        }

        return $components;
    }

    /**
     * Build a URL from components.
     *
     * @param array $components
     * @return string
     */
    public static function build(array $components): string
    {
        $url = '';

        if (isset($components['scheme'])) {
            $url .= $components['scheme'] . '://';
        }

        if (isset($components['host'])) {
            $url .= $components['host'];
        }

        if (isset($components['port'])) {
            $url .= ':' . $components['port'];
        }

        if (isset($components['path'])) {
            $url .= $components['path'];
        }

        if (isset($components['query'])) {
            $url .= '?' . $components['query'];
        }

        if (isset($components['fragment'])) {
            $url .= '#' . $components['fragment'];
        }

        return $url;
    }

    /**
     * Get the query string as an array.
     *
     * @param string $url
     * @return array
     */
    public static function query(string $url): array
    {
        $components = parse_url($url, PHP_URL_QUERY);

        if (empty($components)) {
            return [];
        }

        parse_str($components, $query);
        return $query;
    }

    /**
     * Add or update query parameters.
     *
     * @param string $url
     * @param array $params
     * @return string
     */
    public static function withQuery(string $url, array $params): string
    {
        $query = self::query($url);
        $query = array_merge($query, $params);

        $components = parse_url($url);
        $components['query'] = http_build_query($query);

        return self::build($components);
    }

    /**
     * Get a specific query parameter.
     *
     * @param string $url
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function getQuery(string $url, string $key, mixed $default = null): mixed
    {
        $query = self::query($url);
        return $query[$key] ?? $default;
    }

    /**
     * Remove query parameters.
     *
     * @param string $url
     * @param array|string $keys
     * @return string
     */
    public static function withoutQuery(string $url, array|string $keys): string
    {
        $keys = is_string($keys) ? [$keys] : $keys;
        $query = self::query($url);

        foreach ($keys as $key) {
            unset($query[$key]);
        }

        $components = parse_url($url);

        if (!empty($query)) {
            $components['query'] = http_build_query($query);
        } else {
            unset($components['query']);
        }

        return self::build($components);
    }

    /**
     * Get the scheme (http, https, etc.).
     *
     * @param string $url
     * @return string|null
     */
    public static function scheme(string $url): ?string
    {
        return parse_url($url, PHP_URL_SCHEME);
    }

    /**
     * Get the host.
     *
     * @param string $url
     * @return string|null
     */
    public static function host(string $url): ?string
    {
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * Get the port.
     *
     * @param string $url
     * @return int|null
     */
    public static function port(string $url): ?int
    {
        return parse_url($url, PHP_URL_PORT);
    }

    /**
     * Get the path.
     *
     * @param string $url
     * @return string|null
     */
    public static function path(string $url): ?string
    {
        return parse_url($url, PHP_URL_PATH);
    }

    /**
     * Get the fragment (anchor).
     *
     * @param string $url
     * @return string|null
     */
    public static function fragment(string $url): ?string
    {
        return parse_url($url, PHP_URL_FRAGMENT);
    }

    /**
     * Get the base domain.
     *
     * @param string $url
     * @return string|null
     */
    public static function domain(string $url): ?string
    {
        $host = self::host($url);

        if ($host === null) {
            return null;
        }

        $parts = explode('.', $host);

        if (count($parts) >= 2) {
            return implode('.', array_slice($parts, -2));
        }

        return $host;
    }

    /**
     * Check if URL is absolute.
     *
     * @param string $url
     * @return bool
     */
    public static function isAbsolute(string $url): bool
    {
        return parse_url($url, PHP_URL_SCHEME) !== null;
    }

    /**
     * Get full domain with scheme.
     *
     * @param string $url
     * @return string
     */
    public static function fullDomain(string $url): string
    {
        $scheme = self::scheme($url) ?? 'http';
        $host = self::host($url) ?? '';
        $port = self::port($url);

        $result = "{$scheme}://{$host}";

        if ($port !== null) {
            $result .= ":{$port}";
        }

        return $result;
    }
}
