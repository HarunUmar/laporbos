<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Aduan extends Model
{
    //
    

	public $nilaiBalik = 1;
    protected $table = 'aduan';
    protected $fillable = ['id','judul','isi','lat','long','user_id','pelapor','masalah_id'];


    
    public function user(){
    	 return $this->belongsTo(User::class, 'user_id','id');

    }

    public function getCreatedAtAttribute(){
   		 return \Carbon\Carbon::parse($this->attributes['created_at'])->diffForHumans();
	}



    public function getLikeAttribute(){
   		
   			if(is_null($this->attributes['like'])){
   				$this->nilaiBalik = 0 ;
   			}
   			return $this->nilaiBalik;


   		
	}



}
