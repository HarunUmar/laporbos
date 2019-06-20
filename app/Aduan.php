<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aduan extends Model
{
    //
    

    protected $table = 'aduan';
    protected $fillable = ['id','judul','isi','lat','long','user_id','pelapor','masalah_id'];


    
    public function user(){
    	 return $this->belongsTo(User::class, 'user_id','id');

    }
}
