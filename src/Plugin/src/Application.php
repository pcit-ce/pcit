<?php

declare(strict_types=1);

namespace PCIT\Plugin;

class Application implements PluginInterface
{
    protected $adapter;

    /**
     * @param AdapterInterface $adapter
     * @param array            $config
     */
    public function __construct(AdapterInterface $adapter, $config = null)
    {
        $this->adapter = $adapter;
        // $this->setConfig($config);
    }

    public function deploy(): array
    {
        return $this->adapter->deploy();
    }
}
