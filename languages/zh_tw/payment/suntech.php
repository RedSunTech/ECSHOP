<?php

/**
 * SunTech 紅陽支付語言檔
 */

global $_LANG;

// 共用
$_LANG['suntech_account'] = '商家代碼';
$_LANG['suntech_password'] = '交易密碼';
$_LANG['suntech_pay_submit'] = '立即付款';
$_LANG['suntech_order_submit'] = '送出訂單';
$_LANG['suntech_test_mode'] = '啟用測試模式';
$_LANG['suntech_test_mode_desc'] = '是否在紅陽測試環境中測試交易';
$_LANG['suntech_test_mode_range']['yes'] = '是';
$_LANG['suntech_test_mode_range']['no'] = '否';
$_LANG['suntech_paid'] = '付款完成';
$_LANG['suntech_order_confirm'] = '訂單已成立';
$_LANG['suntech_order_detail'] = " <a href=\'http://www.artfood.com.tw/user.php?act=message_list&order_id=%s\'>訂單詳細資訊</a>";
$_LANG['suntech_trans_fail'] = '付款失敗，請重新下訂單';
$_LANG['suntech_trans_sn'] = '\n交易序號：%s';
$_LANG['suntech_cargo'] = '超商取貨';
$_LANG['suntech_cargo'] = '超商取貨';
$_LANG['suntech_cargo_desc'] = '是否搭配超商取貨';
$_LANG['suntech_cargo_range']['yes'] = '是';
$_LANG['suntech_cargo_range']['no'] = '否';
$_LANG['suntech_cargo_info'] = '\n交貨便代碼：%s\n請至 http://myship.7-11.com.tw/cc2b_track.asp?payment_no=%s 查詢物流';
$_LANG['suntech_cargo_info_html'] = "【交貨便代碼：%s】 <a href=\'http://myship.7-11.com.tw/cc2b_track.asp?payment_no=%s\'>查詢物流</a>";
$_LANG['suntech_cargo_disabled'] = '未啟用超商取貨';
$_LANG['suntech_shipping_msg'] = '送貨狀態：';
$_LANG['suntech_shipping_code'] = '（代碼：%s）';
$_LANG['suntech_due_date'] = '繳費期限';
$_LANG['suntech_due_date_text'] = '\n繳費期限: %s';
$_LANG['suntech_due_date_desc'] = '必須在此期限(天)前完成繳費';
$_LANG['suntech_product_name'] = '網購商品';
$_LANG['suntech_order_reminder'] = '系統通知';
$_LANG['suntech_order_notice'] = '訂單通知';

// 信用卡刷卡 BuySafe
$_LANG['suntech_buysafe'] = '信用卡刷卡';
$_LANG['suntech_buysafe_desc'] = '信用卡刷卡付款，可至下一步選擇分期期數及超商取貨。';
$_LANG['suntech_buysafe_3'] = '分3期';
$_LANG['suntech_buysafe_3_desc'] = '是否開放信用卡刷卡(分3期)方式付款。';
$_LANG['suntech_buysafe_3_range']['yes'] = '是';
$_LANG['suntech_buysafe_3_range']['no'] = '否';
$_LANG['suntech_buysafe_6'] = '分6期';
$_LANG['suntech_buysafe_6_desc'] = '是否開放信用卡刷卡(分6期)方式付款。';
$_LANG['suntech_buysafe_6_range']['yes'] = '是';
$_LANG['suntech_buysafe_6_range']['no'] = '否';
$_LANG['suntech_buysafe_12'] = '分12期';
$_LANG['suntech_buysafe_12_desc'] = '是否開放信用卡刷卡(分12期)方式付款。';
$_LANG['suntech_buysafe_12_range']['yes'] = '是';
$_LANG['suntech_buysafe_12_range']['no'] = '否';
$_LANG['suntech_buysafe_18'] = '分18期';
$_LANG['suntech_buysafe_18_desc'] = '是否開放信用卡刷卡(分18期)方式付款。';
$_LANG['suntech_buysafe_18_range']['yes'] = '是';
$_LANG['suntech_buysafe_18_range']['no'] = '否';
$_LANG['suntech_buysafe_24'] = '分24期';
$_LANG['suntech_buysafe_24_desc'] = '是否開放信用卡刷卡(分24期)方式付款。';
$_LANG['suntech_buysafe_24_range']['yes'] = '是';
$_LANG['suntech_buysafe_24_range']['no'] = '否';
$_LANG['suntech_buysafe_installments'] = '分期選項';
$_LANG['suntech_buysafe_term'][1] = '一次付清';
$_LANG['suntech_buysafe_term'][3] = '分3期';
$_LANG['suntech_buysafe_term'][6] = '分6期';
$_LANG['suntech_buysafe_term'][12] = '分12期';
$_LANG['suntech_buysafe_term'][18] = '分18期';
$_LANG['suntech_buysafe_term'][24] = '分24期';
$_LANG['suntech_buysafe_error_term'] = '選擇分期錯誤';

// 銀聯卡刷卡 UnionPay
$_LANG['suntech_unionpay'] = '銀聯卡刷卡';
$_LANG['suntech_unionpay_desc'] = '以銀聯卡支付款項。';

// 網路ATM WebATM
$_LANG['suntech_webatm'] = '網路ATM轉帳';
$_LANG['suntech_webatm_desc'] = '即時轉帳交易（需自備金融卡讀卡機），可至下一步選擇超商取貨。';

// 超商付款(繳費單) 24Payment
$_LANG['suntech_24pay'] = '超商付款(繳費單)';
$_LANG['suntech_24pay_desc'] = '請至【email 列印繳費單】，持繳費單至超商、郵局繳費，可至下一步選擇超商取貨。';
$_LANG['suntech_24pay_notice'] = '請至【email 列印繳費單】，持繳費單至超商、郵局繳費';
$_LANG['suntech_24pay_barcode'] = '商店自行產生繳費單專用訊息：\n●超商第一段條碼：%s\n超商第二段條碼：%s\n超商第三段條碼：%s\n●郵局第一段條碼：%s\n郵局第二段條碼：%s\n郵局第三段條碼：%s\n●ATM轉帳帳號(金額大於3萬請臨櫃匯款)：\n台新銀行代碼：812，分行代碼：0687\n帳號：%s';
$_LANG['suntech_24pay_type_1'] = '（超商條碼繳款）';
$_LANG['suntech_24pay_type_2'] = '（郵局條碼繳款）';
$_LANG['suntech_24pay_type_3'] = '（虛擬帳號繳款）';

// 超商付款(代碼) PayCode
$_LANG['suntech_paycode'] = '超商付款(代碼)';
$_LANG['suntech_paycode_desc'] = '請至【用戶中心】→【我的訂單】取得【代碼】至超商繳費，可至下一步選擇超商取貨。';
$_LANG['suntech_paycode_msg'] = '繳費代碼: %s\n持【繳費代碼】至指定超商繳費！';
$_LANG['suntech_paycode_code'] = '【繳費代碼: %s】';

// 超商取貨 SunShip
$_LANG['suntech_sunship'] = '超商取貨付款';
$_LANG['suntech_sunship_desc'] = '配送至指定的超商門市後取貨付款。';
$_LANG['suntech_sunship_less_50'] = '使用【超商取貨付款】，交易金額不得低於50元。';