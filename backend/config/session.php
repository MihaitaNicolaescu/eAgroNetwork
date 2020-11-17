<?php

use Illuminate\Support\Str;

return [

    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'encrypt' => false,
    'files' => storage_path('framework/sessions'),
    'connection' => null,
    'table' => 'sessions',
    'lottery' => [2, 100],
    'cookie' => 'laravelsession',
    'path' => '/',
    'domain' => null,
    'secure' => false,

];
