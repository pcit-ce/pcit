<?php

declare(strict_types=1);

namespace App\Http\Controllers\GitHub;

use PCIT\Framework\Support\HttpClient;

class GitHubApp
{
    public function new(): string
    {
        $webhook_url = \Request::get('webhook_url', null);
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
                'issues',
                'issue_comment',
                'check_suite',
                'check_run',
            ],
            'default_permissions' => [
                'issues' => 'write',
                'checks' => 'write',
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

    public function callback()
    {
        $code = \Request::get('code');

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

        file_put_contents(base_path().'framework/storage/private_key/'.$result->id.'.private.key', $result->pem);

        return \Response::make($content, 200, [
            'Content-Type' => 'text/plain',
        ]);
    }
}
