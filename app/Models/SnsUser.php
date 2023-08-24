<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Starter\Entities\BaseModel;

class SnsUser extends BaseModel
{

    protected $casts = [
        'bound_at' => 'datetime'
    ];


    public function snsable(): MorphTo
    {
        return $this->morphTo();
    }
}
