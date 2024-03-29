<?php
/**
 * The en file of crm contract module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     contract 
 * @version     $Id$
 * @link        http://www.zdoo.org
 */
if(!isset($lang->contract)) $lang->contract = new stdclass();
$lang->contract->common = 'Contract';

$lang->contract->id            = 'ID';
$lang->contract->order         = 'Order';
$lang->contract->customer      = 'Customer';
$lang->contract->name          = 'Name';
$lang->contract->code          = 'Alias';
$lang->contract->amount        = 'Amount';
$lang->contract->currency      = 'Currency';
$lang->contract->all           = 'Total amount';
$lang->contract->thisAmount    = 'This amount';
$lang->contract->items         = 'Items';
$lang->contract->begin         = 'Start';
$lang->contract->end           = 'End';
$lang->contract->dateRange     = 'Time Frame';
$lang->contract->delivery      = 'Deliver';
$lang->contract->deliveredBy   = 'Delivered By';
$lang->contract->deliveredDate = 'Delivered';
$lang->contract->return        = 'Payment';
$lang->contract->returnedBy    = 'Received By';
$lang->contract->returnedDate  = 'Received';
$lang->contract->status        = 'Status';
$lang->contract->contact       = 'Contact';
$lang->contract->address       = 'Address';
$lang->contract->signedBy      = 'Signed By';
$lang->contract->signedDate    = 'Signed';
$lang->contract->finishedBy    = 'Finished By';
$lang->contract->finishedDate  = 'Finished';
$lang->contract->canceledBy    = 'Cancelled By';
$lang->contract->canceledDate  = 'Cancelled';
$lang->contract->createdBy     = 'Created By';
$lang->contract->createdDate   = 'Created';
$lang->contract->editedBy      = 'Edited By';
$lang->contract->editedDate    = 'Edited';
$lang->contract->handlers      = 'Contributors';
$lang->contract->contactedBy   = 'Contacted By';
$lang->contract->contactedDate = 'Contacted';
$lang->contract->nextDate      = 'Next Date';
$lang->contract->product       = 'Product';
$lang->contract->productLine   = 'Product Line';
$lang->contract->files         = 'Files';
$lang->contract->createAddress = 'Create Address';

$lang->contract->browse           = 'Contract';
$lang->contract->receive          = 'Receive';
$lang->contract->cancel           = 'Cancel';
$lang->contract->view             = 'Contract';
$lang->contract->finish           = 'Finish';
$lang->contract->record           = 'Record';
$lang->contract->delete           = 'Delete';
$lang->contract->list             = 'Contract';
$lang->contract->create           = 'Create';
$lang->contract->edit             = 'Edit';
$lang->contract->setting          = 'Settings';
$lang->contract->uploadFile       = 'Upload';
$lang->contract->lifetime         = 'Lifetime';
$lang->contract->returnRecords    = 'Payment';
$lang->contract->deliveryRecords  = 'Delivery';
$lang->contract->completeReturn   = 'Complete';
$lang->contract->completeDelivery = 'Complete';
$lang->contract->editReturn       = 'Edit';
$lang->contract->editDelivery     = 'Edit';
$lang->contract->deleteReturn     = 'Delete';
$lang->contract->deleteDelivery   = 'Delete';
$lang->contract->export           = 'Export';
$lang->contract->totalReturn      = 'Total';
$lang->contract->tradeList        = 'Trade List';
$lang->contract->manageTeam       = 'Manage Team';
$lang->contract->confirmTeam      = 'Confirm Contribution';

$lang->contract->deliveryList[]        = '';
$lang->contract->deliveryList['wait']  = 'Wait';
$lang->contract->deliveryList['doing'] = 'Doing';
$lang->contract->deliveryList['done']  = 'Done';

$lang->contract->returnList[]        = '';
$lang->contract->returnList['wait']  = 'Wait';
$lang->contract->returnList['doing'] = 'Doing';
$lang->contract->returnList['done']  = 'Done';

$lang->contract->statusList[]           = '';
$lang->contract->statusList['normal']   = 'Normal';
$lang->contract->statusList['closed']   = 'Closed';
$lang->contract->statusList['canceled'] = 'Cancelled';

$lang->contract->codeUnitList[]        = '';
$lang->contract->codeUnitList['Y']     = 'Year';
$lang->contract->codeUnitList['m']     = 'Month';
$lang->contract->codeUnitList['d']     = 'Day';
$lang->contract->codeUnitList['fix']   = 'Fix';
$lang->contract->codeUnitList['input'] = 'Input';

$lang->contract->totalAmount        = 'The total payment is %s. %s on this page, %s this month.';
$lang->contract->returnInfo         = "<p>%s, received by <strong>%s</strong>, %s.</p>";
$lang->contract->deliveryInfo       = "<p>%s, delivered by %s.</p>";
$lang->contract->deleteReturnInfo   = "%s in %s";
$lang->contract->deleteDeliveryInfo = "in %s";
$lang->contract->teamTips           = "The record has no memberd, so the contribution won't be saved.";

$lang->contract->placeholder = new stdclass();
$lang->contract->placeholder->real = 'Turnover';

$lang->contract->team = new stdclass();
$lang->contract->team->common       = 'Team';
$lang->contract->team->account      = 'Member';
$lang->contract->team->contribution = 'Contribution(%)';
$lang->contract->team->money        = 'Money';
$lang->contract->team->status       = 'Status';
$lang->contract->team->accept       = 'Accept';
$lang->contract->team->reject       = 'Reject';
$lang->contract->team->total        = 'Total';

$lang->contract->team->statusList['wait']   = 'Waiting';
$lang->contract->team->statusList['accept'] = 'Accepted';
$lang->contract->team->statusList['reject'] = 'Rejected';

$lang->contract->error = new stdclass();
$lang->contract->error->wrongContribution = '<strong>Contribution</strong> should be number.';

$lang->plan = new stdclass();
$lang->plan->amount = $lang->contract->thisAmount;

/* Width settings for different languages, in pixels. */
$lang->contract->actionWidth   = 320;
