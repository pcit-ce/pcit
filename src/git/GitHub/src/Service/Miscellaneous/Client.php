<?php

declare(strict_types=1);

namespace PCIT\GitHub\Service\Miscellaneous;

use PCIT\GPI\ServiceClientCommon;

class Client
{
    use ServiceClientCommon;

    /**
     * List all codes of conduct.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function listAllCodesOfConduct()
    {
        return $this->curl->get($this->api_url.'/codes_of_conduct');
    }

    /**
     * Get an individual code of conduct.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getCodeOfConduct(string $key)
    {
        return $this->curl->get($this->api_url.'/codes_of_conduct/'.$key);
    }

    /**
     * @throws \Exception
     *
     * @return mixed
     */
    public function getRepositoryCodeOfConduct(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name);
    }

    /**
     * Get the contents of a repository's code of conduct.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getContentsOfRepositoryCodeOfConduct(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/community/code_of_conduct');
    }

    /**
     * Emojis.
     *
     * Lists all the emojis available to use on GitHub.
     *
     * @throws \Exception
     *
     * @return mixed
     *
     * @see https://developer.github.com/v3/emojis/
     */
    public function getEmojis()
    {
        return $this->curl->get($this->api_url.'/emojis');
    }

    /**
     * Listing available templates.
     *
     * @see https://developer.github.com/v3/gitignore/
     *
     * @throws \Exception
     */
    public function listGitignore()
    {
        return $this->curl->get($this->api_url.'/gitignore/templates');
    }

    /**
     * Get a single template.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getGitignore(string $name = 'C')
    {
        return $this->curl->get($this->api_url.'/gitignore/templates/'.$name);
    }

    /**
     * List all licenses.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function listLicenses()
    {
        return $this->curl->get($this->api_url.'/licenses');
    }

    /**
     * Get an individual license.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getLicense(string $name = 'mit')
    {
        return $this->curl->get($this->api_url.'/licenses/'.$name);
    }

    /**
     * Get the contents of a repository's license.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function getContentsOfRepositoryLicense(string $repo_full_name)
    {
        return $this->curl->get($this->api_url.'/repos/'.$repo_full_name.'/license');
    }

    /**
     * Render an arbitrary Markdown document.
     *
     * @param string $mode    markdown or gfm
     * @param string $context gfm only
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function RenderMarkdown(string $text, string $context, string $mode = 'markdown')
    {
        $data = [
            'text' => $text,
            'mode' => $mode,
            'context' => $context,
        ];

        return $this->curl->post($this->api_url.'/markdown', json_encode(array_filter($data)));
    }

    /**
     * Render a Markdown document in raw mode.
     *
     * @throws \Exception
     *
     * @return mixed
     */
    public function RenderMarkdownRaw(string $content, string $content_type = 'text/plain')
    {
        return $this->curl->post($this->api_url.'/markdown/raw', $content, ['Content-Type' => $content_type]);
    }

    /**
     * meta.
     *
     * @throws \Exception
     *
     * @return mixed
     *
     * @see https://developer.github.com/v3/meta/
     */
    public function getMeta()
    {
        return $this->curl->get($this->api_url.'/meta');
    }

    /**
     * Get your current rate limit status.
     *
     * @throws \Exception
     *
     * @return mixed
     *
     * @see https://developer.github.com/v3/rate_limit/
     */
    public function getRateLimit()
    {
        return $this->curl->get($this->api_url.'/rate_limit');
    }
}
