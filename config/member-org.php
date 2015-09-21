<?php

/*
* This file is part of the DemocracyApps\member-org package.
*
* Copyright 2015 DemocracyApps, Inc.
*
* See the LICENSE.txt file distributed with this source code for full copyright and license information.
*
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Permission Levels
    |--------------------------------------------------------------------------
    |
    | Each organization user is assigned a permission level (by default, 0-9)
    | that may be used to control access to data or actions. Access is strictly
    | increasing. A resource or action requiring level 5 is accessible to any user
    | with access level 5 or higher.
    |
    */
    'max_permission_level' => 9,

    /*
    |--------------------------------------------------------------------------
    | Additional Permission Factors
    |--------------------------------------------------------------------------
    |
    | Additional possible columns in the User model. Superuser has access to
    | everything. Non-confirmed users only have 0-level access regardless of
    | their access setting until they confirm.
    */

    'user_implements_superuser' => true,
    'user_superuser_column' => 'superuser',

    'user_implements_confirmation' => true,
    'user_confirmation_column' => 'verified',
    'user_confirmation_required_threshold' => 0,

    'member_class_suffix' => 'User',

];