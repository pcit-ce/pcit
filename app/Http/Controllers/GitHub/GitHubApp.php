<?php

declare(strict_types=1);

namespace App\Http\Controllers\GitHub;

use PCIT\Framework\Attributes\Route;
use PCIT\Framework\Http\Request;
use PCIT\Framework\Support\HttpClient;

class GitHubApp
{
    #[Route('get', 'api/github/app/new')]

    // #[Query(["webhook_url"])]
    public function new(Request $request): string
    {
        $webhook_url = $request->get('webhook_url', null);
        if (!$webhook_url) {
            throw new \Exception('please set webhook_url, e.g. '.config('app.host').'/api/github/app/new?webhook_url=https://smee.io/XXXXXXXXXXXXXXX', 500);
        }
        $manifest = [
            'name' => 'PCIT',
            'url' => config('app.host'),
            'hook_attributes' => [
                'url' => $webhook_url,
                'active' => true,
            ],
            'redirect_url' => config('app.host').'/api/github/app/new/callback',
            'description' => 'PCIT GitHub App',
            'public' => true,
            'default_events' => [
                'check_run',
                'check_suite',
                'commit_comment',
                //'content_reference',
                'create',
                'delete',
                'deploy_key',
                'deployment',
                'deployment_status',
                // 'fork',
                // 'gollum',
                'issue_comment',
                'issues',
                'label',
                //'marketplace_purchase',
                'member',
                'membership',
                'meta',
                'milestone',
                'organization',
                'org_block',
                'page_build',
                'project_card',
                'project_column',
                'project',
                'public',
                'pull_request',
                'pull_request_review',
                'pull_request_review_comment',
                'push',
                'registry_package',
                'release',
                'repository_dispatch',
                'repository',
                'repository_import',
                'security_advisory',
                'star',
                'status',
                'team',
                'team_add',
                //'watch',
                'workflow_dispatch',
                'workflow_run',
            ],
            'default_permissions' => [
                'actions' => 'write',
                'administration' => 'write',
                'blocking' => 'write',
                'checks' => 'write',
                'contents' => 'write',
                'deployments' => 'write',
                'emails' => 'write',
                'followers' => 'write',
                'gpg_keys' => 'write',
                'issues' => 'write',
                'keys' => 'write',
                'members' => 'write',
                'metadata' => 'read',
                'organization_administration' => 'write',
                'organization_hooks' => 'write',
                'organization_plan' => 'read',
                'organization_projects' => 'admin',
                'organization_secrets' => 'write',
                'organization_self_hosted_runners' => 'write',
                'organization_user_blocking' => 'write',
                'packages' => 'write',
                'pages' => 'write',
                'plan' => 'read',
                'pull_requests' => 'write',
                'repository_hooks' => 'write',
                'repository_projects' => 'admin',
                'secrets' => 'write',
                'security_events' => 'write',
                'starring' => 'write',
                'statuses' => 'write',
                'team_discussions' => 'write',
                'vulnerability_alerts' => 'read',
                'watching' => 'write',
                'workflows' => 'write',
            ],
        ];

        $manifest_json_string = json_encode($manifest);

        $action = 'https://github.com/settings/apps/new';

        return <<<EOF
<html>
<head>
<title>Create new GitHub App | PCIT</title>
</head>
<form action="$action" method="post">
Create a GitHub App Manifest: <input type="text" name="manifest" id="manifest"><br>
<input type="submit" value="Submit">
</form>

<script>
input = document.getElementById("manifest")
input.value = '$manifest_json_string'
</script>
</html>
EOF;
    }

    #[Route('get', 'api/github/app/new/callback')]

    // #[Query(["code"])]
    public function callback(Request $request)
    {
        $code = $request->get('code');

        $response = HttpClient::post(
            "https://api.github.com/app-manifests/$code/conversions"
        );

        if (201 !== HttpClient::getCode()) {
            throw new \Exception($response, 500);
        }

        $app_host = config('app.host');

        $result = json_decode($response);

        $content = <<<EOF
# this content ony show once

CI_GITHUB_APP_NAME=$result->name
CI_GITHUB_APP_ID=$result->id

CI_GITHUB_CLIENT_ID=$result->client_id
CI_GITHUB_CLIENT_SECRET=$result->client_secret
CI_GITHUB_CALLBACK_URL=$app_host/oauth/github

CI_WEBHOOKS_TOKEN=$result->webhook_secret
$result->pem
EOF;

        file_put_contents(\dirname(config('git.github.app.private_key_path')).'/'.$result->id.'.private.key', $result->pem);

        return \Response::make($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
