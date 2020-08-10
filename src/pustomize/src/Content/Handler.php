<?php

declare(strict_types=1);

namespace PCIT\Pustomize\Content;

use App\GetAccessToken;
use PCIT\GPI\Webhooks\Context\ContentContext;
use PCIT\PCIT;

class Handler
{
    public function handle(ContentContext $context): void
    {
        $action = $context->action;
        $content_reference_id = $context->content_reference_id;
        $content_reference_reference = $context->content_reference_reference;
        $installation_id = $context->installation_id;

        // $access_token = $access_token = GetAccessToken::getGitHubAppAccessToken(null, null, (int) $installation_id);
        // $app = new PCIT(['github_access_token' => $access_token]);
        // $title = '';
        // $body = '';

        // $result = $app->github_apps_installations->createContentAttachment((int) $content_reference_id, $title, $body);
    }
}
