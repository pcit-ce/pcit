<?php

declare(strict_types=1);

namespace PCIT\Runner\Events\Handler;

class CommandHandler
{
    /**
     * @return ?string
     *
     * @throws \Exception
     */
    public static function parse(
        string $shell,
        string $step,
        string $image,
        ?array $commands,
        bool $raw = false
    ): ?string {
        if (null === $commands) {
            return null;
        }

        if ('sh' === $shell or 'bash' === $shell) {
            $content = '';

            $content .= <<<EOF
echo '
##[metadata]
{
    "step" : "$step",
    "image": "$image"
}
##[endmetadata]
'
EOF;
            $content .= "\n";

            for ($i = 0; $i < \count($commands); ++$i) {
                $command = $commands[$i];

                $content .= self::prepend($command);
            }
        } else {
            $content = $commands[0];
        }

        if ($raw) {
            return $content;
        }

        $ci_script = base64_encode($content);

        \Log::emergency('💻Command base64encode is '.$ci_script, []);

        return $ci_script;
    }

    public static function prepend($command)
    {
        $array = explode("\n", $command);

        $cmd = '';
        $special_cmd = '';

        foreach ($array as $item) {
            if ('' === $item) {
                continue;
            }

            if ('\\' === substr($item, -1)) {
                // $cmd .= $item;
                $special_cmd .= trim($item, '\\');
                continue;
            }

            if ($special_cmd) {
                $item = $special_cmd.$item;
                $special_cmd = '';
            }

            $cmd .= 'cat > /dev/stdout <<\'EOF\''."\n".'[36m[command]'.$item.'[0m'."\nEOF"."\n".$item."\n";
        }

        return $cmd."\n";
    }
}
