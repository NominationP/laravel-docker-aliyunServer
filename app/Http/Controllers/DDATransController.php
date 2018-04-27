<?php



namespace App\Http\Controllers;
use App\DDATrans;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Log;
use Illuminate\Support\Facades\Redis;

class DDATransController extends Controller
{

	public function test(){

        // return \App::call('App\Http\Controllers\DDATransController@get');
        return app('App\Http\Controllers\MailController')->html_email(["name"=>"yibo","content"=>"content","fromUserName"=>"fromUserName"]);
	}



	public function get(){
		date_default_timezone_set('Asia/Shanghai');
		$date = date("Y-m-d");
		$log = Storage::disk('local')->get(explode("\\",__METHOD__)[3]); // __CLASS__."@".__FUNCTION__
		if($log == "")
			$new_date = "";
		else
			$new_date = json_decode(explode("\n",$log)[0],true)[0];


		$flag = false;
		if($date!=$new_date){
			$flag = true;
		}

		// return Redis::set("ddaCount",0);

		if(Redis::exists("ddaCount") == 0){
			Redis::set("ddaCount",0);
		}else{
			if($flag)
				Redis::INCR("ddaCount");
		}   
		
 		$begin = Redis::get("ddaCount")*10;

		$detail = Redis::zRange("dda",$begin,$begin+9);
		array_unshift($detail, $date);

		if($flag)
			Storage::disk('local')->prepend(explode("\\",__METHOD__)[3], json_encode($detail,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));


		return implode("\n",$detail);


	}



	public function store(){

		$client = new Client();
		$res = $client->get('https://raw.githubusercontent.com/NominationP/NominationP.github.io/master/_posts/blog/2018-04-07-DDA_Translate_Log.md');
		$file = $res->getBody(); 
		$file_arr = explode("\n", $file);
		// print_r(json_encode($file_arr));


		$page = 0;
		$title = "";
		$count = 1;
		foreach ($file_arr as $line => $value) {
			if($line < 20) continue;
			
			$detail = rtrim($value);

			if($detail==null) continue;
			
			Redis::zAdd("dda",$line,$detail);
		}
	}


	public function get_info($content){

		Storage::disk('local')->prepend(explode("\\",__METHOD__)[3], json_encode($content,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));



		return "ok";
	}

}
