<?php

/**
 * @see https://github.com/code-lts/doctum
 *
 *  $ doctum update .doctum.php
 *  $ cd build ; php -S 0.0.0.0:8080
 *
 */

use Doctum\Doctum;
use Doctum\RemoteRepository\GitHubRemoteRepository;
use Doctum\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    // ->exclude('docs')
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/app');

// $versions = GitVersionCollection::create($dir)
//     ->addFromTags('18.*.*')// add tag
//     ->add('master', 'master branch'); // add branch

return new Doctum($iterator,[
    'build_dir' => __DIR__.'/build/doctum',
    'cache_dir' => __DIR__.'/cache/doctum',
]);

// return new Doctum($iterator, [
//         'versions' => $versions,
//         'title' => 'Title API',
//         'build_dir' => __DIR__.'/build/%version%',
//         'cache_dir' => __DIR__.'/cache/%version%',
//         'remote_repository' => new GitHubRemoteRepository('username/repo', __DIR__),
//     ]
// );
