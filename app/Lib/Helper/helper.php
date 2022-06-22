<?php

function hashUrl(string $url, string $algo = 'sha256'): string
{
    $url = preg_replace("/^(https?)?:\/\//", "", $url);
    return hash($algo, $url);
}
