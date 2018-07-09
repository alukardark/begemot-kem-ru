<?php

use Arrilot\BitrixCacher\Cache;
use Arrilot\BitrixCacher\AbortCacheException;


function cache($key, $minutes, Closure $callback, $initDir = '/', $basedir = 'cache')
{
    if (empty($key) || empty($minutes)) {
        throw new AbortCacheException();
    }

    return Cache::remember($key, $minutes, $callback, $initDir, $basedir);
}

Arrilot\BitrixBlade\BladeProvider::register();
Arrilot\BitrixModels\ServiceProvider::register();