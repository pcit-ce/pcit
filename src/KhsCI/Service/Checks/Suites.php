<?php

declare(strict_types=1);

namespace KhsCI\Service\Checks;

use Curl\Curl;

class Suites
{
    private static $curl;

    private static $api_url;

    /**
     * Suites constructor.
     *
     * @param Curl   $curl
     * @param string $api_url
     */
    public function __construct(Curl $curl, string $api_url)
    {
        self::$curl = $curl;

        self::$api_url = $api_url;
    }

    /**
     * Get a single check suite.
     */
    public function getSingle(): void
    {
    }

    /**
     * List check suites for a specific ref.
     */
    public function listSpecificRef(): void
    {
    }

    /**
     * Set preferences for check suites on a repository.
     */
    public function setPreferences(): void
    {
    }

    public function create(): void
    {
    }

    public function request(): void
    {
    }
}
