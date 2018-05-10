<?php
namespace App\Http\Controllers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Intervention\Image\Facades\Image;
require_once('../../vendor/autoload.php');
class PhotoController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function index() {

    $bg = 'images/origin.jpg';
    $overlay = './img/paw.png';
    $image = new Image();
    $image->setDimensionsFromImage($bg);
    $image->draw($bg);
    $image->draw($overlay, '50%', '75%');
    $image->rectangle(40, 40, 120, 80, array(0, 0, 0), 0.5);
    $image->setFont('./font/arial.ttf');
    $image->setTextColor(array(255, 255, 255));
    $image->setStrokeWidth(1);
    $image->setStrokeColor(array(0, 0, 0));
    $image->text('Hello World!', array('fontSize' => 12, 'x' => 50, 'y' => 50));
    $image->text('This is a big sentence with width 200px', array(
        'fontSize' => 60, // Desired starting font size
        'x' => 300,
        'y' => 0,
        'width' => 200,
        'height' => 50,
        'alignHorizontal' => 'center',
        'alignVertical' => 'center',
        'debug' => true
    ));
    $image->text('This is a big sentence', array(
        'fontSize' => 60, // Desired starting font size
        'x' => 300,
        'y' => 200,
        'width' => 200,
        'height' => 50,
        'alignHorizontal' => 'center',
        'alignVertical' => 'center',
        'debug' => true
    ));
    $image->textBox('Lorem ipsum dolor sit amet, consectetur adipiscing elit.', array('width' => 100, 'fontSize' => 8, 'x' => 50, 'y' => 70));
    $image->rectangle(40, 140, 170, 160, array(0, 0, 0), 0.5);
    $image->textBox('Auto wrap and scale font size to multiline text box width and height bounds. Vestibulum venenatis risus scelerisque enim faucibus, ac pretium massa condimentum. Curabitur faucibus mi at convallis viverra. Integer nec finibus ligula, id hendrerit felis.', array(
        'width' => 150,
        'height' => 140,
        'fontSize' => 16, // Desired starting font size
        'x' => 50,
        'y' => 150
    ));
    return $image->show();






        $origin_path            = 'images/origin.jpg';
        $origin_resize_path     = 'images/origin_resize.jpg';
        $origin_add_watermark   = 'images/origin_add_watermark';
        $watermark              = 'images/watermark.png';


        // create Image from file
        $img = Image::make($origin_path);

        // write text
        // $img->text('The quick brown fox jumps over the lazy dog.');

        // write text at position
        // $img->text('The quick brown fox jumps over the lazy dog.', 120, 100);

        // use callback to define details
        $img->text('origin', 120, 100, function($font) {
            $font->file(public_path('fonts/OpenSans-Bold.ttf'));
            $font->size(24);
            $font->color('#fdf6e3');
            $font->align('center');
            $font->valign('top');
            $font->angle(45);
        });

        // // draw transparent text
        // $img->text('origin', 0, 0, function($font) {
        //     $font->color(array(255, 255, 255, 0.5));
        // });

        return $img->response('jpg');

        // // 修改指定图片的大小
        // $img = Image::make($origin_path)->resize(200, 200);
        // // $img = Image::canvas(800, 600, '#ccc');
        // $img->text('The quick brown fox jumps over the lazy dog.',120, 100);
        // $img->text('origin', 1, 2, function($font) {
        //     $font->file('origin/bar.ttf');
        //     $font->size(24);
        //     $font->color('#fdf6e3');
        //     $font->align('center');
        //     $font->valign('top');
        //     $font->angle(45);
        // });


        // // 将处理后的图片重新保存到其他路径
        // $img->save($origin_resize_path);
        // // 插入水印, 水印位置在原图片的右下角, 距离下边距 10 像素, 距离右边距 15 像素
        // // $img->insert('images/watermark.png', 'bottom-right', 15, 10);
        // // 这些逻辑可以通过下面的链式表达式搞定
        // $img = Image::make($origin_path)->resize(200, 200)
        //                                 ->insert($watermark, 'bottom-right', 15, 10)
        //                                 ->save($origin_add_watermark);
        // return view('photo', compact('origin_path', 'origin_resize_path', 'origin_add_watermark'));
    }
}