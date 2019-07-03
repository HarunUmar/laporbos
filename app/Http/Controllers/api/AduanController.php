<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Aduan;
use App\bukti;
use App\Love;
use App\Masalah;
use DB;
use App\User;
use App\Helpers\SendNotif;
use Illuminate\Support\Facades\Cache;

class AduanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page, $dataPerpage,$user_id)
    {
        
       $offset = ($page - 1) * $dataPerpage;
       $query = Aduan::join('users as a','a.id','=','aduan.user_id')
                        ->leftjoin('bukti as b','b.aduan_id','=','aduan.id')
                        ->Join('masalah as c','c.id','=','aduan.masalah_id')
                        ->join('users as e','e.id','=', 'c.user_id')
                        ->leftjoin('love as d','d.aduan_id','=','aduan.id')
                        ->select('a.name as pelapor',
                                 'a.id as id_pelapor',
                                 'b.url',
                                 'aduan.id as id_aduan',
                                 'judul',
                                 'isi',
                                 'aduan.lat',
                                 'aduan.long',
                                 'aduan.created_at',
                                 'c.masalah',
                                 'aduan.status',
                                 'e.id as id_penerima',
                                 'e.name',
                                 'a.img',
                                 'c.jabatan',
                                  DB::raw("count(d.id) as love"),
                                  DB::raw("(SELECT id FROM `love`WHERE `user_id` = ".$user_id." AND `aduan_id` = aduan.id) as `like`")

                             )
                        ->groupBy('aduan.id')
                        ->orderBy('aduan.id','DESC')
                        ->limit($dataPerpage)->offset($offset)
                        ->get();

        $jumdat = Aduan::count();

         $jumHal = ceil($jumdat / $dataPerpage);
         $pageSaatIni = (int) $page;
         $pageSelanjutnya = $page+1;
          if($pageSaatIni == $jumHal){
             $tampilPS = 0;
          }else{
             $tampilPS = $pageSelanjutnya;
          }
          $success = 1;
        return response()->json(['success' => $success,'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'data' => $query], 200);
     
    }



    public function detailAduan($id,$user_id){

            $query = Aduan::join('users as a','a.id','=','aduan.user_id')
                        ->leftjoin('bukti as b','b.aduan_id','=','aduan.id')
                        ->Join('masalah as c','c.id','=','aduan.masalah_id')
                        ->join('users as e','e.id','=', 'c.user_id')
                        ->leftjoin('love as d','d.aduan_id','=','aduan.id')
                        ->select('a.name as pelapor',
                                 'a.id as id_pelapor',
                                 'b.url',
                                 'aduan.id as id_aduan',
                                 'judul',
                                 'isi',
                                 'aduan.lat',
                                 'aduan.long',
                                 'aduan.created_at',
                                 'c.masalah',
                                 'aduan.status',
                                 'e.id as id_penerima',
                                 'e.name',
                                 'a.img',
                                 'c.jabatan',
                                  DB::raw("count(d.id) as love"),
                                  DB::raw("(SELECT id FROM `love`WHERE `user_id` = ".$user_id." AND `aduan_id` = ".$id.") as `like`")

                             )
                        ->groupBy('aduan.id')
                        ->where('aduan.id','=',$id)
                        ->get();

                        $success = 1;
                         return response()->json(['success' => $success,'data' => $query], 200);
    }


    public function myAduan($id,$page,$dataPerpage){


        $offset = ($page - 1) * $dataPerpage;
       $query = Aduan::join('users as a','a.id','=','aduan.user_id')
                        ->leftjoin('bukti as b','b.aduan_id','=','aduan.id')
                        ->Join('masalah as c','c.id','=','aduan.masalah_id')
                        ->join('users as e','e.id','=', 'c.user_id')
                        ->select('a.name as pelapor' ,
                                 'a.id as id_pelapor',
                                 'b.url',
                                 'aduan.id as id_aduan',
                                 'judul',
                                 'isi',
                                 'aduan.lat',
                                 'aduan.long',
                                 'c.masalah',
                                 'aduan.status',
                                 'aduan.created_at',
                                 'e.id as id_penerima',
                                 'e.name'
                             )
                        ->where('a.id',$id)
                        ->orderBy('aduan.id','DESC')
                        ->limit($dataPerpage)->offset($offset)
                        ->get();

        $jumdat = Aduan::count();

         $jumHal = ceil($jumdat / $dataPerpage);
         $pageSaatIni = (int) $page;
         $pageSelanjutnya = $page+1;
          if($pageSaatIni == $jumHal){
             $tampilPS = 0;
          }else{
             $tampilPS = $pageSelanjutnya;
          }
          $success = 1;
        return response()->json(['success' => $success,'pageSaatIni' => $pageSaatIni, 'pageSelanjutnya' => $tampilPS, 'data' => $query], 200);

    }



    public function ubahStatus(Request $request){

          $req = $request->all();
        $messsages = ['aduan_id.required' => 'aduan_id Tidak Bisa Kosong',];
        $rules = ['aduan_id' => 'required',];
        

      $validator = Validator::make($request->all(), $rules,$messsages);
      if($validator->fails()){
          $success = 0;
          $msg = $validator->messages()->all();
          $response = $msg;
      }else{

           
          $success = 1;
          $msg= Aduan::where('id',$req['aduan_id'])->update(['status' => $req['status']]);
          $response = [
              "success" => $success,
              "msg" => $msg,
          ];

      }

      return response()->json($response);
    }


    public function aduanDiTerima(Request $request){

        $req = $request->all();
    $query = Aduan::join('users as a','a.id','=','aduan.user_id')
                        ->leftjoin('bukti as b','b.aduan_id','=','aduan.id')
                        ->Join('masalah as c','c.id','=','aduan.masalah_id')
                        ->join('users as e','e.id','=', 'c.user_id')
                        ->select('a.name as pelapor' ,
                                 'a.id as id_pelapor',
                                 'b.url',
                                 'aduan.id as id_aduan',
                                 'judul',
                                 'isi',
                                 'aduan.lat',
                                 'aduan.long',
                                 'c.masalah',
                                 'aduan.status',
                                 'aduan.created_at',
                                 'e.id as id_penerima',
                                 'e.name',
                                 'e.no_hp as hp'
                             )
                        ->where('aduan.id',$req['aduan_id'])
                        ->orderBy('aduan.id','DESC')
                        ->get();



    $hp = $query[0]->hp;

    $pesan = "Izin Bapak/Ibu ".$query[0]->name;
    $pesan .= "\n Saudara ".$query[0]->pelapor. " Membuat pengaduan";
    $pesan .= "\n Judul : ".$query[0]->judul;
    $pesan .= "\n Pesan : ".$query[0]->isi;
    $pesan .= "\n Pesan : http://suaratech.com/laporbos/bukti/".$query[0]->url;
    $pesan .= "\n Lokasi : https://maps.google.com/maps?daddr=".$query[0]->lat.",".$query[0]->long;
    $pesan .= "\n --------------------------------------------------";
    $pesan .= "\n Mohon Tanggapannya dengan membalas angka 1 yang berarti anda telah meresponnya";
    $q = SendNotif::sendNotifWa($hp,$pesan);
    

    }


    public function tambahLove(Request $request){
          $req = $request->all();
          $query = Love::where('aduan_id',$req['aduan_id'])->where('user_id',$req['user_id'])->count();

          if($query <= 0){
              $req = $request->all();
             Love::create($req);
             $this->invalidateCache();
          }
         // return $query;
         
        

    }

    public function kurangLove(Request $request){
         $req = $request->all();
         $this->invalidateCache();
        Love::where('user_id',$req['user_id'])->where('aduan_id',$req['aduan_id'])->delete();
    }





    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //

      $req = $request->all();
      $req['waktu_buat'] = date('Y-m-d H:i:s');

      $messsages = ['judul.required' => 'judul Tidak Bisa Kosong',
                       'isi.required' => 'isi Tidak Bisa Kosong',
                       'lat.required' => 'lat Tidak Bisa Kosong',
                       'long.required' => 'long Tidak Bisa Kosong',
                       'bukti.required' => 'bukti Tidak Bisa Kosong',
                       'masalah_id.required' => 'masalah tidak bisa kosong',

                      ];
      $rules = [ 'judul' => 'required',
                    'isi' => 'required',
                    'lat' => 'required',
                    'long' => 'required',
                    'bukti' => 'required|file|image|mimes:jpeg,png,jpg|max:2048',
                    'masalah_id' =>'required',
                  ];
       $validator = Validator::make($req, $rules,$messsages);
       if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $kr = 400;
       }else{
                 

                

                    $insert = Aduan::create($req);
                     $file = $request->file('bukti');
 
                    $nama_file = time()."_".$file->getClientOriginalName();
 
                // isi dengan nama folder tempat kemana file diupload
         $tujuan_upload = 'bukti';
        $file->move($tujuan_upload,$nama_file);
 
        bukti::create([
            'url' => $nama_file,
            'aduan_id' => $insert['id'],
        ]);

                    $success = 1;
                    $msg = "Berhasil Mengirim Laporan";
                    $kr = 200;
          
           
       }
       return response()->json(['success' => $success, 'msg' => $msg], $kr);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }


    public function daftarMasalah(){

      $query = Masalah::select('id','masalah','jabatan')->get();

      return response()->json(['success' => 1, 'msg' => $query], 200);

    }


    public function sendNotif(){

        $q = SendNotif::sendNotifWa("082343965747","ini dari localhost");
 	
    }

     protected function invalidateCache()
    {
        // Cache::get('kapal')->flush();
         Cache::tags('aduan')->flush();
    }


    public function responGagal($kodeId, $id_aduan){
        if($kodeId == 2){
            return "anda harus melakukan konfirmasi aduan, dengan cara membalas chat ini, ".$id_aduan."#2";
        } else if($kodeId == 3){
            return "Silahkan Selesaikan Aduan ini dengan cara membalas chat ini, ".$id_aduan."#3";
        }else{
            return "Terima kasih telah menyelesaikan aduan ini ";
        }
       
    }

    public function responSukses($kodeId, $id_aduan){
        if($kodeId == 2){
            return "terima kasih telah melakukan konfirmasi aduan ini , jika aduan ini telah selesai, silahkan membalas chat ini dengan , ".$id_aduan."#3";
        } else if($kodeId == 3){
            return "terima kasih aduan ini telah selesai, sukses selalu untuk anda ";
        }
       
    }

    public function callBack(Request $request){


       // 3#ok = untuk selesai
       // 3#siap = proses


    
        $pesan = "";
        $req =  $request->post();


        if(strpos($req['message'],"#")) {

        $noHp = str_replace("62","0",$req['phone']);
        $user = User::where('no_hp',$noHp)->first();
        
    
        if(!empty($user)){
            
            $pes = strtoupper($req['message']);
            $pecah = explode('#',$pes);
            $id_di_db = Aduan::where('id',$pecah[0])->where('user_id',$user->id)->first();
            if(!empty($id_di_db)){

                $nilai_next = $id_di_db->status + 1;
       
            if($id_di_db->status > $pecah[1]){
                 $pesan =  $this->responGagal($nilai_next,$id_di_db->id);
                 
            }
            else if($id_di_db->status == $pecah[1] ){
                $pesan =  $this->responGagal($nilai_next,$id_di_db->id);
                
            } 
            else if($nilai_next != $pecah[1]){
                $pesan =  $this->responGagal($nilai_next,$id_di_db->id);
            }
            else{
             if($nilai_next == $pecah[1] and $pecah[1] <=3 ){
                 $this->invalidateCache();
                 Aduan::where('id',$id_di_db->id)->update(['status' => $pecah[1]]);
                 $pesan = $this->responSukses($nilai_next,$id_di_db->id);
                 }    
             }
            
            } else{

                $pesan = "anda tidak punya otoritasi untuk melakukan oprasi ini";
            }
            
          
            
        }else{
            $pesan = "anda tidak terdaftar";
        }
    
        
        }
        return response($pesan, 200)->header('Content-Type', 'text/plain');
    }

}
