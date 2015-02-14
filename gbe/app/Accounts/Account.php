<?php namespace DemocracyApps\GB\Accounts;


use DemocracyApps\GB\Utility\EloquentPropertiedObject;

class Account extends EloquentPropertiedObject {
    const UNKNOWN   = 0;
    const REVENUE   = 1;
    const EXPENSE   = 2;
    const ASSET     = 3;
    const LIABILITY = 4;
    const EQUITY    = 5;
    const CONTRA    = 6;

}