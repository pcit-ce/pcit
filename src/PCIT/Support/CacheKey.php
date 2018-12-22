<?php

declare(strict_types=1);

namespace PCIT\Support;

class CacheKey
{
    public static function cloneKey(int $jobId)
    {
        return 'pcit/clone/'.$jobId;
    }

    /**
     * @param string type pipeline success failure changed
     */
    public static function pipelineHashKey(int $jobId, string $type = 'pipeline')
    {
        return 'pcit/'.$type.'/'.$jobId;
    }

    public static function pipelineListKey(int $jobId, string $type = 'pipeline')
    {
        return 'pcit/'.$type.'/list/'.$jobId;
    }

    public static function pipelineListCopyKey(int $jobId, string $type = 'pipeline')
    {
        $cache = Cache::store();

        $sourceKey = 'pcit/'.$type.'/list/'.$jobId;
        $copyKey = 'pcit/'.$type.'/list_copy/'.$jobId;

        $cache->del($copyKey);

        $cache->restore($copyKey, 0, $cache->dump($sourceKey));

        return $copyKey;
    }

    public static function serviceHashKey(int $jobId)
    {
        return 'pcit/services/'.$jobId;
    }

    public static function notificationsHashKey(int $jobId)
    {
        return 'pcit/notifications/'.$jobId;
    }

    public static function logHashKey(int $jobId)
    {
        return 'pcit/build_log/'.$jobId;
    }

    /**
     * @param string type download or upload
     */
    public static function cacheKey(int $jobId, $type = 'download')
    {
        return 'pcit/cache/'.$type.'/'.$jobId;
    }
}
