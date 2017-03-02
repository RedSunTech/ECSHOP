<?php
if (!defined('IN_ECS')) {
    die('Hacking attempt');
}

function setOrderAmount($order_id, $paid = 0, $amount = 0)
{
    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
        " SET money_paid = '$paid', " .
        " order_amount = '$amount' " .
        "WHERE order_id = '$order_id'";
    $GLOBALS['db']->query($sql);
}


function setOrderPayName($order_id, $pay_name = '')
{
    $order_detail_html = sprintf($GLOBALS['_LANG']['suntech_order_detail'], $order_id);
    if ($pay_name != '') {
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
            " SET pay_name = CONCAT(pay_name, '$pay_name', '$order_detail_html') " .
            "WHERE order_id = '$order_id'";
    } else {
        $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
            " SET pay_name = CONCAT(pay_name, '$order_detail_html') " .
            "WHERE order_id = '$order_id'";
    }
    $GLOBALS['db']->query($sql);
}


function setOrderShippingName($order_id, $shipping_name, $cargo_no = '')
{
    $shipping_track_html = '';
    if ($cargo_no != '') {
        $shipping_track_html = sprintf($GLOBALS['_LANG']['suntech_cargo_info_html'], $cargo_no, $cargo_no);
    }

    $sql = 'UPDATE ' . $GLOBALS['ecs']->table('order_info') .
        " SET shipping_id = '1', " .
        " shipping_name = CONCAT('$shipping_name', '$shipping_track_html') " .
        "WHERE order_id = '$order_id'";
    $GLOBALS['db']->query($sql);
}

function setOrderFeedBack($user_id, $note, $order_id)
{
    $sql = "INSERT INTO " . $GLOBALS['ecs']->table('feedback') .
        "(parent_id, user_id, user_name, user_email, msg_title, msg_type, msg_content, msg_time, message_img, order_id)" .
        " VALUES (0, '" . $user_id . "', '" . $GLOBALS['_LANG']['suntech_order_reminder'] . "', ' ', " .
        "'" . $GLOBALS['_LANG']['suntech_order_notice'] . "', 5, '" . $note . "', '" . gmtime() . "', '', '" . $order_id . "')";
    $GLOBALS['db']->query($sql);
}

function sendMail($order, $status)
{
    global $ecs, $smarty, $_CFG;

    switch ($status) {
        case 'confirm':
            if ($_CFG['send_confirm_email'] == '1') {
                $tpl = get_mail_template('order_confirm');
                $order['formated_add_time'] = local_date($GLOBALS['_CFG']['time_format'], $order['add_time']);
                $smarty->assign('order', $order);
                $smarty->assign('shop_name', $_CFG['shop_name']);
                $smarty->assign('send_date', local_date($_CFG['date_format']));
                $smarty->assign('sent_date', local_date($_CFG['date_format']));
                $content = $smarty->fetch('str:' . $tpl['template_content']);
            }
            break;
        case 'ship':
            if ($_CFG['send_ship_email'] == '1') {
                $order['invoice_no'] = '';
                $tpl = get_mail_template('deliver_notice');
                $smarty->assign('order', $order);
                $smarty->assign('send_time', local_date($_CFG['time_format']));
                $smarty->assign('shop_name', $_CFG['shop_name']);
                $smarty->assign('send_date', local_date($_CFG['date_format']));
                $smarty->assign('sent_date', local_date($_CFG['date_format']));
                $smarty->assign('confirm_url', $ecs->url() . 'receive.php?id=' . $order['order_id'] . '&con=' . rawurlencode($order['consignee']));
                $smarty->assign('send_msg_url', $ecs->url() . 'user.php?act=message_list&order_id=' . $order['order_id']);
                $content = $smarty->fetch('str:' . $tpl['template_content']);
            }
            break;
        case 'cancel':
            if ($_CFG['send_cancel_email'] == '1') {
                $tpl = get_mail_template('order_cancel');
                $smarty->assign('order', $order);
                $smarty->assign('shop_name', $_CFG['shop_name']);
                $smarty->assign('send_date', local_date($_CFG['date_format']));
                $smarty->assign('sent_date', local_date($_CFG['date_format']));
                $content = $smarty->fetch('str:' . $tpl['template_content']);
            }
            break;
        case 'invalid':
            if ($_CFG['send_invalid_email'] == '1') {
                $tpl = get_mail_template('order_invalid');
                $smarty->assign('order', $order);
                $smarty->assign('shop_name', $_CFG['shop_name']);
                $smarty->assign('send_date', local_date($_CFG['date_format']));
                $smarty->assign('sent_date', local_date($_CFG['date_format']));
                $content = $smarty->fetch('str:' . $tpl['template_content']);
            }
            break;
    }

    if (isset($tpl)) {
        send_mail($order['consignee'], $order['email'], $tpl['template_subject'], $content, $tpl['is_html']);
    }
}

function getMobilePayment($code)
{
    $sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('touch_payment') . " WHERE pay_code = '$code' AND enabled = '1'";
    $res = $GLOBALS['db']->query($sql, 'SILENT');
    $payment = $GLOBALS['db']->fetchRow($res);
    $config_list = unserialize($payment['pay_config']);
    foreach ($config_list as $config) {
        $payment[$config['name']] = $config['value'];
    }
    return $payment;
}