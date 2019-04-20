<?php

declare(strict_types=1);

namespace PCIT\Plugin\Adapter;

class NPM extends AbstractAdapter
{
    const PROVIDER = 'npm';

    public $username;

    public $password;

    public $api_key;

    public $email;

    public $registry;

    public $tag = null;

    public $skip_verify = false;

    public $fail_on_version_conflict = false;

    public $access = 'public';

    public function __construct(array $config)
    {
        $this->username = $config['username'] ?? null;
        $this->password = $config['password'] ?? null;
        $this->email = $config['email'] ?? null;
        $this->api_key = $config['api_key'] ?? null;
        $this->tag = $config['tag'] ?? null;
        $this->registru = $config['registry'] ?? null;
        $this->skip_verify = $config['skip_verify'] ?? false;
        $this->fail_on_version_conflict = $config['fail_on_version_conflict'] ?? false;
        $this->access = $config['access'] ?? 'public';
    }

    public function deploy(): array
    {
        return [
            'image' => 'plugins/npm',
            'env' => array_filter([
                $this->username ? 'NPM_USERNAME='.$this->username : null,
                $this->password ? 'NPM_PASSWORD='.$this->password : null,
                $this->email ? 'NPM_EMAIL='.$this->email : null,
                $this->api_key ? 'NPM_TOKEN='.$this->api_key : null,
                $this->tag ? 'PLUGIN_TAG='.$this->tag : null,
                $this->registry ? 'NPM_REGISTRY='.$this->registry : null,
                'PLUGIN_SKIP_VERIFY='.$this->skip_verify,
                'PLUGIN_FAIL_ON_VERSION_CONFLICT='.$this->fail_on_version_conflict,
                'PLUGIN_ACCESS='.$this->access,
            ]),
        ];
    }
}
