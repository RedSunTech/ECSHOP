<?php

/**
 * ECSHOP 红阳buysafe
 * ============================================================================
 * 版权所有 2005-2010 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liuhui $
 * $Id: alipay.php 17063 2010-03-25 06:35:46Z liuhui $
 */

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/buysafe.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc']    = 'buysafe_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod']  = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online']  = '1';

    /* 作者 */
    $modules[$i]['author']  = '紅陽科技';

    /* 网址 */
    $modules[$i]['website'] = 'https://www.esafe.com.tw/';

    /* 版本号 */
    $modules[$i]['version'] = '1.0.4';

    /* 配置信息 */
    $modules[$i]['config']  = array(
        array('name' => 'buysafe_account', 'type' => 'text', 'value' => ''),
        array('name' => 'buysafe_password', 'type' => 'text', 'value' => '')
    );

    return;
}

/**
 * 类
 */
class buysafe
{

    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function buysafe()
    {
    }

    function __construct()
    {
        $this->buysafe();
    }

    /**
     * 生成支付代码
     * @param   array   $order      订单信息
     * @param   array   $payment    支付方式信息
     */
    function get_code($order, $payment)
    {
        if (!defined('EC_CHARSET'))
        {
            $charset = 'utf-8';
        }
        else
        {
            $charset = EC_CHARSET;
        }
    	$url = "https://www.esafe.com.tw/Service/Etopm.aspx";
    	//$url = "https://test.esafe.com.tw/Service/Etopm.aspx";
    	$order_amount=round($order["order_amount"], 0);
		
        $form = '<form method="post" name="keyinorder" action="'.$url.'" >';
        $form.='<input type="hidden" name="web" value="'.$payment["buysafe_account"].'">';//商店代號
        $form.='<input type="hidden" name="MN" value="'.$order_amount.'">';//交易金額 maxlength="8"
        //$form.='<input type="hidden" name="online" value="1">';//交易方式
        $form.='<input type="hidden" name="Td" value="'.$order['order_sn'].'">';//訂單編號
        $form.='<input type="hidden" name="OrderInfo" value="">';//交易內容 maxlength="4000"
        $form.='<input type="hidden" name="sna" value="'.$order["consignee"].'">';//姓名 maxlength="30"
        //$form.='<input type="hidden" name="uI" value="A987654321">';//身分證 maxlength="10"
        $form.='<input type="hidden" name="sdt" value="'.$order["tel"].'">';//電話 maxlength="24"
        $form.='<input type="hidden" name="sd" value="'.$order["address"].'">';//住址 maxlength="100"
        $form.='<input type="hidden" name="email" value="'.$order["email"].'">';//email maxlength="100"
        $form.='<input type="hidden" name="note1" value="buysafe">';//備註1 maxlength="4000"
        $form.='<input type="hidden" name="note2" value="'.$order['log_id'].','.strtoupper(sha1($payment["buysafe_account"].$payment["buysafe_password"].$order['order_sn'].$order['log_id'].$order_amount)).'">';//備註2 maxlength="200"
        $form.='<input type="hidden" name="Card_Type" value="">';//交易類別
        $form.='<input type="hidden" name="ChkValue" value="'.strtoupper(sha1($payment["buysafe_account"].$payment["buysafe_password"].$order_amount)).'">';//備註2 maxlength="200"
        $form.='<input type="submit" value="' .$GLOBALS['_LANG']['pay_button']. '" />';
        $form.='</form>';

        $button = '<div style="text-align:center">'.$form.'</div>';

        return $button;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        if (!empty($_POST))
        {
            foreach($_POST as $key => $data)
            {
                $_GET[$key] = $data;
            }
        }
        $payment  = get_payment('buysafe');
        $order_sn = $_GET['Td'];
        $order = explode(',',urldecode($_GET['note2']));
        $log_id = $order[0];
        if ($order[1] != strtoupper(sha1($payment["buysafe_account"].$payment["buysafe_password"].$order_sn.$log_id.intval($_GET["MN"])))) {
        	return false;
        }
        if ($_GET['ChkValue'] != strtoupper(sha1($payment["buysafe_account"].$payment["buysafe_password"].$_GET['buysafeno'].intval($_GET["MN"]).$_GET['errcode']))) {
            return false;
        }
        /* 检查支付的金额是否相符 */
        if (!check_money($log_id, $_GET['MN']))
        {
            return false;
        }
        if (($_GET['errcode']==="00") || ($_GET['errcode']==="0")){
            /* 改变订单状态 */
            order_paid($log_id, 2);

            return true;
        }else{
            return false;
        }
    }
    
    
   static $lockstream = 'st=lDEFABCNOPyzghi_jQRST-UwxkVWXYZabcdef+IJK6/7nopqr89LMmGH012345uv';
   //加密
   public function enCrypt($txtStream,$password){
       //随机找一个数字，并从密锁串中找到一个密锁值
       $lockLen = strlen(self::$lockstream);
       $lockCount = rand(0,$lockLen-1);
       $randomLock = self::$lockstream[$lockCount];
       //结合随机密锁值生成MD5后的密码
       $password = md5($password.$randomLock);
       //开始对字符串加密
       $txtStream = base64_encode($txtStream);
       $tmpStream = '';
       $i=0;$j=0;$k = 0;
       for ($i=0; $i<strlen($txtStream); $i++) {
           $k = $k == strlen($password) ? 0 : $k;
           $j = (strpos(self::$lockstream,$txtStream[$i])+$lockCount+ord($password[$k]))%($lockLen);
           $tmpStream .= self::$lockstream[$j];
           $k++;
       }
       return $tmpStream.$randomLock;
   }
   public function deCrypt($txtStream,$password){
       $lockLen = strlen(self::$lockstream);
       //获得字符串长度
       $txtLen = strlen($txtStream);
       //截取随机密锁值
       $randomLock = $txtStream[$txtLen - 1];
       //获得随机密码值的位置
       $lockCount = strpos(self::$lockstream,$randomLock);
       //结合随机密锁值生成MD5后的密码
       $password = md5($password.$randomLock);
       //开始对字符串解密
       $txtStream = substr($txtStream,0,$txtLen-1);
       $tmpStream = '';
       $i=0;$j=0;$k = 0;
       for ($i=0; $i<strlen($txtStream); $i++) {
           $k = $k == strlen($password) ? 0 : $k;
           $j = strpos(self::$lockstream,$txtStream[$i]) - $lockCount - ord($password[$k]);
           while($j < 0){
               $j = $j + ($lockLen);
           }
           $tmpStream .= self::$lockstream[$j];
           $k++;
       }
       return base64_decode($tmpStream);
   }
    
}

?>