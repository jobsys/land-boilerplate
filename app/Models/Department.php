<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Permission\Traits\Authorisations;
use Modules\Starter\Entities\BaseModel;

class Department extends BaseModel
{
    use Authorisations;

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }


    public $appends = [
        'type'
    ];

    public function getTypeAttribute(): string
    {
        return 'department';
    }
}
