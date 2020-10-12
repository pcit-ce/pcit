<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Repositories;

use PCIT\GitHub\Service\ClientCommon;
use stdClass;

/**
 * @see https://developer.github.com/v3/repos
 */
class Client extends ClientCommon
{
    /**
     * @param array[] $client_payload
     */
    public function createDispatchEvent(
        string $owner,
        string $repo,
        string $event_type,
        array $client_payload = []
    ): void {
        if ([] === $client_payload) {
            $client_payload = new stdClass();
        }

        $data = compact('event_type', 'client_payload');

        $this->curl->post(
            $this->api_url.'/repos/'.$owner.'/'.$repo.'/dispatches',
            json_encode($data)
        );

        echo 111;

        $this->successOrFailure(204, true);
    }
}
