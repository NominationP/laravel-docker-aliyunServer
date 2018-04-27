<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Mail;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class MailController extends Controller {
   public function basic_email(){
      $data = array('name'=>"Virat Gandhi");
   
      Mail::send(['text'=>'mail'], $data, function($message) {
         $message->to('605166577@qq.com', 'Tutorials Point')->subject
            ('Laravel Basic Testing Mail');
         $message->from('605166577@qq.com','Virat Gandhi');
      });
      echo "Basic Email Sent. Check your inbox.";
   }

   public function html_email($arr){
      $data = array('name'=>$arr['name'],'content'=>$arr['content']);
      Mail::send('mail', $data, function($message) {
         $message->to('605166577@qq.com', 'Tutorials Point')->subject
            ('Laravel HTML Testing Mail');
         $message->from('605166577@qq.com','AUT SENDER -- wx 一波一波一波一波一波');
      });

      Storage::disk('local')->prepend(explode("\\",__METHOD__)[3], json_encode($arr,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));

      return true;
   }
   
   public function attachment_email(){
      $data = array('name'=>"Virat Gandhi");
      Mail::send('mail', $data, function($message) {
         $message->to('abc@gmail.com', 'Tutorials Point')->subject
            ('Laravel Testing Mail with Attachment');
         $message->attach('C:\laravel-master\laravel\public\uploads\image.png');
         $message->attach('C:\laravel-master\laravel\public\uploads\test.txt');
         $message->from('xyz@gmail.com','Virat Gandhi');
      });
      echo "Email Sent with attachment. Check your inbox.";
   }
}