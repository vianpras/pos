<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Requistion extends Model
{
    //
    // if your key name is not 'id'
    // you can also set this to null if you don't have a primary key
    protected $primaryKey = 'code';

    public $incrementing = false;

    // In Laravel 6.0+ make sure to also set $keyType
    protected $keyType = 'string';
}
