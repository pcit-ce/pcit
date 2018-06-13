<?php

namespace App\Http\Controllers;


class WeChatController
{
    public function __invoke()
    {
        return [
            'code' => 200,
            'data' =>
                'https://user-images.githubusercontent.com/16733187/41222863-c610772e-6d9a-11e8-8847-27ac16c8fb54.jpg'
        ];
    }
}
