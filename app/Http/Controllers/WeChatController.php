<?php

namespace App\Http\Controllers;
use EasyWeChat\Factory;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Kernel\Messages\Text;
use EasyWeChat\Kernel\Messages\News;
use EasyWeChat\Kernel\Messages\NewsItem;
use EasyWeChat\Kernel\Messages\Article;
use EasyWeChat\Kernel\Messages\Raw;
use EasyWeChat\Kernel\Messages\Media;

use Illuminate\Support\Facades\Storage;

use GuzzleHttp\Client;
use Log;

class WeChatController extends Controller
{

    public $app;

    public function construct(){
        $this->app = app('wechat.official_account');
    }

    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function serve()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');


        $app->server->push(function ($message) {

            $fix_return =    '回复 1 获取今日单词'."\n"
                            .'回复 2 获取今日老友'."\n"
                            .'回复 3 获取DDALOG'."\n"
                            ."\n".'有什么想获取的内容吗,说说看,请以"get"为开头,结尾附上邮箱地址,我会实时收到哦 格式为 "email:XXXXX@qq.com"';

            switch ($message['MsgType']) {
                case 'event':
                    return $fix_return;
                    break;
                case 'text':

                    $content = $message['Content'];   
                    $createTime = $message['CreateTime']; 
                    $fromUserName = $message['FromUserName']; 
                    $date_fromat = date('Y-m-d H:i:s', $createTime);  
                    if($content == 1){
                        return $detail = \App::call('App\Http\Controllers\DDATransController@get');
                    }
                    elseif($content == 2){
                        return $detail = \App::call('App\Http\Controllers\FriendsController@get');
                    }

                    elseif($content == 3){
                        return \App::call('App\Http\Controllers\DDATransController@get_daily_update');
                    }














                    elseif(substr($content,0,3)=="get"){

                        $email = array_values(array_slice(explode("email:",$content), -1))[0];

                        $flag = app('App\Http\Controllers\MailController')->html_email(["name"=>"yibo","content"=>$content,"fromUserName"=>$fromUserName]);

                        if($flag){

                            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                                return "Got it ! Reply to you soon ! :)";
                            }else{
                                return "Got it ! but email is invalid, so I cant reply you :("; 
                            }
                        }else{
                            return "error please contack me !!!";
                        }
                       


                        // return "OK";
                    }
                    else{
                        return $fix_return;
                    }

                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }

            // ...
        });

        return $app->server->serve();

    }


    function menu_list(){
        $app = app('wechat.official_account');

        $current = $app->menu->list();
        return print_r($current,true);
    }

    function menu_set(){
        $buttons = [
            [
                "type" => "click",
                "name" => "今日歌曲",
                "key"  => "V1001_TODAY_MUSIC"
            ],
            [
                "name"       => "菜单",
                "sub_button" => [
                    [
                        "type" => "view",
                        "name" => "搜索",
                        "url"  => "http://www.soso.com/"
                    ],
                    [
                        "type" => "view",
                        "name" => "视频",
                        "url"  => "http://v.qq.com/"
                    ],
                    [
                        "type" => "click",
                        "name" => "赞一下我们",
                        "key" => "V1001_GOOD"
                    ],
                ],
            ],
        ];

        $matchRule = [
            "tag_id" => "2",
            "sex" => "1",
            "country" => "中国",
            "province" => "广东",
            "city" => "广州",
            "client_platform_type" => "2",
            "language" => "zh_CN"
        ];

        $app = app('wechat.official_account');

        $app->menu->create($buttons, $matchRule);
    }


    /**
     * 处理微信的请求消息
     *
     * @return string
     */
    public function server1()
    {
        Log::info('request arrived.'); # 注意：Log 为 Laravel 组件，所以它记的日志去 Laravel 日志看，而不是 EasyWeChat 日志

        $app = app('wechat.official_account');
        $app->server->setMessageHandler(function ($message) {
            return "您好！欢迎关注我!";
        });
        return $response = $app->server->serve();
        $app->server->setMessageHandler(function ($message) {
            switch ($message->MsgType) {
                case 'event':
                    return '收到事件消息';
                    break;
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
        });

        return $app->server->serve();
    }

//         $app->server->push(function($message){

//             switch ($message['MsgType']) {


//                 case 'event':
//                     return '收到事件消息';
//                     break;
//                 case 'text':
                
//                 return $text = new Text('您好！overtrue。');

// $text_xml = '<xml><ToUserName><![CDATA[ogq-sw7qhbX_2pDucLvZvpMzGt_E]]></ToUserName>
// <FromUserName><![CDATA[gh_850c879e4fcd]]></FromUserName>
// <CreateTime>1523576680</CreateTime>
// <MsgType><![CDATA[text]]></MsgType>
// <Content><![CDATA[1ddd]]></Content>
// </xml>';

// $article_xml = '<xml>
//     <ToUserName>
//         <![CDATA[ogq-sw7qhbX_2pDucLvZvpMzGt_E]]>
//     </ToUserName>
//     <FromUserName>
//         <![CDATA[gh_850c879e4fcd]]>
//     </FromUserName>
//     <CreateTime>12345678</CreateTime>
//     <MsgType>
//         <![CDATA[news]]>
//     </MsgType>
//     <ArticleCount>1</ArticleCount>
//     <Articles>
//         <item>
//             <Title>
//                 <![CDATA[title1]]>
//             </Title>
//             <Description>
//                 <![CDATA[description1]]>
//             </Description>
//             <PicUrl>
//                 <![CDATA[picurl]]>
//             </PicUrl>
//             <Url>
//                 <![CDATA[url]]>
//             </Url>
//         </item>
//     </Articles>
// </xml>';


// $new_xml ='<xml><MsgType><![CDATA[news]]></MsgType><ArticleCount>1</ArticleCount>
// <Articles>
// <item>
//     <Title><![CDATA[你皮任你皮]]></Title>
//     <Description><![CDATA[...]]></Description>
//     <Url><![CDATA[https://nominationp.github.io/life/body/]]></Url>
//     <PicUrl><![CDATA[http://mpic.tiankong.com/cff/59a/cff59afd76785980fadad5612f13afb1/640.jpg]]></PicUrl>
// </item>
// </Articles>
// <ToUserName><![CDATA[ogq-sw7qhbX_2pDucLvZvpMzGt_E]]></ToUserName>
// <FromUserName><![CDATA[gh_850c879e4fcd]]></FromUserName>
// <CreateTime>1523577649</CreateTime>
// </xml>';

// // return $message = new Raw($article_xml);


// $items = [
//     new NewsItem([
//         'title'       => "你皮任你皮",
//         'description' => '...',
//         'url'         => "https://nominationp.github.io/life/body/",
//         'image'       => "http://mpic.tiankong.com/cff/59a/cff59afd76785980fadad5612f13afb1/640.jpg",
//         // ...
//     ])
// ];       
//  $news = new News($items);
// return ($news);
//                 $article = new Article([
//                         'title'   => 'EasyWeChat',
//                         'author'  => 'overtrue',
//                         'content' => 'EasyWeChat 是一个开源的微信 SDK，它... ...',
//                         // ...
//                     ]);
//                 return $article;
                







//         // return $text = new Text('您好！overtrue。');
//         $news = new News([
//         'title'       => "你皮任你皮",
//         'description' => '杰哥教学',
//         'url'         => "https://nominationp.github.io/life/body/",
//         'image'       => "http://mpic.tiankong.com/cff/59a/cff59afd76785980fadad5612f13afb1/640.jpg",
//         // ...
//         ]);

//         $items = [
//             new NewsItem([
//                 'title'       => "你皮任你皮",
//                 'description' => '...',
//                 'url'         => "https://nominationp.github.io/life/body/",
//                 'image'       => "http://mpic.tiankong.com/cff/59a/cff59afd76785980fadad5612f13afb1/640.jpg",
//                 // ...
//             ])
//         ];       
//         return $news = new News($items);
//         return $text;

//                 // $news = new News();
//                 // $news->title = 'EasyWeChat';
//                 // $news->description = '微信 SDK ...';

//                 // return $news;

//                 // $news = new News([
//                 //         'title'       => $title,
//                 //         'description' => '...',
//                 //         'url'         => $url,
//                 //         'image'       => $image,
//                 //         // ...
//                 //     ]);



//                 // $article = new Article();
//                 // $article->title   = 'EasyWeChat';
//                 // $article->author  = 'overtrue';
//                 // $article->content = '微信 SDK ...';

//                     // return $article;
//                     // return '收到文字消息';
//                     break;
//                 case 'image':
//                     return '收到图片消息';
//                     break;
//                 case 'voice':
//                     return '收到语音消息';
//                     break;
//                 case 'video':
//                     return '收到视频消息';
//                     break;
//                 case 'location':
//                     return '收到坐标消息';
//                     break;
//                 case 'link':
//                     return '收到链接消息';
//                     break;
//                 // ... 其它消息
//                 default:
//                     return '收到其它消息';
//                     break;
//             }
//         });



    public function user_list(){
// return $text = new Text(['content' => '您好！overtrue。']);
                $text = new Text('您好！overtrue。');
                return print_r($text,true);

    //     $client = new Client();
    // $res = $client->get('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=wx1a15d1b0218f14cb&secret=5f5b2d86ac46bab834641d2826b91a95');
    // return $file = $res->getBody(); 
        $items = [
            new NewsItem([
                'title'       => "你皮任你皮",
                'description' => '...',
                'url'         => "https://nominationp.github.io/life/body/",
                'image'       => "http://mpic.tiankong.com/cff/59a/cff59afd76785980fadad5612f13afb1/640.jpg",
                // ...
            ])
        ];       
         $news = new News($items);
        return print_r($news,true);
        $title = "easywechat";
        $url = 'https://nominationp.github.io/life/body/"';
        $image = "http://mpic.tiankong.com/cff/59a/cff59afd76785980fadad5612f13afb1/640.jpg";

                // $article = new Article([
                //         'title'   => 'EasyWeChat',
                //         'author'  => 'overtrue',
                //         'content' => 'EasyWeChat 是一个开源的微信 SDK，它... ...',
                //         // ...
                //     ]);
                // return $article;




                    // return (array)$article;
        $text = new Text('您好！overtrue。');
        $news = new News([
        'title'       => "你皮任你皮",
        'description' => '杰哥教学',
        'url'         => "https://nominationp.github.io/life/body/",
        'image'       => "http://mpic.tiankong.com/cff/59a/cff59afd76785980fadad5612f13afb1/640.jpg",
        // ...
        ]);
        // $app =? $user_list;
        // 
        return $news;
        
        return var_dump($news)."\br".var_dump($article);
        
    }
}