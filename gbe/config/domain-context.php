<?php

/*
* This file is part of the DemocracyApps\domain-context package.
*
* Copyright 2015 DemocracyApps, Inc.
*
* See the LICENSE.txt file distributed with this source code for full copyright and license information.
*
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Home Domain
    |--------------------------------------------------------------------------
    |
    | This is the domain that contains all common routes as well as the ones
    | that may be mapped.
    |
    */
    'home_domain' => null,

    /*
    |--------------------------------------------------------------------------
    | Mapped Domains
    |--------------------------------------------------------------------------
    |
    | TBD. Should be able to do here as well as in DB (set type here and name of
    | table.
    */
    'mapped_domain_storage' => 'config', // Could be 'database'

    'mapped_domains' => [
        'example.com' => 1
    ],
];