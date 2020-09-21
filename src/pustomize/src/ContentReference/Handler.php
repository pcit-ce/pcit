<?php

declare(strict_types=1);

namespace PCIT\Pustomize\ContentReference;

use App\GetAccessToken;
use PCIT\GPI\Webhooks\Context\ContentReferenceContext;
use PCIT\PCIT;

class Handler
{
    public function handle(ContentReferenceContext $context): void
    {
        $action = $context->action;
        $content_reference = $context->content_reference;
        $id = $content_reference->id;
        $reference = $content_reference->reference;
        $installation_id = $context->installation->id;

        $access_token = $access_token = GetAccessToken::getGitHubAppAccessToken(null, null, (int) $installation_id);
        $app = new PCIT(['github_access_token' => $access_token]);
        $title = '';
        $body = '';

        $result = $app->github_apps_installations->createContentAttachment((int) $id, $title, $body);
    }
}
