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


	public function get_update_range($key,$begin,$range_flag,$supInfo){

		if($begin == -1){
			/**
			 * get begin by supInfo
			 */
			$count = 0 ;
			while(true){
				$detail = Redis::zRange($key,$count,$count);

				if($detail[0] == $supInfo){
					$begin = $count+1;
					break;
				}
				$count++;

			}
			if($begin == -1){
				return "";
			}
		}
		$res = [];

		$flag_count = 0;
		while(true){

			$detail = Redis::zRange($key,$begin,$begin);

			if($detail == null){
				break;
			}

			array_push($res,$detail[0]);

			if (strpos($detail[0], $range_flag) !== false) {

				$flag_count++;
				if($flag_count == 1){
			    	break;
				}
			}

			$begin++;



		}

		return $res;

	}



	public function get(){


		date_default_timezone_set('Asia/Shanghai');

		/** @var array redis kyes of fd */		
		$redis_fd = Redis::keys("friends*");
		sort($redis_fd);
		if($redis_fd == null){
			abort('redis::keys("friends*") return null');
		}

		/**
		 * set range from # to #
		 */
		$date = date("Y-m-d");

		if(!Storage::disk('local')->exists(explode("\\",__METHOD__)[3])){
			Storage::disk('local')->put(explode("\\",__METHOD__)[3],"");
		}
		$log = explode("\n",Storage::disk('local')->get(explode("\\",__METHOD__)[3]))[0]; // __CLASS__."@".__FUNCTION__

		if($log == "")
			$new_date = "" ;
		else
			$new_date = json_decode(explode("\n",$log)[0],true)[0];

		if($new_date != $date){

			if($new_date == ""){

				/** first message */
				$detail = $this->get_update_range($redis_fd[0],0,"---","");
				array_unshift($detail,$redis_fd[0]);


			}else{

				$log_arr = json_decode($log,true);

				$title = array_values(array_slice($log_arr, 1))[0];
				$last_line_title = array_values(array_slice($log_arr, -1))[0];
				$detail =  $this->get_update_range($title,-1,"---",$last_line_title);

				if($detail == []){
					return "FINE FIRST BLOOD, QUICK TO DEVP SECOND!";
				}


				array_unshift($detail,$title);

			}

			array_unshift($detail, $date);

			Storage::disk('local')->prepend(explode("\\",__METHOD__)[3], json_encode($detail,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));

		}else{

			$detail = json_decode($log,true);


		}

		return implode("\n",$detail);


	}



	public function store(){

		$url = "https://api.github.com/repos/NominationP/NominationP.github.io/git/trees/38a08631c3892937498148c6af30fdabfe54bd3a";
		$client = new Client();

		$res = $client->get($url,['auth' => ['NominationP', 'a873525']]);
		$github_tree = $res->getBody();
		$github_tree_arr = json_decode($github_tree,true);


		foreach ($github_tree_arr['tree'] as $key => $value) {

			$title = $value['path'];
			$url = $value['url'];
			$title = substr(explode("-",$title)[3], 0, -3);

			/** each del keys */
			Redis::del($title);

			$res = $client->get($url);
			$github_tree = $res->getBody();
			$github_tree_arr = json_decode($github_tree,true);

			$content = base64_decode($github_tree_arr['content']);
			$content = array_filter(explode("\n",$content));
			// $redis_content = Redis::zRange($title,0,-1);



			// return $redis_length = Redis::ZCARD($title);
			// return $content_length = count($content);


			foreach ($content as $key_line => $value_line) {

				if($key_line<20) continue;

				$detail = rtrim($value_line);

				// if($key_line == 32){
				// 	return $value_line;
				// }

				// if($detail == "- ch Sounds like a date to me")
				// 	return $key_line;

				if($detail==null) continue;

				if (strpos($detail, '---') !== false) {
					$detail = '--------DAILY :)DONE-----SEP'.$key_line;

					// Redis::zAdd($title,$key_line,$detail);die();

				}

				// if($key_line == 80) break;
				
				Redis::zAdd($title,$key_line,$detail);

			}


		}

	}


	public function get_info($content){

		Storage::disk('local')->prepend(explode("\\",__METHOD__)[3], json_encode($content,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));



		return "ok";
	}

}
