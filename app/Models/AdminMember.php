<?php

namespace App\Models;

use App\Models\MemBer as BaseModel;

class AdminMember extends BaseModel
{
    //表名
    public $table = 'admin_member';

    //主键
    protected $primaryKey = 'mem_id';
}