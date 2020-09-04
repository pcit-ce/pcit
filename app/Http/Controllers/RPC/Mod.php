<?php

declare(strict_types=1);

namespace App\Http\Controllers\RPC;

use App\RPC\Handler as RPC;
use PCIT\Framework\Attributes\Route;
use PCIT\Framework\Http\Request;

/**
 * @see http://wiki.geekdream.com/Specification/json-rpc_2.0.html
 */
class Mod
{
    #[Route('post','rpc')]
    public function __invoke(Request $request, RPC $rpc)
    {
        $content = $request->getContent();

        $token = $request->headers()->get('Authorization');

        if ($token !== 'token '.config('rpc.secret') || 'token ' === $token) {
            return [
                'error' => [
                    'code' => 401,
                    'message' => 'miss Authorization header, please set CI_RPC_SECRET env',
                ],
            ];
        }

        return $rpc->handle($content);
    }
}
