<?php
/**
 * 发送短信验证码
 * @copyright  Copyright (c) 2000-2019 QIN TEAM (http://www.qlh.com)
 * @version    GUN  General Public License 10.0.0
 * @license    Id:  Userinfo.php 2019-5-8 13:04:00
 * @author     Qinlh WeChat QinLinHui0706
 */
namespace sms;
use think\Config;
use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

class aliyunSms {

    // 保存错误信息
    public $error;

    // Access Key ID
    private $accessKeyId = '';

    // Access Access Key Secret
    private $accessKeySecret = '';

    // 签名
    private $signName = '';

    // 模版ID
    private $templateCode = '';

    public function __construct() {
        $this->accessKeyId = Config('accessKeyId');
        $this->accessKeySecret = Config('accessKeySecret');
        $this->signName = Config('signName');
        $this->templateCode = Config('templateCode');
    }

    /**
    * 发送短信验证码
    * @param  [$mobile] [手机号]
    * @param  [$verify_code] [验证码]
    * @return [type] [description]
    * @author [qinlh] [WeChat QinLinHui0706]
    */
    public function SendSms($mobile='', $verify_code='') {
        if($mobile !== '' && $verify_code !== '') {
            if (!preg_match("/1[3456789]{1}\d{9}$/", $mobile)) {
                return ['Code' => 0, 'Message' => '手机号格式错误'];
            }
            AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessKeySecret)
                        ->regionId('cn-hangzhou') // 根据需要更换 RegionId
                        ->asGlobalClient();
            try {
                $result = AlibabaCloud::rpcRequest()
                                        ->product('Dysmsapi')
                                        // ->scheme('https') // https | http
                                        ->version('2017-05-25')
                                        ->action('SendSms')
                                        ->method('POST')
                                        ->options([
                                            'query' => [
                                                'PhoneNumbers' => $mobile,
                                                'SignName' => $this->signName,
                                                'TemplateCode' => $this->templateCode,
                                                'TemplateParam' => '{"code":"'.$verify_code.'"}',
                                            ],
                                        ])
                                    ->request();
                $returnArr = $result->toArray();
                if(isset($returnArr['Code']) && $returnArr['Code'] == 'OK') {
                    return ['Code' => 1, 'Message' => 'Successful sending of authentication code'];
                } else {
                    logger("R \r\n"."【{$mobile}】发送验证码失败：".$result);
                    return ['Code' => $returnArr['Code'], 'Message' => $returnArr['Message']];
                }
            } catch (ClientException $e) {
                echo $e->getErrorMessage() . PHP_EOL;
            } catch (ServerException $e) {
                echo $e->getErrorMessage() . PHP_EOL;
            }
        } else {
            return ['Code' => 0, 'Message' => '参数错误'];
        }
    }

}