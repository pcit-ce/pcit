<?php

declare(strict_types=1);

namespace PCIT\Deployer\Adapter;

class NPM extends AbstractAdapter
{
    const PROVIDER = 'npm';

    public $username;

    public $password;

    public $api_key;

    public $email;

    public $registry;

    public $tag = null;

    public function __construct(array $config)
    {
        $this->username = $config['username'] ?? null;
        $this->password = $config['password'] ?? null;
        $this->api_key = $config['api_key'] ?? null;
        $this->email = $config['email'] ?? null;
        $this->registru = $config['registry'] ?? null;
        $this->tag = $config['tag'] ?? null;
    }

    public function deploy()
    {
        return [
         'image' => 'plugins/npm',
         'env' => array_filter([
             $this->username ? 'NPM_USERNAME='.$this->username : null,
             $this->password ? 'NPM_PASSWORD='.$this->password : null,
             $this->api_key ? 'NPM_TOKEN='.$this->api_key : null,
             $this->email ? 'NPM_EMAIL='.$this->email : null,
             $this->registry ? 'NPM_REGISTRY='.$this->registry : null,
             $this->tag ? 'PLUGIN_TAG='.$this->tag : null,
         ]),
        ];
    }
}
