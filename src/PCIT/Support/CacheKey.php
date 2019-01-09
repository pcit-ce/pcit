<?php

declare(strict_types=1);

namespace PCIT\Support;

class CacheKey
{
    /*
    1) "pcit/success/list/745"
    3) "pcit/success/745"

    2) "pcit/failure/list/745"
    9) "pcit/failure/745"

   11) "pcit/changed/list/745"
    4) "pcit/changed/745"

    5) "pcit/pipeline/list/745"
    6) "pcit/pipeline/745"

    7) "pcit/services/745"

    8) "pcit/cache/upload/745"
   12) "pcit/cache/download/745"

   13) "pcit/clone/745"

   14) "pcit/notifications/745"
    */

    public static function cloneKey(int $jobId)
    {
        return 'pcit/clone/'.$jobId;
    }

    /**
     * @param int    $jobId
     * @param string $type  pipeline | success | failure | changed
     *
     * @return string
     */
    public static function pipelineHashKey(int $jobId, string $type = 'pipeline')
    {
        return 'pcit/'.$type.'/'.$jobId;
    }

    public static function pipelineListKey(int $jobId, string $type = 'pipeline')
    {
        return 'pcit/'.$type.'/list/'.$jobId;
    }

    public static function pipelineDumpListKey(int $jobId, string $type = 'pipeline')
    {
        $cache = Cache::store();
        $sourceKey = 'pcit/'.$type.'/list/'.$jobId;

        return $cache->dump($sourceKey);
    }

    public static function pipelineListCopyKey(int $jobId, string $type = 'pipeline')
    {
        $cache = Cache::store();

        $copyKey = 'pcit/'.$type.'/list_copy/'.$jobId;
        $cache->del($copyKey);

        $dump = self::pipelineDumpListKey($jobId, $type);

        $cache->restore($copyKey, 0, $dump);

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
     * @param int $jobId
     * @param string type download | upload
     *
     * @return string
     */
    public static function cacheKey(int $jobId, $type = 'download')
    {
        return 'pcit/cache/'.$type.'/'.$jobId;
    }

    /**
     * 删除某个 job 所用到的缓存.
     *
     * @param int $jobId
     *
     * @throws \Exception
     */
    public static function flush(int $jobId): void
    {
        $cache = Cache::store();

        $result = $cache->keys('pcit/*/'.$jobId);

        foreach ($result as $key) {
            $cache->del($key);
        }
    }
}
