<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class bukti extends BaseEloquent 
{
    //

    protected $table = 'bukti';
    protected $fillable = ['id','url','aduan_id'];

}
