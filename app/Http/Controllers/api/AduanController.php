<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\Aduan;
use App\bukti;

class AduanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($page, $dataPerpage)
    {
        
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
                                 'e.id as id_penerima',
                                 'e.name'
                             )
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



    public function detailAduan($id){

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
                                 'e.id as id_penerima',
                                 'e.name'
                             )
                        ->where('aduan.id','=',$id)
                        ->get();

                        $success = 1;
                         return response()->json(['success' => $success,'data' => $query], 200);
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

}
