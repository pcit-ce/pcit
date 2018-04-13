<?php

declare(strict_types=1);

namespace KhsCI\Service\SEO;

use Curl\Curl;

class Baidu
{
    private $appid;
    private $token;
    private $type;
    private $curl;

    public function __construct($config, Curl $curl)
    {
        $this->curl = $curl;
        $this->appid = '1582772278694886';
        $this->token = 'JmdFnjWsRc0NEYRL';
    }

    /**
     * @param string $xml
     *
     * @return array
     */
    private function xmlToUrlArray(string $xml)
    {
        $obj = simplexml_load_file($xml);
        $i = -1;
        $urls = [];
        foreach ($obj->children() as $k => $v) {
            ++$i;
            $urls[$i] = "$v->loc";
        }

        return $urls;
    }

    /**
     * 历史链接提交.
     *
     * @see https://ziyuan.baidu.com/xzh/commit/method
     *
     * @param string $siteMap
     *
     * @return array|mixed
     *
     * @throws \Curl\Error\CurlError
     */
    public function history(string $siteMap = 'https://www.khs1994.com/sitemap.xml')
    {
        $this->type = 'batch';
        $urls = $this->xmlToUrlArray($siteMap);

        return $this->push($urls);
    }

    /**
     * 当天产生内容提交.
     *
     * @param string $siteMap
     *
     * @return array|mixed
     *
     * @throws \Curl\Error\CurlError
     */
    public function realtime($siteMap = 'https://www.khs1994.com/sitemap.xml')
    {
        $this->type = 'realtime';
        $urls = $this->xmlToUrlArray($siteMap);

        return $this->push($urls);
    }

    /**
     * 原创内容提交（内容产生后 1 小时之内提交）.
     *
     * @param string $siteMap
     *
     * @return array|mixed
     *
     * @throws \Curl\Error\CurlError
     */
    public function original($siteMap = 'https://www.khs1994.com/sitemap.xml')
    {
        $this->type = 'realtime,original';
        $urls = $this->xmlToUrlArray($siteMap);

        return $this->push($urls);
    }

    /**
     * 参数为包含 urls 的数组.
     *
     * @param array $urls
     *
     * @return array|mixed
     *
     * @throws \Curl\Error\CurlError
     */
    public function push(array $urls)
    {
        $api_url = "http://data.zz.baidu.com/urls?appid=$this->appid&token=$this->token&type=$this->type";
        $this->curl->setHeader('Content-Type', 'text/plain');

        return $this->curl->post($api_url, implode("\n", $urls));
    }
}
