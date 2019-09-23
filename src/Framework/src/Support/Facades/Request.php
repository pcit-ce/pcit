<?php

declare(strict_types=1);

namespace PCIT\Framework\Support\Facades;

/**
 * @method static get($key, $default = null)
 * @method static getSession()
 * @method static hasPreviousSession()
 * @method static hasSession()
 * @method static getClientIps()
 * @method static getClientIp()
 * @method static getScriptName()
 * @method static getPathInfo()
 * @method static getBasePath()
 * @method static getBaseUrl()
 * @method static getScheme()
 * @method static getPort()
 * @method static getUser()
 * @method static getPassword()
 * @method static getUserInfo()
 * @method static getHttpHost()
 * @method static getRequestUri()
 * @method static getSchemeAndHttpHost()
 * @method static getUri()
 * @method static getUriForPath($path)
 * @method static getRelativeUriForPath($path)
 * @method static getQueryString()
 * @method static isSecure()
 * @method static getHost()
 * @method static getMethod()
 * @method static getRealMethod()
 * @method static getMimeType($format)
 * @method static getFormat($mimeType)
 * @method static getRequestFormat($default = 'html')
 * @method static getContentType()
 * @method static getDefaultLocale()
 * @method static getLocale()
 * @method static isMethod($method)
 * @method static isMethodSafe($andCacheable = true)
 * @method static isMethodCacheable()
 * @method static getContent($asResource = false)
 * @method static getETags()
 * @method static isNoCache()
 * @method static getPreferredLanguage(array $locales = null)
 * @method static getLanguages()
 * @method static getCharsets()
 * @method static getEncodings()
 * @method static getAcceptableContentTypes()
 * @method static isXmlHttpRequest()
 * @method static isFromTrustedProxy()
 */
class Request extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'request';
    }
}
