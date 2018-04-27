<?php



namespace App\Http\Controllers;
use App\DDATrans;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Log;
use Illuminate\Support\Facades\Redis;

class FriendsController extends Controller
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

		$url = "https://api.github.com/repos/NominationP/NominationP.github.io/git/trees/38a08631c3892937498148c6af30fdabfe54bd3a";
		$client = new Client(['auth' => ['NominationP', 'a873525']]);

		$res = $client->get($url);
		$github_tree = $res->getBody();
		$github_tree_arr = json_decode($github_tree,true);


		foreach ($github_tree_arr['tree'] as $key => $value) {

			$title = $value['path'];
			$url = $value['url'];
			$title = substr(explode("-",$title)[3], 0, -3);

			$res = $client->get($url);
			$github_tree = $res->getBody();
			$github_tree_arr = json_decode($github_tree,true);

			$content = base64_decode($github_tree_arr['content']);
			$content = array_filter(explode("\n",$content));
			$redis_content = Redis::zRange($title,0,-1);



			// return $redis_length = Redis::ZCARD($title);
			// return $content_length = count($content);


			foreach ($content as $key_line => $value_line) {

				if($key_line<20) continue;

				$detail = rtrim($value_line);

				if($key_line == 31){
					return $value_line;
				}

				// if($detail == "- ch Sounds like a date to me")
				// 	return $key_line;

				if($detail==null) continue;
				
				Redis::zAdd($title,$key_line,$detail);

			}

			return ;


		}

	}


	public function get_info($content){

		Storage::disk('local')->prepend(explode("\\",__METHOD__)[3], json_encode($content,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));



		return "ok";
	}

}
