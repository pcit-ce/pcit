<?php

declare(strict_types=1);

namespace PCIT\Deployer\Adapter;

class DOCKER extends AbstractAdapter
{
    const provider = 'docker';

    public $image;

    public $target;

    /**
     * @var array
     */
    public $build_args;

    public $context;

    public $dockerfile;

    public $username;

    public $password;

    public $registry;

    public $dockerHost;

    public $dry_run;

    public function __construct(array $config)
    {
        $tag = $config['tag'] ?? $config['tags'] ?? 'latest';
        $this->image = $config['repo'].':'.$tag;
        $this->target = $config['target'] ?? null;
        $this->build_args = $config['build_args'] ?? [];
        $this->context = $config['context'] ?? '.';
        $this->dockerfile = $config['dockerfile'] ?? 'Dockerfile';
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->registry = $config['registry'] ?? null;
        $this->dockerHost = $config['host'];
        $this->dry_run = $config['dry_run'] ?? false;
    }

    public function getOptions()
    {
        $image = $this->registry ? $this->registry.'/'.$this->image : $this->image;
        $options = '-t '.$image;
        $this->target && $options .= ' --target '.$this->target;
        $this->dockerfile && $options .= ' -f '.$this->dockerfile;

        foreach ($this->build_args as $build_arg) {
            $options .= ' --build-arg '.$build_arg;
        }

        $options .= ' '.$this->context;

        return $options;
    }

    public function deploy(): array
    {
        return [
          'image' => 'pcit/docker',
          'env' => [
            'PCIT_DOCKER_COMMAND=build',
            'PCIT_DOCKER_OPTIONS='.$this->getOptions(),
            'PCIT_DOCKER_IMAGE='.$this->image,
            'PCIT_DOCKER_HOST='.$this->dockerHost,
            'PCIT_DOCKER_BUILDKIT=1',
            'PCIT_DOCKER_USERNAME='.$this->username,
            'PCIT_DOCKER_PASSWORD='.$this->password,
            'PCIT_DOCKER_REGISTRY='.$this->registry,
            'PCIT_DOCKER_DRY_RUN='.(int) $this->dry_run,
          ],
      ];
    }
}
