<?php
namespace upload;

/**
 * 极客之家 高端PHP - 图片压缩比例处理
 * @author Qinlh WeChat QinLinHui0706 OR whoru.S.Q <qinlh@outlook.com>
 * @copyright  Copyright (c) 2000-2018 QIN TEAM (http://www.qinlh.com)
 * @version    GUN  General Public License 10.0.0
 * @created 2018/09/19 10:57:59
 */
class Upload
{

    private $updir = ''; //文件名前缀
    private $newname = 'date'; //文件名类型 date时间类型
    private $maxsize = 10240000; //文件大小不得超过10M
    private $whdth = 1280; //图片最大宽度 130万像素比 有效1228800
    private $height = 960; //图片最大高度

    /**
     * [UploadPic 上传图片]
     * @param [$upfile] [图片对象]
     * @param [$updir] [文件名前缀]
     * @return [type] [description]
     * @author [qinlh] [qinlh@outlook.com]
     */
    public function UploadPic($upfile, $updir)
    {
        if ($this->newname == 'date') {
            $this->newname = date("Ymdhis");
        }
        //使用日期做文件名
        $name = $upfile["name"];
        $type = $upfile["type"];
        $size = $upfile["size"];
        $tmp_name = $upfile["tmp_name"];

        switch ($type) {
            case 'image/pjpeg':
            case 'image/jpeg':
                $extend = ".jpg";
                break;
            case 'image/gif':
                $extend = ".gif";
                break;
            case 'image/png':
                $extend = ".png";
                break;
        }
        if (empty($extend)) {
            echo ("警告！只能上传图片类型：GIF JPG PNG");
            exit();
        }
        if ($size > $this->maxsize) {
            $maxpr = $this->maxsize / 1000;
            echo ("警告！上传图片大小不能超过" . $maxpr . "K!");
            exit();
        }
        if (move_uploaded_file($tmp_name, $updir . $newname . $extend)) {
            return $updir . $newname . $extend;
        }
    }

    /**
     * [show_pic_scal 宽高比例缩放]
     * @return [type] [description]
     * @author [qinlh] [qinlh@outlook.com]
     */
    public function show_pic_scal($picpath)
    {
        $imginfo = GetImageSize($picpath);
        $imgw = $imginfo[0];
        $imgh = $imginfo[1];

        $ra = number_format(($imgw / $imgh), 1); //宽高比
        $ra2 = number_format(($imgh / $imgw), 1); //高宽比

        if ($imgw > $this->whdth or $imgh > $this->height) {
            if ($imgw > $imgh) {
                $newWidth = $this->whdth;
                $newHeight = round($newWidth / $ra);

            } elseif ($imgw < $imgh) {
                $newHeight = $height;
                $newWidth = round($newHeight / $ra2);
            } else {
                $newWidth = $this->whdth;
                $newHeight = round($newWidth / $ra);
            }
        } else {
            $newHeight = $imgh;
            $newWidth = $imgw;
        }
        $newsize[0] = $newWidth;
        $newsize[1] = $newHeight;

        return $newsize;
    }

    public function getImageInfo($src)
    {
        return getimagesize($src);
    }

    /**
     * 创建图片，返回资源类型
     * @param string $src 图片路径
     * @return resource $im 返回资源类型
     */
    public function create($src)
    {
        $info = $this->getImageInfo($src);
        switch ($info[2]) {
            case 1:
                $im = imagecreatefromgif($src);
                break;
            case 2:
                $im = imagecreatefromjpeg($src);
                break;
            case 3:
                $im = imagecreatefrompng($src);
                break;
        }
        return $im;
    }

    /**
     * 缩略图主函数
     * @param string $src 图片路径
     * @param int $w 缩略图宽度
     * @param int $h 缩略图高度
     * @return mixed 返回缩略图路径
     */
    public function resize($src, $w, $h)
    {
        $temp = pathinfo($src);
        $name = $temp["basename"]; //文件名
        $dir = $temp["dirname"]; //文件所在的文件夹
        $extension = $temp["extension"]; //文件扩展名
        $savepath = "{$dir}/{$name}"; //缩略图保存路径,新的文件名为*.thumb.jpg

        //获取图片的基本信息
        $info = $this->getImageInfo($src);
        $width = $info[0]; //获取图片宽度
        $height = $info[1]; //获取图片高度
        $per1 = round($width / $height, 2); //计算原图长宽比
        $per2 = round($w / $h, 2); //计算缩略图长宽比

        //计算缩放比例
        if ($per1 > $per2 || $per1 == $per2) {
            //原图长宽比大于或者等于缩略图长宽比，则按照宽度优先
            $per = $w / $width;
        }
        if ($per1 < $per2) {
            //原图长宽比小于缩略图长宽比，则按照高度优先
            $per = $h / $height;
        }
        $temp_w = intval($width * $per); //计算原图缩放后的宽度
        $temp_h = intval($height * $per); //计算原图缩放后的高度
        $temp_img = imagecreatetruecolor($temp_w, $temp_h); //创建画布
        $im = $this->create($src);
        imagecopyresampled($temp_img, $im, 0, 0, 0, 0, $temp_w, $temp_h, $width, $height);
        if ($per1 > $per2) {
            imagejpeg($temp_img, $savepath, 100);
            imagedestroy($im);
            return $savepath;
            // return $this->addBg($savepath, $w, $h, "w");
            //宽度优先，在缩放之后高度不足的情况下补上背景
        }
        if ($per1 == $per2) {
            imagejpeg($temp_img, $savepath, 100);
            imagedestroy($im);
            return $savepath;
            //等比缩放
        }
        if ($per1 < $per2) {
            imagejpeg($temp_img, $savepath, 100);
            imagedestroy($im);
            return $savepath;
            // return $this->addBg($savepath, $w, $h, "h");
            //高度优先，在缩放之后宽度不足的情况下补上背景
        }
    }

    /**
     * 添加背景
     * @param string $src 图片路径
     * @param int $w 背景图像宽度
     * @param int $h 背景图像高度
     * @param String $first 决定图像最终位置的，w 宽度优先 h 高度优先 wh:等比
     * @return 返回加上背景的图片
     */
    public function addBg($src, $w, $h, $fisrt = "w")
    {
        $bg = imagecreatetruecolor($w, $h);
        $white = imagecolorallocate($bg, 255, 255, 255);
        imagefill($bg, 0, 0, $white); //填充背景

        //获取目标图片信息
        $info = $this->getImageInfo($src);
        $width = $info[0]; //目标图片宽度
        $height = $info[1]; //目标图片高度
        $img = $this->create($src);
        if ($fisrt == "wh") {
            //等比缩放
            return $src;
        } else {
            if ($fisrt == "w") {
                $x = 0;
                $y = ($h - $height) / 2; //垂直居中
            }
            if ($fisrt == "h") {
                $x = ($w - $width) / 2; //水平居中
                $y = 0;
            }
            imagecopymerge($bg, $img, $x, $y, 0, 0, $width, $height, 100);
            imagejpeg($bg, $src, 100);
            imagedestroy($bg);
            imagedestroy($img);
            return $src;
        }

    }
}
