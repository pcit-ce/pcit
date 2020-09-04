<?php

declare(strict_types=1);

namespace PCIT\Framework\Attributes;

#[\Attribute()]
class OpenAPI
{
    /** @var string */
    public $tags;
    /** @var string */
    public $description;
    /** @var string */
    public $summary;
    /** @var string */
    public $operationId;
    /** @var array */
    public $response;

    public function __construct($config)
    {
        $this->tags = $config['tags'];
        $this->description = $config['description'];
        $this->summary = $config['summary'];
        $this->operationId = $config['operationId'];
        $this->response = $config['response'];
    }

    public function getRaw(): void
    {
        $tags = $this->tags;
        var_dump();
    }
}
