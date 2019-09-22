<?php

declare(strict_types=1);

namespace PCIT\GitHub\Webhooks;

use App\GetAccessToken;
use PCIT\PCIT;

class Content
{
    public static function handle($json_content): void
    {
        [
            'action' => $action,
            'content_reference_id' => $content_reference_id,
            'content_reference_reference' => $content_reference_reference,
            'installation_id' => $installation_id,
    ] = \PCIT\GitHub\WebhooksParse\Content::handle($json_content);

        $access_token = $access_token = GetAccessToken::getGitHubAppAccessToken(null, null, (int) $installation_id);
        $app = new PCIT(['github_access_token' => $access_token]);
        $title = '';
        $body = '';

        // $result = $app->github_apps_installations->createContentAttachment((int) $content_reference_id, $title, $body);
    }
}
