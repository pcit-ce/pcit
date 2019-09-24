<?php

declare(strict_types=1);

namespace PCIT\Builder\Events;

use PCIT\Framework\Support\Log;

class CommandHandler
{
    /**
     * @param string     $setup
     * @param string     $image
     * @param array|null $commands
     *
     * @return string
     *
     * @throws Exception
     */
    public static function parse(string $shell = 'sh', string $setup, string $image, ?array $commands)
    {
        if (null === $commands) {
            return null;
        }

        if ('sh' === $shell or 'bash' === $shell) {
            $content = '\n';

            $content .= 'echo;echo\n\necho "==>" Pipeline '.$setup.' Run On "=>" '.$image;

            $content .= '\n\nsleep 0.1;echo;echo\n\nset -x\n\n';

            for ($i = 0; $i < \count($commands); ++$i) {
                $command = addslashes($commands[$i]);

                $content .= $command;

                $content .= '\n\n';
            }
        } else {
            $content = $commands[0];
        }

        // var_dump(stripcslashes($content));

        $ci_script = base64_encode(stripcslashes($content));

        Log::debug(__FILE__, __LINE__, 'Command base64encode is '.$ci_script, [], Log::EMERGENCY);

        return $ci_script;
    }
}
