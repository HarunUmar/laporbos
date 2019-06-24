<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use App\User;
use JWTAuth;
use Auth;

class UserController extends Controller
{
    public function register(Request $request){
        $req = $request->all();
        $messsages = ['name.required' => 'name Tidak Bisa Kosong',
                    'no_hp.required'=>'no_hp Tidak Bisa Kosong',
                    'no_hp.unique' => 'no_hp Sudah Digunakan',
                    'email.required'=> 'email Tidak Bisa Kosong',
                    'email.unique'=> 'email Sudah Digunakan',
                    'lat.required' => 'lat disertakan',
                    'long.required' => 'long disertakan',
                    'password.required'=> 'password Tidak Bisa Kosong',
                    
                   ];

        $rules = ['name' => 'required',
                'no_hp' => 'required|unique:users',
                'email' => 'required|unique:users',
                'lat' => 'required',
                'long' => 'required',
                'password' => 'required',
               ];
        

      $validator = Validator::make($request->all(), $rules,$messsages);
      if($validator->fails()){
          $success = 0;
          $msg = $validator->messages()->all();
          $response = $msg;
      }else{
        
       


        $req['role'] = "3"; // default masyarakat
        $req['password']= bcrypt($req['password']);

        $req['img'] = "-";

        $register = User::create($req);
        if($register){
    
          $success = 1;
          // $token = JWTAuth::fromUser($register);
          $msg = User::findOrFail($register->id);
          $response = [
              "success" => $success,
              "user_id" => $register['id'],
              // "key" => "Bearer {$token}",
              "msg" => $msg,
          ];
          // if(isset($req['token'])){
          //    User::where('id',$register['id'])->update(['token' => $req['token'] ]);
          // }

        }else{
          $success = 0;
          $msg = 'Gagal Registrasi';
          $response = [
              "success" => $success,
              "msg" => $msg,
          ];
        } 
      }
        return response()->json($response);
   }


   

   public function login(Request $request){
        $req = $request->all();
        $messsages = array( 
                            'no_hp.required'=>'no_hp Harus Diisi',
                            'password.required'=>'password Harus Diisi',
                           );   

        $rules = array( 'no_hp' => 'required',
                        'password' => 'required',
                      );

        $validator = Validator::make($request->all(), $rules,$messsages);
        if($validator->fails()){
            $success = 0;
            $msg = $validator->messages()->all();
            $response = $msg;
        }else{
            $token = JWTAuth::attempt(['no_hp' => $request->no_hp, 'password' => $request->password]);
            if($token){
                $user = Auth::user()->where('no_hp',$request->no_hp)->first();
                if($user->role == 3){
                  $success = 1;
                  $msg = $user;
                  // $token = JWTAuth::fromUser($user);

                  $response = [
                      "success" => $success,
                       "token" => $token,
                      "msg" => $msg,
                      // "key" => "Bearer {$token}",
                  ];
                  
                  // if(isset($req['token'])){
                  //   $user->update(['token_gcm' => $req['token']]);
                  // }
                  
                }else{
                  $success = 0;
                  $msg = "Hanya Untuk User";
                  $response = [
                      "success" => $success,
                     
                      "msg" => $msg,
                  ];
                }
                
            }else{
                $success = 0;
                $msg = "Gagal Login";
                $response = [
                      "success" => $success,
                      "msg" => $msg,
                  ];
            } 
        }
        return response()->json($response);
   }

   public function UpdatePassword(Request $request)
   {
      $req = $request->all();
      $messsages = array( 'user_id.required' => 'user_id Harus Diisi',
                          'password.required' => 'password Harus Diisi',
                         );   

      $rules = array( 'user_id' => 'required',
                      'password' => 'required',
                    );

      $validator = Validator::make($req, $rules,$messsages);
      if($validator->fails()){
          $success = 0;
          $msg = $validator->messages()->all();
      }else{
          $find = User::findOrFail($req['user_id']);
          $find->update(['password' => bcrypt($req['password'])]);
          $success = 1;
          $msg = "Berhasil Ganti Password";
      }

      return response()->json(['success'=> $success,'msg'=>$msg]);
   }


   public function UpdateImage(Request $request)
   {
      $req = $request->all();
      $messsages = array( 
                          'user_id.required' => 'user_id Harus Diisi',
                          'gambar.required' => 'gambar Harus Diisi',
                          'gambar.mimes' => 'gambar Harus Extensi Harus jpg,jpeg,png',
                         );   

      $rules = array( 'user_id' => 'required',
                      'gambar' => 'required|mimes:jpg,jpeg,png',
                    );

      $validator = Validator::make($request->all(), $rules,$messsages);
      if($validator->fails()){
          $success = 0;
          $msg = $validator->messages()->all();
      }else{
        $user = User::findOrFail($req['user_id']);
        $file = $request->file('gambar');
        $nama_file = time()."_".$file->getClientOriginalName();
                // isi dengan nama folder tempat kemana file diupload
        $tujuan_upload = 'users';
        $file->move($tujuan_upload,$nama_file);


        $update = $user->update(['img' => $nama_file]);
        if($update){
          $success = 1;
          $msg =  $nama_file;
        }else{
          $success = 0;
          $msg = "Gagal Update Gambar";
        }
      }
      return response()->json(['success'=> $success,'msg'=>$msg]);
   }



   public function updateAlamat(Request $request)
   {
      $req = $request->all();
      $messsages = array( 
                          'user_id.required' => 'user_id Harus Diisi',
                          'lat.required' => 'lat Harus Diisi',
                          'long.required' => 'long Harus Diisi',
                          'detail_alamat' => 'detail_alamat Harus Diisi' 
                         );   

      $rules = array( 'user_id' => 'required',
                      'lat' => 'required',
                      'long' => 'required',
                      'detail_alamat' => 'required'
                    );

      $validator = Validator::make($request->all(), $rules,$messsages);
      if($validator->fails()){
          $success = 0;
          $msg = $validator->messages()->all();
      }else{
        $user = User::findOrFail($req['user_id']);
        $update = $user->update(['lat' => $req['lat'], 'long' => $req['long'], 'detail_alamat' => $req['detail_alamat'] ]);
        if($update){
          $success = 1;
          $msg =  "Berhasil Update Alamat";
        }else{
          $success = 0;
          $msg = "Gagal Update Alamat";
        }
      }
      return response()->json(['success'=> $success,'msg'=>$msg]);
   }

  

    public function showProfil($id)
    {
      
            $select = User::findOrFail($id);
            $success = 1;
            $msg = $select;
            $kr = 200;
      
       return response()->json(['success' => $success, 'msg' => $msg], $kr);
    }
    
  public function listPenerima($page, $dataPerpage)
    {
        
       $offset = ($page - 1) * $dataPerpage;
       $query = User::leftJoin('masalah as a','a.user_id','=','users.id')
                        ->select('users.id',
                          'users.name',
                          'users.img',
                          'a.jabatan')
                        ->orderBy('users.id','DESC')
                        ->where('users.role',2)
                        ->limit($dataPerpage)->offset($offset)
                        ->get();

        $jumdat = User::count();

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
}

	

  



    
