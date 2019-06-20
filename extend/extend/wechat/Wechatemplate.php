<?php
/**
 * 极客之家 高端PHP - 微信公众号发送模板消息通知
 * @copyright  Copyright (c) 2016 QIN TEAM (http://www.qinlh.com)
 * @license    GUN  General Public License 2.0
 * @version    Id:  Aes.php 2019-2-15 16:36:52
 */
namespace wechat; 
use think\Config;

class Wechatemplate 
{ 
    public $appid; 
    public $appsecret; 
    public $access_token; 
      
    public function __construct(){ 
        $this->appid = config('APPID'); 
        $this->appsecret = config('APPSECRET'); 
        //判断access_token是否存在，不存在重新获取 
        if(session('access_token_bd')) { 
            $this->access_token = session('access_token_bd'); 
        } else { 
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$this->appid."&secret=".$this->appsecret; 
            $res = json_decode($this->http_request($url),true); 
            $this->access_token = $res['access_token']; 
            session(array('name'=>'access_token_bd','value'=>$this->access_token,'expire'=>7200));//保存access_token  
        }    
    }
//获取凭据access_token
    public function getAccessToken(){
        //如果缓存文件存在并且是7200秒之内更新的就直接读取缓存文件
        if(file_exists('./at') && time()-filemtime('./at')<7200){
            return file_get_contents('./at');
        }
        //以get方式请求
        $ret = file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->appsecret.'');
        //把json转为数组,第二个参数为true返回数组,false返回对象
        $ret = json_decode($ret,true);
        return $ret['access_token'];
    }
// 获取用户信息及openid
    public function getUserInfo($openid){
        $access_token = $this->getAccessToken();
        $url = 'https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';
        $re = file_get_contents($url);
        return json_decode($re,true);
    }
     /**
     * 发送模板消息
     * @param [$data] [模板数据实体]
     * @return [type] [array]
     */ 
    public function send_template_message($data){ 
        $url="https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=".$this->access_token; 
        $res=$this->http_request($url,$data); 
        return $res; 
        return json_decode($res,true); 
    } 

     /**
     * 处理模板消息发送数据实体
     * @param [$openid] [用户openid]
     * @param [$template_id] [模板ID]
     * @param [$data] [模板数据]
     * @param [$url] [模板跳转链接（海外帐号没有跳转能力）]
     * @param [$topcolor] [通知头部颜色]
     * @return [type] [array]
     */
    public function template($openid, $template_id, $data, $url='') { 
        switch($template_id){ 
            case 1: 
                $tpl = "x5GjL7LZvlZ5ORTzmDFQ_mGClOvKEXakoZGU_4y4i8c";//测试通知 
                break; 
            case 2: 
                $tpl = "qvv-TSDm87WbdWpGdjBXZmKWWBPGSkHW_VrMCO3OOLg";//下单成功通知
                break;
            default: 
                $tpl = ''; 
                break; 
            
        } 
        if(!$tpl) { 
            return ''; 
        } 
        $template = [
                        'touser'     => $openid, 
                        'template_id'=> $tpl, 
                        'url'        => $url, 
                        // 'topcolor'   =>$topcolor, 
                        'data'       => $data,
                        "emphasis_keyword" => "keyword1.DATA"  
                    ]; 
        return $template; 
    } 

     /**
     * request curl请求
     * @param [$url] [请求url]
     * @param [$data] [数据]
     * @return [type] [array]
     */
    protected function http_request($url,$data=null){ 
        $curl = curl_init(); 
        curl_setopt($curl,CURLOPT_URL,$url); 
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,FALSE); 
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,FALSE); 
        if(!empty($data)){ 
           curl_setopt($curl,CURLOPT_POST,1); 
           curl_setopt($curl,CURLOPT_POSTFIELDS,$data); 
        } 
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1); 
        $output = curl_exec($curl); 
        curl_close($curl); 
        return $output; 
    } 
} 