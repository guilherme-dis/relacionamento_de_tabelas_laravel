<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $fillable =['name','video'];
    use HasFactory;
    
    public function module()
    {
        return $this->belongsTo(Module::class);
    }
    public function comments(){
        return $this->morphMany(Comment::class,'commentable');
    }
}
