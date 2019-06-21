<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class masalah extends Model
{
    //

     protected $table = 'masalah';
    protected $fillable = ['id','masalah','jabatan'];


    
    public function user(){
    	 return $this->belongsTo(User::class, 'user_id','id');

    }

}
