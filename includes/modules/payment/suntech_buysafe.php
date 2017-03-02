<?php

/**
 * ECSHOP 紅陽 BuySafe
 */

$suntech_payment = 'suntech_buysafe';

if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' . $GLOBALS['_CFG']['lang'] . '/payment/suntech.php';

if (file_exists($payment_lang)) {
    global $_LANG;

    include_once($payment_lang);
}

/* 模块的基本信息 */
if (isset($set_modules) && $set_modules == TRUE) {
    $i = isset($modules) ? count($modules) : 0;

    /* 代码 */
    $modules[$i]['code'] = basename(__FILE__, '.php');

    /* 描述对应的语言项 */
    $modules[$i]['desc'] = $suntech_payment . '_desc';

    /* 是否支持货到付款 */
    $modules[$i]['is_cod'] = '0';

    /* 是否支持在线支付 */
    $modules[$i]['is_online'] = '1';

    /* 作者 */
    $modules[$i]['author'] = '紅陽科技';

    /* 网址 */
    $modules[$i]['website'] = 'https://www.esafe.com.tw/';

    /* 版本号 */
    $modules[$i]['version'] = '1.5.0';

    /* 配置信息 */
    $modules[$i]['config'] = array(
        array('name' => 'suntech_account', 'type' => 'text', 'value' => ''),
        array('name' => 'suntech_password', 'type' => 'text', 'value' => ''),
        array('name' => 'suntech_test_mode', 'type' => 'select', 'value' => 'no'),
        array('name' => 'suntech_buysafe_3', 'type' => 'select', 'value' => 'no'),
        array('name' => 'suntech_buysafe_6', 'type' => 'select', 'value' => 'no'),
        array('name' => 'suntech_buysafe_12', 'type' => 'select', 'value' => 'no'),
        array('name' => 'suntech_buysafe_18', 'type' => 'select', 'value' => 'no'),
        array('name' => 'suntech_buysafe_24', 'type' => 'select', 'value' => 'no'),
        array('name' => 'suntech_cargo', 'type' => 'select', 'value' => 'no')
    );

    return;
}

include_once(ROOT_PATH . '/includes/lib_suntech.php');

/**
 * 类
 */
class suntech_buysafe
{
    /**
     * 构造函数
     *
     * @access  public
     * @param
     *
     */
    function __construct()
    {
        $this->suntech_buysafe();
        $this->suntech_payment = 'suntech_buysafe';
    }

    function suntech_buysafe()
    {
    }

    /**
     * 生成支付代码
     * @param  array $order 订单信息
     * @param  array $payment 支付方式信息
     * @return  string
     */
    function get_code($order, $payment)
    {
        $url = "/suntech_post.php";
        $order_amount = round($order["order_amount"], 0);
        $form = '<form method="post" name="keyinorder" action="' . $url . '" >';
        $form .= '<input type="hidden" name="web" value="' . $payment["suntech_account"] . '">';//商店代號
        $form .= '<input type="hidden" name="MN" value="' . $order_amount . '">';//交易金額 maxlength="8"
        $form .= '<input type="hidden" name="Td" value="' . $order['order_sn'] . '">';//訂單編號
        $form .= '<input type="hidden" name="sna" value="' . $order["consignee"] . '">';//姓名 maxlength="30"
        $form .= '<input type="hidden" name="sdt" value="' . $order["mobile"] . '">';//電話 maxlength="24"
        $form .= '<input type="hidden" name="email" value="' . $order["email"] . '">';//email maxlength="100"
        $form .= '<input type="hidden" name="note1" value="' . $this->suntech_payment . '">';//備註1 maxlength="4000"
        $form .= '<input type="hidden" name="note2" value="' . $order['log_id'] . ',' . strtoupper(sha1($payment["suntech_account"] . $payment["suntech_password"] . $order['order_sn'] . $order['log_id'])) . '">';//備註2 maxlength="200"
        $form .= '<input type="hidden" name="Card_Type" value="0">';//交易類別

        $term_select = '<div style="padding-bottom: 10px">' . $GLOBALS['_LANG']['suntech_buysafe_installments'] . ' : <select name="Term">';
        foreach ($GLOBALS['_LANG']['suntech_buysafe_term'] as $key => $term) {
            if ($key == 1 || $payment['suntech_buysafe_' . $key] == 'yes') {
                $term_select .= '<option value="' . $key . '">' . $term . '</option>';
            }
        }
        $term_select .= '</select></div>';
        $form .= $term_select;


        if ($payment['suntech_cargo'] == 'yes') {
            $cargo_flag = '<div style="padding-bottom: 10px">' . $GLOBALS['_LANG']['suntech_cargo'] . ' : <input name="CargoFlag" value="1" type="checkbox"></div>';
            $form .= $cargo_flag;
        }

        $form .= '<input type="hidden" name="ChkValue" value="' . strtoupper(sha1($payment["suntech_account"] . $payment["suntech_password"] . $order_amount)) . '">';//備註2 maxlength="200"
        $form .= '<input type="submit" value="' . $GLOBALS['_LANG']['suntech_pay_submit'] . '" />';
        $form .= '</form>';

        $button = '<div style="text-align:center">' . $form . '</div>';

        return $button;
    }

    /**
     * 响应操作
     */
    function respond()
    {
        $payment = get_payment($this->suntech_payment);
        $store_pwd = $payment["suntech_password"];
        $order_sn = $_POST['Td'];
        $cargo_no = isset($_POST['CargoNo']) ? $_POST['CargoNo'] : '';
        $store_type = isset($_POST['StoreType']) ? $_POST['StoreType'] : '';
        $order = explode(',', urldecode($_POST['note2']));
        $log_id = $order[0];
        $order_info = order_info('', $order_sn);

        if ($order[1] != strtoupper(sha1($_POST["web"] . $store_pwd . $order_sn . $log_id))) {
            return false;
        }

        if ($store_type != '') {
            $check_value = strtoupper(sha1($_POST["web"] . $store_pwd . $_POST['buysafeno'] . $store_type));
            if ($check_value != $_POST['ChkValue']) {
                return false;
            };

            if ($store_type == '1010') {
                $shipping_status = SS_RECEIVED;
            } else {
                $shipping_status = SS_SHIPPED;
                sendMail($order_info, 'ship');
            }
            $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
                " SET shipping_status = '" . $shipping_status . "', shipping_time = '" . gmtime() . "'" .
                " WHERE order_id = '" . $order_info['order_id'] . "'";
            $GLOBALS['db']->query($sql);
            $note = $GLOBALS['_LANG']['suntech_shipping_msg'] . urldecode($_POST["StoreMsg"]) . (($_POST["StoreType"] != '') ? sprintf($GLOBALS['_LANG']['suntech_shipping_code'], $_POST["StoreType"]) : '');
            order_action($order_info['order_sn'], $order_info['order_status'], $shipping_status, $order_info['pay_status'], $note, $GLOBALS['_LANG']['buyer']);
            setOrderFeedBack($order_info['user_id'], $note, $order_info['order_id']);
            echo '0000';
            exit;
        } else {
            $check_value = strtoupper(sha1($_POST["web"] . $store_pwd . $_POST['buysafeno'] . intval($_POST["MN"]) . $_POST['errcode'] . $cargo_no));
            if ($check_value != $_POST['ChkValue']) {
                return false;
            };

            /* 检查支付的金额是否相符 */
            if (!check_money($log_id, $_POST['MN'])) {
                return false;
            }

            if (($_POST['errcode'] === "00") || ($_POST['errcode'] === "0")) {
                /* 改变订单状态 */
                if ($order_info['order_status'] != OS_CONFIRMED) {
                    $note = $GLOBALS['_LANG']['suntech_paid'] . sprintf($GLOBALS['_LANG']['suntech_trans_sn'], $_POST['buysafeno']);
                    if ($cargo_no != '') {
                        $shipping_name =  $GLOBALS['_LANG']['suntech_cargo'];
                        $shipping_name .= isset($_POST['StoreName'])? '(' . urldecode($_POST['StoreName']) . ')':'';
                        setOrderShippingName($order_info['order_id'], $shipping_name, $cargo_no);
                        $note .= sprintf($GLOBALS['_LANG']['suntech_cargo_info'], $cargo_no, $cargo_no);
                    }
                    /* 改变订单状态 */
                    order_paid($log_id, 2, $note);
                    sendMail($order_info, 'confirm');
                    setOrderFeedBack($order_info['user_id'], $note, $order_info['order_id']);
                }
                return true;
            } else {
                return false;
            }
        }
    }
}
