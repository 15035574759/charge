<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
//




/**
 * 高端PHP - 自定义函数
 *
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Type_model.php 2016-6-12 16:36:52
 */

/**
 * 打印函数 打印关于变量的易于理解的信息。
 * @param  [type] $var [description]
 * @return [type]      [description]
 */
if (! function_exists('p')) 
{
	function p($var)
	{   
	 	echo "<pre>";   
	  	print_r($var);   
	    exit;
	}
}

/**
 * 打印函数 打印关于变量的详细信息。
 * @param  [type] $var [description]
 * @return [type]      [description]
 */
if (! function_exists('dd'))
{
	function dd($var)
	{    
		echo "<pre>";    
		var_dump($var);   
		exit;
	}
}

/**
 * echo 打印函数 输出一个或者多个字符串
 * @param  [type] $val [description]
 * @return [type]      [description]
 */
if (! function_exists('e'))
{
	function e($val)
	{ 
		echo "<pre>";    
		echo $val;
		exit;
	}
}

// 以POST方式提交数据
function post_data($url, $param, $is_file = false, $return_array = true) {
    set_time_limit ( 0 );
    if (! $is_file && is_array ( $param )) {
        $param = JSON ( $param );
    }
    if ($is_file) {
        $header [] = "content-type: multipart/form-data; charset=UTF-8";
    } else {
        $header [] = "content-type: application/json; charset=UTF-8";
    }
    $ch = curl_init ();
    if (class_exists ( '/CURLFile' )) { // php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
        curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, true );
    } else {
        if (defined ( 'CURLOPT_SAFE_UPLOAD' )) {
            curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false );
        }
    }
    curl_setopt ( $ch, CURLOPT_URL, $url );
    curl_setopt ( $ch, CURLOPT_CUSTOMREQUEST, "POST" );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
    curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
    curl_setopt ( $ch, CURLOPT_HTTPHEADER, $header );
    curl_setopt ( $ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)' );
    curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
    curl_setopt ( $ch, CURLOPT_AUTOREFERER, 1 );
    curl_setopt ( $ch, CURLOPT_POSTFIELDS, $param );
    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
    $res = curl_exec ( $ch );
    $flat = curl_errno ( $ch );
    if ($flat) {
        $data = curl_error ( $ch );
        addWeixinLog ( $flat, 'post_data flat' );
        addWeixinLog ( $data, 'post_data msg' );
    }
    
    curl_close ( $ch );
    
    $return_array && $res = json_decode ( $res, true );
    
    return $res;
}
