<?php

define('IN_ECS', true);
global $_LANG;

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . '/includes/lib_payment.php');
require(ROOT_PATH . '/includes/lib_order.php');
require(ROOT_PATH . '/includes/lib_suntech.php');

$payment_lang = ROOT_PATH . '/languages/' . $GLOBALS['_CFG']['lang'] . '/payment/suntech.php';
$redirect = '/';

if (file_exists($payment_lang)) {
    include_once($payment_lang);
}
$note1 = explode(',', $_POST['note1']);
$payment_name = $note1[0];
if($note1[1] == 'mobile') {
    $payment = getMobilePayment($payment_name);
    $redirect = 'mobile';
}
else {
    $payment = get_payment($payment_name);
}

if (!$payment) {
    die("payment error:" . $payment_name);
}

$error_msg = '';
$pwd = $payment['suntech_password'];
$order_sn = $_POST['Td'];
$order = explode(',', urldecode($_POST['note2']));
$log_id = $order[0];
$order_info = order_info('', $order_sn);
$order_id = $order_info['order_id'];

if ($order[1] != strtoupper(sha1($_POST["web"] . $pwd . $order_sn . $log_id))) {
    die("token error");
}

if ($payment["suntech_test_mode"] == 'yes') {
    $url = "https://test.esafe.com.tw/Service/Etopm.aspx";
} else {
    $url = "https://www.esafe.com.tw/Service/Etopm.aspx";
}

// 分期，信用卡刷卡(BuySafe)限定
switch ($payment_name) {
    case 'suntech_buysafe':
        if (is_numeric($_POST['Term']) && $_POST['Term'] > 1) {
            if ($payment['suntech_buysafe_' . $_POST['Term']] != 'yes') {
                $error_msg = $_LANG['suntech_buysafe_error_term'];
                break;
            }
            setOrderPayName($order_id, $_LANG['suntech_buysafe_term'][$_POST['Term']]);
            $_POST['ChkValue'] = strtoupper(sha1($_POST["web"] . $pwd . $_POST["MN"] . $_POST['Term']));
        } else {
            unset($_POST['Term']);
        }
        break;
    case 'suntech_unionpay':
    case 'suntech_webatm':
        setOrderPayName($order_id);
        break;
    case 'suntech_sunship':
        setOrderPayName($order_id);
        if ($order_info['amount'] < 50) {
            $error_msg = $_LANG['suntech_sunship_less_50'];
        }
        break;
    case 'suntech_24pay':
    case 'suntech_paycode':
        if (is_numeric($payment['suntech_due_date']) && $payment['suntech_due_date'] > 1) {
            $NewDate = Date('Ymd', strtotime("+" . $payment['suntech_due_date'] . " days"));
        } else {
            $NewDate = Date('Ymd', strtotime("+7 days"));
        }
        $_SESSION['payment_duedate'] = $NewDate;
        $_POST['DueDate'] = $NewDate;
        $_POST['UserNo'] = $payment['user_id'];
        break;
    default:
}

if ($_POST['CargoFlag']) {
    if($payment['suntech_cargo'] != 'yes') {
        $error_msg = $_LANG['suntech_cargo_disabled'];
    }
    else {
        $shipping_name =  $GLOBALS['_LANG']['suntech_cargo'];
        setOrderShippingName($order_info['order_id'], $shipping_name);
    }
}

// 輸出html
$html = '<!DOCTYPE html>';
$html .= '<html>';
$html .= '<head>';
$html .= '<meta charset="utf-8">';
$html .= '</head>';
$html .= '<body>';

if ($error_msg == '') {
    $html .= '<form method="post" id="suntech_payment" action="' . $url . '" >';
    foreach ($_POST as $key => $post) {
        $html .= '<input type="hidden" name="' . $key . '" value="' . $post . '">';
    }
    $html .= '</form>';
    $html .= '<script type="text/javascript">document.getElementById("suntech_payment").submit();</script>';
} else {
    $html .= '<script type="text/javascript">alert("' . $error_msg . '");location.replace("' . $redirect .'");</script>';
}

$html .= '</body>';
$html .= '</html>';

echo $html;

