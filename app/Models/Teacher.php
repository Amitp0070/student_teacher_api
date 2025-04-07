<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = ['name']; // 👈 ye line add karo
    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
