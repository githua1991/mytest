<?php
/** 缩略图生成类,支持imagemagick及gd库两种处理
*   Date:   2013-07-15
*   Author: fdipzone
*   Ver:    1.2
*
*   Func:
*   public  set_config: 设置参数
*   public  create_thumb: 生成缩略图
*   private fit: 缩略图片
*   private crop: 裁剪图片
*   private gd_fit: GD库缩略图片
*   private gd_crop: GD库裁剪图片
*   private get_size: 获取要转换的size
*   private get_crop_offset: 获取裁图的偏移量
*   private add_watermark: 添加水印
*   private check_handler: 判断处理程序是否已安装
*   private create_dirs: 创建目录
*   private exists: 判断参数是否存在
*   private to_log: 记录log
*   private hex2rgb: hex颜色转rgb颜色
*   private get_file_ext: 获取图片类型
*
*   ver:    1.1 增加GD库处理
*   ver:    1.2 增加width,height错误参数处理
*               增加当图片colorspace不为RGB时作转RGB处理
*               修正使用crop保存为gif时出现透明无效区域问题，使用+repage参数，删除透明无效区域即可
*
*   tips:建议使用imagemagick
*        GD库不支持透明度水印,如果必须使用透明水印,请将水印图片做成有透明度。
*        GD库输出gif如加透明水印，会有问题。
*/

class PicThumbAdmin{ // class start

    private $_log = null;            // log file
    private $_handler = null;        // 进行图片处理的程序,imagemagick/gd库
    private $_type = 'fit';          // fit or crop
    private $_source = null;         // 原图路径
    private $_dest = null;           // 缩略图路径
    private $_watermark = null;      // 水印图片
    private $_opacity = 75;          // 水印圖片透明度,gd库不支持
    private $_gravity = 'SouthEast'; // 水印摆放位置 NorthWest, North, NorthEast, West, Center, East, SouthWest, South, SouthEast
    private $_geometry = '+10+10';   // 水印定位,gd库不支持
    private $_croppos = 'TL';        // 截图的位置 TL TM TR ML MM MR BL BM BR
    private $_bgcolor = null;        // 填充的背景色
    private $_quality = 90;          // 生成的图片质量
    private $_width = null;          // 指定区域宽度
    private $_height = null;         // 指定区域高度


    // 初始化
    public function __construct($logfile=''){
        if($logfile!=''){
            $this->_log = $logfile;
        }
    }


    // 设置参数
    public function set_config($param=array()){
        $this->_handler = $this->exists($param, 'handler')? strtolower($param['handler']) : null;
        $this->_type = $this->exists($param, 'type')? strtolower($param['type']) : 'fit';
        $this->_watermark = $this->exists($param, 'watermark')? $param['watermark'] : null;
        $this->_opacity = $this->exists($param, 'opacity')? $param['opacity'] : 75;
        $this->_gravity = $this->exists($param, 'gravity')? $param['gravity'] : 'SouthEast';
        $this->_geometry = $this->exists($param, 'geometry')? $param['geometry'] : '+10+10';
        $this->_croppos = $this->exists($param, 'croppos')? $param['croppos'] : 'TL';
        $this->_bgcolor = $this->exists($param, 'bgcolor')? $param['bgcolor'] : null;
        $this->_quality = $this->exists($param, 'quality')? $param['quality'] : 90;
        $this->_width = $this->exists($param, 'width')? $param['width'] : null;
        $this->_height = $this->exists($param, 'height')? $param['height'] : null;
    }


    /** 创建缩略图
    * @param String $source 原图
    * @param String $dest   目标图
    * @return boolean
    */
    public function create_thumb($source, $dest){

        // 检查使用的handler是否已安装
        if(!$this->check_handler()){
            $this->to_log('handler not installed');
            return false;
        }

        // 判断区域宽高是否正确
        if(!is_numeric($this->_width) || !is_numeric($this->_height) || $this->_width<=0 || $this->_height<=0){
            $this->to_log('width or height invalid');
            return false;
        }

        // 判断源文件是否存在
        if(!file_exists($source)){
            $this->to_log($source.' not exists');
            return false;
        }

        // 创建目标文件路径
        if(!$this->create_dirs($dest)){
            $this->to_log(dirname($dest).' create fail');
            return false;
        }

        $this->_source = $source;   // 源文件
        $this->_dest = $dest;       // 目标文件

        // 处理图片
        switch($this->_type){
            case 'fit':
                if($this->_handler=='imagemagick'){
                    return $this->fit();
                }else{
                    return $this->gd_fit();
                }
                break;

            case 'crop':
                if($this->_handler=='imagemagick'){
                    return $this->crop();
                }else{
                    return $this->gd_crop();
                }
                break;

            default:
                $this->to_log($this->_type.' not fit and crop');
                return false;
        }

    }


    /** 按比例压缩或拉伸图片
    * @return boolean
    */
    private function fit(){

        // 判断是否填充背景
        $bgcolor = ($this->_bgcolor!=null)? 
        sprintf(" -background '%s' -gravity center -extent '%sx%s' ", $this->_bgcolor, $this->_width, $this->_height) : "";

        // 判断是否要转为RGB
        $source_info = getimagesize($this->_source);
        $colorspace = (!isset($source_info['channels']) || $source_info['channels']!=3)? ' -colorspace RGB ' : '';

        // 命令行
        $cmd = sprintf("convert -resize '%sx%s' '%s' %s -quality %s %s '%s'", $this->_width, $this->_height, $this->_source, $bgcolor, $this->_quality, $colorspace, $this->_dest);

        // 记录执行的命令
        $this->to_log($cmd);

        // 执行命令
        exec($cmd);

        // 添加水印
        $this->add_watermark($this->_dest);

        return is_file($this->_dest)? true : false;

    }


    /** 裁剪图片
    * @return boolean
    */
    private function crop(){

        // 获取生成的图片尺寸
        list($pic_w, $pic_h) = $this->get_size();

        // 获取截图的偏移量
        list($offset_w, $offset_h) = $this->get_crop_offset($pic_w, $pic_h);

        // 判断是否要转为RGB
        $source_info = getimagesize($this->_source);
        $colorspace = (!isset($source_info['channels']) || $source_info['channels']!=3)? ' -colorspace RGB ' : '';

        // 命令行
        $cmd = sprintf("convert -resize '%sx%s' '%s' -quality %s %s -crop %sx%s+%s+%s +repage '%s'", $pic_w, $pic_h, $this->_source, $this->_quality, $colorspace, $this->_width, $this->_height, $offset_w, $offset_h, $this->_dest);

        // 记录执行的命令
        $this->to_log($cmd);

        // 执行命令
        exec($cmd);

        // 添加水印
        $this->add_watermark($this->_dest);

        return is_file($this->_dest)? true : false;

    }


    /** GD库按比例压缩或拉伸图片
    * @return boolean
    */
    private function gd_fit(){

        // 获取生成的图片尺寸
        list($pic_w, $pic_h) = $this->get_size();

        list($owidth, $oheight, $otype) = getimagesize($this->_source);

        switch($otype){
            case 1: $source_img = imagecreatefromgif($this->_source); break;
            case 2: $source_img = imagecreatefromjpeg($this->_source); break;
            case 3: $source_img = imagecreatefrompng($this->_source); break;
            default: return false;
        }

        // 按比例缩略/拉伸图片
        $new_img = imagecreatetruecolor($pic_w, $pic_h);
        imagecopyresampled($new_img, $source_img, 0, 0, 0, 0, $pic_w, $pic_h, $owidth, $oheight);

        // 判断是否填充背景
        if($this->_bgcolor!=null){
            $bg_img = imagecreatetruecolor($this->_width, $this->_height);
            $rgb = $this->hex2rgb($this->_bgcolor);
            $bgcolor =imagecolorallocate($bg_img, $rgb['r'], $rgb['g'], $rgb['b']);
            imagefill($bg_img, 0, 0, $bgcolor);
            imagecopy($bg_img, $new_img, (int)(($this->_width-$pic_w)/2), (int)(($this->_height-$pic_h)/2), 0, 0, $pic_w, $pic_h);
            $new_img = $bg_img;
        }

        // 获取目标图片的类型
        $dest_ext = $this->get_file_ext($this->_dest);

        // 生成图片
        switch($dest_ext){
            case 1: imagegif($new_img, $this->_dest, $this->_quality); break;
            case 2: imagejpeg($new_img, $this->_dest, $this->_quality); break;
            case 3: imagepng($new_img, $this->_dest, (int)(($this->_quality-1)/10)); break;
        }

        if(isset($source_img)){
            imagedestroy($source_img);
        }

        if(isset($new_img)){
            imagedestroy($new_img);
        }

        // 添加水印
        $this->add_watermark($this->_dest);

        return is_file($this->_dest)? true : false;

    }


    /** GD库裁剪图片
    * @return boolean
    */
    private function gd_crop(){

        // 获取生成的图片尺寸
        list($pic_w, $pic_h) = $this->get_size();

        // 获取截图的偏移量
        list($offset_w, $offset_h) = $this->get_crop_offset($pic_w, $pic_h);

        list($owidth, $oheight, $otype) = getimagesize($this->_source);

        switch($otype){
            case 1: $source_img = imagecreatefromgif($this->_source); break;
            case 2: $source_img = imagecreatefromjpeg($this->_source); break;
            case 3: $source_img = imagecreatefrompng($this->_source); break;
            default: return false;
        }

        // 按比例缩略/拉伸图片
        $tmp_img = imagecreatetruecolor($pic_w, $pic_h);
        imagecopyresampled($tmp_img, $source_img, 0, 0, 0, 0, $pic_w, $pic_h, $owidth, $oheight);

        // 裁剪图片
        $new_img = imagecreatetruecolor($this->_width, $this->_height);
        imagecopyresampled($new_img, $tmp_img, 0, 0, $offset_w, $offset_h, $this->_width, $this->_height, $this->_width, $this->_height);

        // 获取目标图片的类型
        $dest_ext = $this->get_file_ext($this->_dest);

        // 生成图片
        switch($dest_ext){
            case 1: imagegif($new_img, $this->_dest, $this->_quality); break;
            case 2: imagejpeg($new_img, $this->_dest, $this->_quality); break;
            case 3: imagepng($new_img, $this->_dest, (int)(($this->_quality-1)/10)); break;
        }

        if(isset($source_img)){
            imagedestroy($source_img);
        }

        if(isset($tmp_img)){
            imagedestroy($tmp_img);
        }

        if(isset($new_img)){
            imagedestroy($new_img);
        }

        // 添加水印
        $this->add_watermark($this->_dest);

        return is_file($this->_dest)? true : false;

    }


    /** 获取目标图生成的size
    * @return Array $width, $height
    */
    private function get_size(){
        list($owidth, $oheight) = getimagesize($this->_source);
        $width = (int)($this->_width);
        $height = (int)($this->_height);
        
        switch($this->_type){
            case 'fit':
                $pic_w = $width;
                $pic_h = (int)($pic_w*$oheight/$owidth);
                if($pic_h>$height){
                    $pic_h = $height;
                    $pic_w = (int)($pic_h*$owidth/$oheight);
                }
                break;
            case 'crop':
                if($owidth>$oheight){
                    $pic_h = $height;
                    $pic_w = (int)($pic_h*$owidth/$oheight);
                }else{
                    $pic_w = $width;
                    $pic_h = (int)($pic_w*$oheight/$owidth);
                }
                break;
        }

        return array($pic_w, $pic_h);
    }


    /** 获取截图的偏移量
    * @param int $pic_w 图宽度
    * @param int $pic_h 图高度
    * @return Array $offset_w, $offset_h
    */
    private function get_crop_offset($pic_w, $pic_h){
        $offset_w = 0;
        $offset_h = 0;
        
        switch(strtoupper($this->_croppos)){
            case 'TL':
                $offset_w = 0;
                $offset_h = 0;
                break;

            case 'TM':
                $offset_w = (int)(($pic_w-$this->_width)/2);
                $offset_h = 0;
                break;

            case 'TR':
                $offset_w = (int)($pic_w-$this->_width);
                $offset_h = 0;
                break;

            case 'ML':
                $offset_w = 0;
                $offset_h = (int)(($pic_h-$this->_height)/2);
                break;

            case 'MM':
                $offset_w = (int)(($pic_w-$this->_width)/2);
                $offset_h = (int)(($pic_h-$this->_height)/2);
                break;

            case 'MR':
                $offset_w = (int)($pic_w-$this->_width);
                $offset_h = (int)(($pic_h-$this->_height)/2);
                break;

            case 'BL':
                $offset_w = 0;
                $offset_h = (int)($pic_h-$this->_height);
                break;

            case 'BM':
                $offset_w = (int)(($pic_w-$this->_width)/2);
                $offset_h = (int)($pic_h-$this->_height);
                break;

            case 'BR':
                $offset_w = (int)($pic_w-$this->_width);
                $offset_h = (int)($pic_h-$this->_height);
                break;
        }

        return array($offset_w, $offset_h);
    }


    /** 添加水印
    * @param String $dest 图片路径
    */
    private function add_watermark($dest){
        if($this->_watermark!=null && file_exists($this->_watermark) && file_exists($dest)){
            list($owidth, $oheight, $otype) = getimagesize($dest);
            list($w, $h, $wtype) = getimagesize($this->_watermark);

            // 水印图比原图要小才加水印
            if($w<=$owidth && $h<=$oheight){

                if($this->_handler=='imagemagick'){ // imagemagick 添加水印

                    $cmd = sprintf("composite -gravity %s -geometry %s -dissolve %s '%s' %s %s", $this->_gravity, $this->_geometry, $this->_opacity, $this->_watermark, $dest, $dest);

                    $this->to_log($cmd);

                    exec($cmd);

                }else{ // gd 添加水印

                    switch($wtype){
                        case 1: $water_img = imagecreatefromgif($this->_watermark); break;
                        case 2: $water_img = imagecreatefromjpeg($this->_watermark); break;
                        case 3: $water_img = imagecreatefrompng($this->_watermark); break;
                        default: return false;
                    }

                    switch($otype){
                        case 1: $dest_img = imagecreatefromgif($dest); break;
                        case 2: $dest_img = imagecreatefromjpeg($dest); break;
                        case 3: $dest_img = imagecreatefrompng($dest); break;
                        default: return false;
                    }

                    // 水印位置
                    switch(strtolower($this->_gravity)){
                        case 'northwest':
                            $posX = 0;
                            $posY = 0;
                            break;
                        case 'north':
                            $posX = ($owidth - $w) / 2;
                            $posY = 0;
                            break;
                        case 'northeast':
                            $posX = $owidth - $w;
                            $posY = 0;
                            break;
                        case 'west':
                            $posX = 0;
                            $posY = ($oheight - $h) / 2;
                            break;
                        case 'center':
                            $posX = ($owidth - $w) / 2;
                            $posY = ($oheight - $h) / 2;
                            break;
                        case 'east':
                            $posX = $owidth - $w;
                            $posY = ($oheight - $h) / 2;
                            break;
                        case 'southwest':
                            $posX = 0;
                            $posY = $oheight - $h;
                            break;
                        case 'south':
                            $posX = ($owidth - $w) / 2;
                            $posY = $oheight - $h;
                            break;
                        case 'southeast':
                            $posX = $owidth - $w;
                            $posY = $oheight - $h;
                            break;
                    }

                    imagealphablending($dest_img, true);
                    imagecopy($dest_img, $water_img, $posX, $posY, 0, 0, $w, $h);

                    switch($otype){
                        case 1:imagegif($dest_img, $dest, $this->_quality); break;
                        case 2:imagejpeg($dest_img, $dest, $this->_quality); break;
                        case 3:imagepng($dest_img, $dest, (int)(($this->_quality-1)/10)); break;
                    }

                    if(isset($water_img)){
                        imagedestroy($water_img);
                    }

                    if(isset($dest_img)){
                        imagedestroy($dest_img);
                    }

                }
            }
        }
    }


    /** 判断处理程序是否已安装
    * @return boolean
    */
    private function check_handler(){

        $handler = $this->_handler;

        if(!in_array($handler, array('imagemagick', 'gd', null))){
            return false;
        }

        // 检查是否有安装imagemagick
        $imagemagick_installed = strstr(shell_exec('convert -version'),'Version: ImageMagick')!=''? true : false;

        // 检查是否有安装gd库
        $gd_installed = function_exists('gd_info')? true : false;

        switch($handler){
            case 'imagemagick':
                return $imagemagick_installed;
                break;

            case 'gd':
                return $gd_installed;
                break;

            case null:
                if($imagemagick_installed){
                    $this->_handler = 'imagemagick';
                    return true;
                }

                if($gd_installed){
                    $this->_handler = 'gd';
                    return true;
                }
                break;
        }

        return false;
    }


    /** 创建图片目录
    * @param String $path
    * @return boolean
    */
    private function create_dirs($dest){
        if(!is_dir(dirname($dest))){
            return mkdir(dirname($dest), 0777, true);
        }
        return true;
    }


    /** 判断参数是否存在
    * @param  Array   $obj  数组对象
    * @param  String  $key  要查找的key
    * @return boolean
    */
    private function exists($obj,$key=''){
        if($key==''){
            return isset($obj) && !empty($obj);
        }else{
            $keys = explode('.',$key);
            for($i=0,$max=count($keys); $i<$max; $i++){
                if(isset($obj[$keys[$i]])){
                    $obj = $obj[$keys[$i]];
                }else{
                    return false;
                }
            }
            return isset($obj) && !empty($obj);
        }
    }


    /** 记录log
    * @param String $msg 要记录的log讯息
    */
    private function to_log($msg){
        if($this->_log){
            $msg = '['.date('Y-m-d H:i:s').']'.$msg."\r\n";
            file_put_contents($this->_log, $msg, FILE_APPEND);
        }
    }


    /** hex颜色转rgb颜色
    * @param  String $color hex颜色
    * @return Array
    */
    private function hex2rgb($hexcolor){
        $color = str_replace('#', '', $hexcolor);
        if (strlen($color) > 3) {
            $rgb = array(
                'r' => hexdec(substr($color, 0, 2)),
                'g' => hexdec(substr($color, 2, 2)),
                'b' => hexdec(substr($color, 4, 2))
            );
        } else {
            $r = substr($color, 0, 1) . substr($color, 0, 1);
            $g = substr($color, 1, 1) . substr($color, 1, 1);
            $b = substr($color, 2, 1) . substr($color, 2, 1);
            $rgb = array(
                'r' => hexdec($r),
                'g' => hexdec($g),
                'b' => hexdec($b)
            );
        }
        return $rgb;
    }


    /** 获取图片类型
    * @param  String $file 图片路径
    * @return int
    */
    private function get_file_ext($file){
        $filename = basename($file);
        list($name, $ext)= explode('.', $filename);

        $ext_type = 0;

        switch(strtolower($ext)){
            case 'jpg':
            case 'jpeg':
                $ext_type = 2;
                break;
            case 'gif':
                $ext_type = 1;
                break;
            case 'png':
                $ext_type = 3;
                break;
        }

        return $ext_type;
    }

} // class end

