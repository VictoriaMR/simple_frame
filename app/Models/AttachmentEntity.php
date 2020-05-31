<?php

namespace App\Models;

use App\Models\Base;

class AttachmentEntity extends Base
{
    //表名
    protected $table = 'attachment_entity';

    //文件类型
    const TYPE_ADMIN_LOGIN_BACKGROUD = 1;

    public function getListByType($type = 0)
    {
        return $this->where('type', (int) $type)->get();
    }

    public function getListByTypeOne($type = 0)
    {
        return $this->where('type', (int) $type)->find();
    }
}
