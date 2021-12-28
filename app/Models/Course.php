<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable =['name','avalible'];
    public function modules(){
        return $this->hasMany(Module::class);
    }
    public function image()
    {
        return $this->morphOne(Image::class,'imageable');//imageable é o nome do relacionamento que se encontra na classe Image.
    }

    public function comments(){
        return $this->morphMany(Comment::class,'commentable');
    }
}
 