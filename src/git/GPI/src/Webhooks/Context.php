<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks;

use JsonMapper;
use PCIT\GPI\Webhooks\Context\Components\Installation;
use PCIT\GPI\Webhooks\Context\Components\Organization;
use PCIT\GPI\Webhooks\Context\Components\Repository;
use PCIT\GPI\Webhooks\Context\Components\User\Owner;
use PCIT\GPI\Webhooks\Context\Components\User\Sender;
use stdClass;

/**
 * @property string       $action
 * @property Sender       $sender
 * @property Repository   $repository
 * @property Organization $organization
 * @property Installation $installation
 * @property Owner        $owner
 */
abstract class Context implements ContextInterface
{
    /** 原始 webhook 内容 */
    public string $raw;

    public array $context_array;

    /** @var string git 提供者 */
    public $git_type;

    /** @var bool 是否为私有仓库 */
    public $private;

    public string $action;

    public JsonMapper $json_mapper;

    public bool $org;

    public function __construct(array $context_array = [], string $raw = '{}')
    {
        $this->raw = $raw;
        $this->context_array = $context_array;
        $mapper = new JsonMapper();

        $mapper->bRemoveUndefinedAttributes = false;
        // class 中存在必须存在（@required）, json 不存在
        $mapper->bExceptionOnMissingData = false;
        // json 存在，class 中不存在
        $mapper->bExceptionOnUndefinedProperty = false;
        // json 存在，class 中不存在，处理函数
        $mapper->undefinedPropertyHandler = [$this, 'setUndefinedProperty'];

        $this->json_mapper = $mapper;

        $this->installation = $mapper->map(
            $this->installation ?? new \stdClass(),
            new Installation()
        );

        $this->sender = $mapper->map(
            $this->sender ?? new \stdClass(),
            new Sender()
        );

        $this->organization = $mapper->map(
            $this->organization ?? new \stdClass(),
            new Organization()
        );

        $this->repository = $mapper->map(
            $this->repository ?? new \stdClass(),
            new Repository()
        );

        $this->owner = $this->repository->owner ?? new stdClass();
    }

    public function __get(string $name)
    {
        return $this->context_array[$name] ?? ($this->$name = json_decode($this->raw)->$name ?? null) ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->context_array[$name] = $value;
    }

    public function setUndefinedProperty($object, string $propName, $jsonValue): void
    {
        // var_dump($object, $propName, $jsonValue);

        // $object->{'UNDEF' . $propName} = $jsonValue;

        $object->$propName = $jsonValue;
    }
}
