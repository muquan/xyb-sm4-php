<?php
namespace xyb\sm4;
class RtSm4
{
    protected $sm4;
    protected $keyLen = 16;
    protected $ivLen  = 16;

    function __construct($key)
    {
        $this->sm4 = new Core($key);
    }

    public function encrypt($data, $type = 'sm4', $iv = '', $formatOut = 'hex')
    {

        if ($type != 'sm4-ecb') {
            $this->check_iv($iv);
        }
        $ret = '';
        switch ($type) {
            case 'sm4':
            case 'sm4-cbc':
                $data = $this->mystr_pad_bak($data, $this->keyLen); //需要补齐
                $ret  = $this->sm4->enDataCbc($data, $iv);
                break;
            case 'sm4-ecb':
                $data = $this->mystr_pad_bak($data, $this->keyLen); //需要补齐
                $ret  = $this->sm4->enDataEcb($data);
                break;
            case 'sm4-ctr':
                $ret = $this->sm4->enDataCtr($data, $iv);
                break;
            case 'sm4-ofb':
                $ret = $this->sm4->enDataOfb($data, $iv);
                break;
            case 'sm4-cfb':
                $ret = $this->sm4->enDataCfb($data, $iv);
                break;
            default:
                throw new Exception('bad type');
        }
        if ($formatOut == 'hex') {
            return bin2hex($ret);
        } else if ($formatOut == 'base64') {
            return base64_encode($ret);
        }
        return $ret;
    }

    public function decrypt($data, $type = 'sm4', $iv = '', $formatInput = 'hex')
    {
        if ($type != 'sm4-ecb') {
            $this->check_iv($iv);
        }
        if ($formatInput == 'hex') {
            $data = hex2bin($data);
        } else if ($formatInput == 'base64') {
            $data = base64_decode($data);
        }
        //else  is raw
        switch ($type) {
            case 'sm4':
            case 'sm4-cbc':
                $ret = $this->sm4->deDataCbc($data, $iv);
                $ret = $this->mystr_unpad_bak($ret);
                break;
            case 'sm4-ecb':
                $ret = $this->sm4->deDataEcb($data);
                $ret = $this->mystr_unpad_bak($ret);
                break;
            case 'sm4-ctr':
                $ret = $this->sm4->deDataCtr($data, $iv);
                break;
            case 'sm4-ofb':
                $ret = $this->sm4->deDataOfb($data, $iv);
                break;
            case 'sm4-cfb':
                $ret = $this->sm4->deDataCfb($data, $iv);
                break;
            default:
                throw new Exception('bad type');
        }
        return $ret;
    }

    //加密前补齐，特定后缀不适用全部的对接方
    protected function mystr_pad($data, $len = 16)
    {
        $n         = $len - strlen($data) % $len;
        $tmpString = $this->strToHex($data) . "80";
        $tmpString .= str_repeat("00", $n - 1);
        return $this->hexToStr($tmpString);
    }

    // 解密后去掉补齐，特定后缀不适用全部的对接方
    protected function mystr_unpad($data)
    {
        $tmp       = $this->strToHex($data);
        $lastIndex = strrpos($tmp, '80');
        if ($lastIndex !== false) {
            $tmp = substr($tmp, 0, $lastIndex);
        }
        return $this->hexToStr($tmp);
    }

    //加密前补齐，通用代码，无特定需求可用这个
    protected function mystr_pad_bak($data, $len = 16)
    {
        $n = $len - strlen($data) % $len;
        return $data . str_repeat(chr($n), $n);
    }
    // 解密后去掉补齐 通用的代码，无特定需求可用这个
    protected function mystr_unpad_bak($data)
    {
        $n = ord(substr($data, -1));
        return substr($data, 0, -$n);
    }
    protected function check_iv($iv)
    {
        if (strlen($iv) != $this->ivLen) {
            throw new Exception('bad iv');
        }
    }

    protected function strToHex($string)
    {
        $hex = unpack('H*', $string);
        return $hex[1];
    }

    protected function hexToStr($hex)
    {
        $string = pack('H*', $hex);
        return $string;
    }
}

