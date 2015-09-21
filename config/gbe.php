<?php

/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Additional Permission Factors
    |--------------------------------------------------------------------------
    |
    | Additional possible columns in the User model. Superuser has access to
    | everything. Non-confirmed users only have 0-level access regardless of
    | their access setting until they confirm.
    */

    'image_storage_filesystem' => 'local', // Options are 'local' or 's3' for now.

];