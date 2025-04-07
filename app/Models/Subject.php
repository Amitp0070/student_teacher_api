<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['title', 'teacher_id']; // 👈 ye line add karo

    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

}
