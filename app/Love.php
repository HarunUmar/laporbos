<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Love extends Model
{
    //
    
    protected $table = 'love';
    protected $fillable = ['id','user_id','aduan_id'];
}
