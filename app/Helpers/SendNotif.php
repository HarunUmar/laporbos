<?php
namespace App\Helpers;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Message\Topics;
use FCM;


class SendNotif{
 	
 	public static function sendNotifikasi($token, $pengirim, $pesan, $gambar){
 		
        $payload = array();
        $payload['team'] = 'indonesia';
        $payload['score'] = '5.6';

        $res = array();
        $res['data']['title'] = $pengirim;
        $res['data']['is_background'] = false;
        $res['data']['message'] = $pesan;
        $res['data']['image'] = asset('upload/images-400/'.$gambar);
        $res['data']['payload'] =  $payload;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
       
	    $optionBuiler = new OptionsBuilder();
	    $optionBuiler->setTimeToLive(60*20);

	    $notificationBuilder = new PayloadNotificationBuilder($pengirim);
	    $notificationBuilder->setBody($pesan)->setSound('default');

	    $dataBuilder = new PayloadDataBuilder();
	     $dataBuilder->addData($res);

	    $option = $optionBuiler->build();
	    $notification = $notificationBuilder->build();
	    $data = $dataBuilder->build();

	    $downstreamResponse = FCM::sendTo($token, $option, null, $data);  
    }

    public static function sendTopic($judul,$pesan,$city_id)
    {
        $notificationBuilder = new PayloadNotificationBuilder($judul);
        $notificationBuilder->setBody($pesan)
                            ->setSound('default');

        $notification = $notificationBuilder->build();

        $topic = new Topics();
        $topic->topic('kota'.$city_id);

        $topicResponse = FCM::sendToTopic($topic, null, $notification, null);

        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

     public static function sendTopicWithData($pengirim,$judul,$pesan,$gambar, $city_id)
    {
        $notificationBuilder = new PayloadNotificationBuilder($judul);
        $notificationBuilder->setBody($pesan)
                            ->setSound('default');

        $payload = array();
        $payload['team'] = 'indonesia';
        $payload['score'] = '5.6';

        $res = array();
        $res['data']['title'] = $judul;
        $res['data']['is_background'] = false;
        $res['data']['message'] = $pesan;
        $res['data']['image'] = asset('upload/images-400/'.$gambar);
        $res['data']['payload'] =  $payload;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        $topic = new Topics();
        $topic->topic('kota'.$city_id);


        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($pengirim);
        $notificationBuilder->setBody($pesan)->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
         $dataBuilder->addData($res);

        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $topicResponse = FCM::sendToTopic($topic, $option, null, $data);
        

        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }



    // pake ini di agenda dengan inovasi mo send token berdasarkan id User
  public static function sendTopicWithUserId($pengirim,$judul,$pesan,$gambar, $id_user)
    {
        $notificationBuilder = new PayloadNotificationBuilder($judul);
        $notificationBuilder->setBody($pesan)
                            ->setSound('default');

        $payload = array();
        $payload['team'] = 'indonesia';
        $payload['score'] = '5.6';

        $res = array();
        $res['data']['title'] = $judul;
        $res['data']['is_background'] = false;
        $res['data']['message'] = $pesan;
        $res['data']['image'] = asset('bukti/'.$gambar);
        $res['data']['payload'] =  $payload;
        $res['data']['timestamp'] = date('Y-m-d G:i:s');
        $topic = new Topics();
        $topic->topic('user'.$id_user);


        $optionBuiler = new OptionsBuilder();
        $optionBuiler->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($pengirim);
        $notificationBuilder->setBody($pesan)->setSound('default');

        $dataBuilder = new PayloadDataBuilder();
         $dataBuilder->addData($res);

        $option = $optionBuiler->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $topicResponse = FCM::sendToTopic($topic, $option, null, $data);
        

        $topicResponse->isSuccess();
        $topicResponse->shouldRetry();
        $topicResponse->error();
    }

}