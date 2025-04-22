<?php
/**
 * Created by jianphp.
 * User: muquan
 * Date: 2025-04-19
 * Time: 11:56
 */
namespace xyb\sm4;
class App
{
    private $clientSecret='';
    private $sm4='';

    function __construct($clientSecret)
    {
        $key=$this->getKey($clientSecret);
        $this->sm4=new RtSm4($key);
        $this->clientSecret=$clientSecret;
    }

    /**
     * 解密密文
     * @param string  $encryptStr 加密密文
     * @return string
     */
    public function decrypt($encryptStr){
        $text = $this->urlSafeBase64Decode($encryptStr);
        $iv=$this->getIv($this->clientSecret);
        return $this->sm4->decrypt($text,'sm4-cbc',$iv,'utf-8');
    }


    /**
     * 获取解密key
     * @param string  $clientSecret 应用秘钥
     * @return string
     */
    private function getKey($clientSecret){
        return str_pad(substr($this->urlSafeBase64Decode($clientSecret), 0, 16), 16, "\0", STR_PAD_RIGHT);
    }

    /**
     * 获取解密iv
     * @param string  $clientSecret 应用秘钥
     * @return string
     */
    private function getIv($clientSecret){
        return str_pad(substr($this->urlSafeBase64Decode(strtolower($clientSecret)), 0, 16), 16, "\0", STR_PAD_RIGHT);
    }

    /**
     * 将 URL 安全的 Base64 转换为标准 Base64解码
     * @param string  $input 原始字符串
     * @return string
     */
    private function urlSafeBase64Decode($input) {
        $remainder = strlen($input) % 4;
        if ($remainder) {
            $input .= str_repeat('=', 4 - $remainder);
        }
        $input = strtr($input, '-_', '+/');

        return base64_decode($input);
    }

}