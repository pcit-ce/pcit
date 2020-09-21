<?php

declare(strict_types=1);

namespace PCIT\GPI\Webhooks\Context\Components\User;

/**
 * @property int    $uid
 * @property string $username
 * @property string $pic
 * @property bool   $org
 */
class User
{
    public int $id;

    public string $login;

    public string $node_id;

    public string $avatar_url;

    /** 'User'|'Organization' */
    public string $type;

    public function __get(string $name)
    {
        if ('uid' === $name) {
            return $this->id;
        }

        if ('username' === $name) {
            return $this->login;
        }

        if ('pic' === $name) {
            return $this->avatar_url;
        }

        if ('org' === $name) {
            return 'Organization' === $this->type;
        }
    }
}
