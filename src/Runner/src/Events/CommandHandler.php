<?php

declare(strict_types=1);

namespace PCIT\Runner\Events;

class CommandHandler
{
    /**
     * @return string
     *
     * @throws Exception
     */
    public static function parse(string $shell = 'sh', string $step, string $image, ?array $commands)
    {
        if (null === $commands) {
            return null;
        }

        if ('sh' === $shell or 'bash' === $shell) {
            $content = '\n';

            $content .= 'echo;echo\n\necho "==>" Pipeline ['.$step.'] Run On "=>" ['.$image.']';

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

        \Log::emergency('Command base64encode is '.$ci_script, []);

        return $ci_script;
    }
}
