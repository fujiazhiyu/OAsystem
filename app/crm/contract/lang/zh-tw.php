<?php
/**
 * The zh-tw file of crm contract module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青島易軟天創網絡科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     contract 
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
if(!isset($lang->contract)) $lang->contract = new stdclass();
$lang->contract->common = '合同';

$lang->contract->id            = '編號';
$lang->contract->order         = '簽約訂單';
$lang->contract->customer      = '所屬客戶';
$lang->contract->name          = '名稱';
$lang->contract->code          = '合同編號';
$lang->contract->amount        = '金額';
$lang->contract->currency      = '貨幣類型';
$lang->contract->all           = '合同總額';
$lang->contract->thisAmount    = '本次回款';
$lang->contract->items         = '主要條款';
$lang->contract->begin         = '開始日期';
$lang->contract->end           = '結束日期';
$lang->contract->dateRange     = '起止日期';
$lang->contract->delivery      = '交付';
$lang->contract->deliveredBy   = '由誰交付';
$lang->contract->deliveredDate = '交付時間';
$lang->contract->return        = '回款';
$lang->contract->returnedBy    = '由誰回款';
$lang->contract->returnedDate  = '回款時間';
$lang->contract->status        = '狀態';
$lang->contract->contact       = '聯繫人';
$lang->contract->address       = '合同地址';
$lang->contract->signedBy      = '由誰簽署';
$lang->contract->signedDate    = '簽署日期';
$lang->contract->finishedBy    = '由誰完成';
$lang->contract->finishedDate  = '完成時間';
$lang->contract->canceledBy    = '由誰取消';
$lang->contract->canceledDate  = '取消時間';
$lang->contract->createdBy     = '由誰創建';
$lang->contract->createdDate   = '創建時間';
$lang->contract->editedBy      = '最後修改';
$lang->contract->editedDate    = '最後修改時間';
$lang->contract->handlers      = '貢獻者';
$lang->contract->contactedBy   = '由誰聯繫';
$lang->contract->contactedDate = '最後聯繫';
$lang->contract->nextDate      = '下次聯繫';
$lang->contract->product       = '產品';
$lang->contract->productLine   = '產品綫';
$lang->contract->files         = '附件';
$lang->contract->createAddress = '新建';

$lang->contract->browse           = '瀏覽合同';
$lang->contract->receive          = '回款';
$lang->contract->cancel           = '取消合同';
$lang->contract->view             = '合同詳情';
$lang->contract->finish           = '完成合同';
$lang->contract->record           = '溝通';
$lang->contract->delete           = '刪除合同';
$lang->contract->list             = '合同列表';
$lang->contract->create           = '創建合同';
$lang->contract->edit             = '編輯合同';
$lang->contract->setting          = '系統設置';
$lang->contract->uploadFile       = '上傳附件';
$lang->contract->lifetime         = '合同的一生';
$lang->contract->returnRecords    = '回款記錄';
$lang->contract->deliveryRecords  = '交付記錄';
$lang->contract->completeReturn   = '完成回款';
$lang->contract->completeDelivery = '完成交付';
$lang->contract->editReturn       = '編輯回款';
$lang->contract->editDelivery     = '編輯交付';
$lang->contract->deleteReturn     = '刪除回款';
$lang->contract->deleteDelivery   = '刪除交付';
$lang->contract->export           = '導出';
$lang->contract->totalReturn      = '回款合計';
$lang->contract->tradeList        = '收支記錄';
$lang->contract->manageTeam       = '維護團隊';
$lang->contract->confirmTeam      = '確認貢獻度';

$lang->contract->deliveryList[]        = '';
$lang->contract->deliveryList['wait']  = '等待交付';
$lang->contract->deliveryList['doing'] = '交付中';
$lang->contract->deliveryList['done']  = '交付完成';

$lang->contract->returnList[]        = '';
$lang->contract->returnList['wait']  = '等待回款';
$lang->contract->returnList['doing'] = '回款中';
$lang->contract->returnList['done']  = '回款完成';

$lang->contract->statusList[]           = '';
$lang->contract->statusList['normal']   = '正常';
$lang->contract->statusList['closed']   = '已完成';
$lang->contract->statusList['canceled'] = '已取消';

$lang->contract->codeUnitList[]        = '';
$lang->contract->codeUnitList['Y']     = '年';
$lang->contract->codeUnitList['m']     = '月';
$lang->contract->codeUnitList['d']     = '日';
$lang->contract->codeUnitList['fix']   = '固定值';
$lang->contract->codeUnitList['input'] = '輸入值';

$lang->contract->totalAmount        = '本頁合同總金額：%s，已回款：%s，其中本月回款：%s；';
$lang->contract->returnInfo         = "<p>%s, 由 <strong>%s</strong> 回款%s。</p>";
$lang->contract->deliveryInfo       = "<p>%s由%s交付。</p>";
$lang->contract->deleteReturnInfo   = "%s的回款%s";
$lang->contract->deleteDeliveryInfo = "%s的交付";
$lang->contract->teamTips           = '<strong>成員為空</strong>或者<strong>貢獻度為空</strong>不保存。';

$lang->contract->placeholder = new stdclass();
$lang->contract->placeholder->real = '成交金額';

$lang->contract->team = new stdclass();
$lang->contract->team->common       = '團隊';
$lang->contract->team->account      = '成員';
$lang->contract->team->contribution = '貢獻度(%)';
$lang->contract->team->money        = '銷售額';
$lang->contract->team->status       = '狀態';
$lang->contract->team->accept       = '接受';
$lang->contract->team->reject       = '拒絶';
$lang->contract->team->total        = '合計';

$lang->contract->team->statusList['wait']   = '待確認';
$lang->contract->team->statusList['accept'] = '已接受';
$lang->contract->team->statusList['reject'] = '已拒絶';

$lang->contract->error = new stdclass();
$lang->contract->error->wrong = '<strong>貢獻度</strong>應該是數字';

$lang->plan = new stdclass();
$lang->plan->amount = $lang->contract->thisAmount;

/* Width settings for different languages, in pixels. */
$lang->contract->actionWidth   = 240;
