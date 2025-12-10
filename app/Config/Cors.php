<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    public array $default = [
        'allowedOrigins'         => ['*'],
        'allowedOriginsPatterns' => [],
        'supportsCredentials'    => false,
        'allowedHeaders'         => ['*'],
        'allowedMethods'         => ['*'],
        'exposedHeaders'         => [],
        'maxAge'                 => 7200,
    ];
}
