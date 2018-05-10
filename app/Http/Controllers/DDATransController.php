<?php



namespace App\Http\Controllers;
use App\DDATrans;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;
use Log;
use Illuminate\Support\Facades\Redis;

class DDATransController extends Controller
{

	public function __construct(){
		
		date_default_timezone_set('Asia/Shanghai');

	}

	public function test(){

		$this->create_image();
		print "<img src=image.png?".date("U").">";


// $this->TextToImage_my( $text='Helloooo! my unicode words:  ǩ Ƥ Ў  ض ط  Ⴓ ');
        // return \App::call('App\Http\Controllers\DDATransController@get');
        // return app('App\Http\Controllers\MailController')->html_email(["name"=>"yibo","content"=>"content","fromUserName"=>"fromUserName"]);
	}


	public	function  create_image(){
		        $im = @imagecreate(200, 200) or die("Cannot Initialize new GD image stream");
		        $background_color = imagecolorallocate($im, 255, 255, 0);  // yellow
		        imagepng($im,"image.png");
		        imagedestroy($im);
		}


	public function get(){

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

	public function index_update(){
		return Redis::zrange("dda_update",0,-1);
	}

	public function store_update(){

		$client = new Client();
		$res = $client->get('https://raw.githubusercontent.com/NominationP/NominationP.github.io/master/_posts/blog/2018-05-01-DDA-translate_log_update.md');
		$file = $res->getBody(); 
		$file_arr = explode("\n", $file);
		// return (json_encode($file_arr));


		$page = 0;
		$title = "";
		$count = 1;
		foreach ($file_arr as $line => $value) {
			if($line < 17) continue;
			
			$detail = rtrim($value);
			if($detail==null) continue;


			Redis::zAdd("dda_update",$line,$detail);
		}
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



	public function get_daily_update(){
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
				$detail = $this->get_update_range("dda_update",0,"#","");

			}else{

				$log_arr = json_decode($log,true);
				$last_line_title = array_values(array_slice($log_arr, -1))[0];
			 	$detail =  $this->get_update_range("dda_update",-1,"#",$last_line_title);
				array_unshift($detail,$last_line_title);

			}

			array_unshift($detail, $date);

			Storage::disk('local')->prepend(explode("\\",__METHOD__)[3], json_encode($detail,JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE));

		}else{

			$detail = json_decode($log,true);
		}

		/**
		 * process: change detail suit for wechat
		 */
		foreach ($detail as $key => &$value) {

			if (strpos($value, '##') !== false && $key == 1) {
				$value = str_replace(array('#'), '',$value);
				$value = "Section Title:".$value;
				continue;
			}

			if (strpos($value, '##') !== false && $key == count($detail)-1) {
				$value = str_replace(array('#'), '',$value);
				$value = "Next Section: ".$value;
				continue;
			}

			$value = explode(":*",$value);

			if(isset($value[1])){
				$value = $value[1];
			}else{
				$value = $value[0];
			}
		}


		return implode("\n",$detail);

		
	}


	public function get_update_title(){
			
		$title_arr = [];

		$detail = Redis::zRange("dda_update",0,-1);
		foreach ($detail as $key => $value) {

			if (strpos($value, '#') !== false) {
				array_push($title_arr, $value);
			}
		}
		return implode("\n",$title_arr);

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
