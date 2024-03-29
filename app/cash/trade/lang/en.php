<?php
/**
 * The trade module English file of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Xiying Guan <guanxiying@xirangit.com>
 * @package     trade
 * @version     $Id$
 * @link        http://www.zdoo.org
 */
if(!isset($lang->trade)) $lang->trade = new stdclass();
$lang->trade->common      = 'Trade';
$lang->trade->id          = 'ID';
$lang->trade->depositor   = 'Account';
$lang->trade->type        = 'Type';
$lang->trade->currency    = 'Currency';
$lang->trade->exchangeRate= 'Exchange rate';
$lang->trade->trader      = 'Trader';
$lang->trade->customer    = 'Customer';
$lang->trade->money       = 'Amount';
$lang->trade->status      = 'Status';
$lang->trade->rate        = 'ROI';
$lang->trade->desc        = 'Description';
$lang->trade->product     = 'Product';
$lang->trade->order       = 'Order';
$lang->trade->contract    = 'Contract';
$lang->trade->category    = 'Category';
$lang->trade->date        = 'Date';
$lang->trade->deadline    = 'Deadline';
$lang->trade->handlers    = 'Handler';
$lang->trade->dept        = 'Department';
$lang->trade->receipt     = 'From';
$lang->trade->payment     = 'To';
$lang->trade->fee         = 'Fee';
$lang->trade->transferIn  = 'Amount';
$lang->trade->transferOut = 'Amount';
$lang->trade->schema      = 'Template';
$lang->trade->importFile  = 'Files';
$lang->trade->encode      = 'Encode';
$lang->trade->createdBy   = 'Created By';
$lang->trade->createdDate = 'Created';
$lang->trade->editedBy    = 'Edited By';
$lang->trade->editedDate  = 'Edited';
$lang->trade->month       = 'Month';
$lang->trade->uploadFile  = 'Files';
$lang->trade->productLine = 'Product Line';
$lang->trade->area        = 'Customer Area';
$lang->trade->industry    = 'Customer Industry';
$lang->trade->level       = 'Customer Class';
$lang->trade->size        = 'Customer Size';
$lang->trade->interest    = 'Loan Interest';
$lang->trade->loanID      = 'Loan';
$lang->trade->investID    = 'Invest';
$lang->trade->loanrate    = 'Interest Rate';
$lang->trade->outType     = 'Type';

$lang->trade->all            = 'All';
$lang->trade->create         = 'Create Trade';
$lang->trade->in             = 'Income';
$lang->trade->out            = 'Expense';
$lang->trade->invest         = 'Invest';
$lang->trade->redeem         = 'Redeem';
$lang->trade->loan           = 'Loan';
$lang->trade->repay          = 'Pay';
$lang->trade->createIn       = 'Income';
$lang->trade->createOut      = 'Expense';
$lang->trade->transfer       = 'Transfer';
$lang->trade->edit           = 'Edit';
$lang->trade->detail         = 'Details';
$lang->trade->view           = 'View';
$lang->trade->browse         = 'Bills';
$lang->trade->delete         = 'Delete';
$lang->trade->batchCreate    = 'Batch Create';
$lang->trade->batchEdit      = 'Batch Edit';
$lang->trade->newTrader      = 'Create Trader';
$lang->trade->import         = 'Import';
$lang->trade->export         = 'Export';
$lang->trade->showImport     = 'Results';
$lang->trade->fullYear       = 'Full year';
$lang->trade->quarter        = 'Quarter';
$lang->trade->export2Excel   = 'Export Excel';
$lang->trade->compare        = 'Annual Compare';
$lang->trade->setReportUnit  = 'Report Unit';
$lang->trade->settings       = 'Settings';
$lang->trade->manageCategory = 'Manage Category';

$lang->trade->settingList['trader']       = 'Trader required';
$lang->trade->settingList['product']      = 'Product required';
$lang->trade->settingList['dept']         = 'Department required';
$lang->trade->settingList['category']     = 'Category required';
$lang->trade->settingList['lastCategory'] = 'Last-level Category required';

$lang->trade->report = new stdclass();
$lang->trade->report->common      = 'Report'; 
$lang->trade->report->annual      = ' Annual Report'; 
$lang->trade->report->month       = ' Month Report'; 
$lang->trade->report->compare     = ' Annual Comparison';
$lang->trade->report->create      = 'Create Report';
$lang->trade->report->selectYears = 'Select Year';
$lang->trade->report->undefined   = 'Undefined';
$lang->trade->report->compareTip  = 'Select two years to compare';
$lang->trade->report->unit        = 'Unit';

$lang->trade->report->unitList[1]       = '$';
$lang->trade->report->unitList[1000]    = 'K$';
$lang->trade->report->unitList[10000]   = '10K$';
$lang->trade->report->unitList[1000000] = 'M$';

$lang->trade->report->typeList['annual']  = 'Annual Balance Sheet'; 
$lang->trade->report->typeList['compare'] = 'Annual Comparison Sheet'; 

$lang->trade->typeList['in']          = 'Income';
$lang->trade->typeList['out']         = 'Expense';
$lang->trade->typeList['transferout'] = 'Transfer out';
$lang->trade->typeList['transferin']  = 'Transfer in';
$lang->trade->typeList['invest']      = 'Invest';
$lang->trade->typeList['redeem']      = 'Redeem';
$lang->trade->typeList['loan']        = 'Loan';
$lang->trade->typeList['repay']       = 'Repay';

$lang->trade->quarters = new stdclass();
$lang->trade->quarters->Q4 = '10,11,12';
$lang->trade->quarters->Q3 = '07,08,09';
$lang->trade->quarters->Q2 = '04,05,06';
$lang->trade->quarters->Q1 = '01,02,03';

$lang->trade->quarterList['Q1'] = '1st Quarter';
$lang->trade->quarterList['Q2'] = '2nd Quarter';
$lang->trade->quarterList['Q3'] = '3rd Quarter';
$lang->trade->quarterList['Q4'] = '4th Quarter';

$lang->trade->monthList['last']  = 'Last Year';
$lang->trade->monthList['01']    = 'January';
$lang->trade->monthList['02']    = 'February';
$lang->trade->monthList['03']    = 'March';
$lang->trade->monthList['04']    = 'April';
$lang->trade->monthList['05']    = 'May';
$lang->trade->monthList['06']    = 'June';
$lang->trade->monthList['07']    = 'July';
$lang->trade->monthList['08']    = 'August';
$lang->trade->monthList['09']    = 'September';
$lang->trade->monthList['10']    = 'October';
$lang->trade->monthList['11']    = 'November';
$lang->trade->monthList['12']    = 'December';
$lang->trade->monthList['total'] = 'Total';

$lang->trade->categoryList['transferin']  = 'Transfer In';
$lang->trade->categoryList['transferout'] = 'Transfer Out';
$lang->trade->categoryList['invest']      = 'Invest';
$lang->trade->categoryList['redeem']      = 'Redeem';
$lang->trade->categoryList['loan']        = 'Loan';
$lang->trade->categoryList['repay']       = 'Repay';

$lang->trade->transferCategoryList['transferin']  = 'Transfer In';
$lang->trade->transferCategoryList['transferout'] = 'Transfer Out';

$lang->trade->objectTypeList['customer'] = 'Customer';
$lang->trade->objectTypeList['order']    = 'Order';
$lang->trade->objectTypeList['contract'] = 'Contract';

$lang->trade->investTypeList['invest'] = 'Invest';
$lang->trade->investTypeList['redeem'] = 'Redeem';

$lang->trade->loanTypeList['loan']  = 'Loan';
$lang->trade->loanTypeList['repay'] = 'Repay';

$lang->trade->encodeList['gbk']  = 'GBK';
$lang->trade->encodeList['utf8'] = 'UTF-8';

$lang->trade->notEqual = 'The two accounts cannot be the same!';
$lang->trade->feeDesc  = '%s from %s to %s';
$lang->trade->fileNode = 'The format is csv';

$lang->trade->importedFields = array();
$lang->trade->importedFields['category'] = 'Category';
$lang->trade->importedFields['type']     = 'Type';
$lang->trade->importedFields['trader']   = 'Trader';
$lang->trade->importedFields['in']       = 'Income';
$lang->trade->importedFields['out']      = 'Expense';
$lang->trade->importedFields['date']     = 'Date';
$lang->trade->importedFields['category'] = 'Category';
$lang->trade->importedFields['dept']     = 'Department';
$lang->trade->importedFields['desc']     = 'Description';
$lang->trade->importedFields['fee']      = 'Fee';
$lang->trade->importedFields['product']  = 'Product';
$lang->trade->importedFields['handlers'] = 'Handler';

$lang->trade->statusList['returned']   = 'Returned';
$lang->trade->statusList['returning']  = 'Returning';
$lang->trade->statusList['unReturned'] = 'Unreturned';
$lang->trade->statusList['repaied']    = 'Repaid';
$lang->trade->statusList['repaying']   = 'Repaying';
$lang->trade->statusList['unRepaied']  = 'Unrepaid';

$lang->trade->progressList['invest'] = 'Redemption';
$lang->trade->progressList['loan']   = 'Repayment';

$lang->trade->totalIn       = '%s: income %s;';
$lang->trade->totalOut      = '%s: expense %s;';
$lang->trade->totalAmount   = '%s: income %s, expense %s, %s;';
$lang->trade->totalInvest   = '%s: invest %s, redeem %s, unredeem %s, %s;';
$lang->trade->selectItem    = 'Seleted';
$lang->trade->profit        = 'profit';
$lang->trade->loss          = 'loss';
$lang->trade->balance       = 'Income is equal to Expense';
$lang->trade->total         = 'Total';

$lang->trade->noTraderMatch  = 'No match found. Click to create.';
$lang->trade->unique         = 'There is a record existed.';
$lang->trade->showExistTrade = 'Show existing record';
$lang->trade->hideExistTrade = 'Hide existing record';
$lang->trade->ignore         = 'Ignore';
$lang->trade->denied         = 'You have no permission to browse the list. Please ask you Admin to set permissions.';
$lang->trade->emptyData      = 'The fields with * cannot be empty.';
$lang->trade->detailTip      = 'The total amount is different from the trade amount. Do you want to save the current amount?';

$lang->trade->chartList['productLine'] = 'by product line';
$lang->trade->chartList['category']    = 'by category';
$lang->trade->chartList['area']        = 'by customer area';
$lang->trade->chartList['industry']    = 'by customer industry';
$lang->trade->chartList['size']        = 'by customer size';
$lang->trade->chartList['dept']        = 'by department';

$lang->trade->excel = new stdclass();
$lang->trade->excel->title = new stdclass();
$lang->trade->excel->title->depositor = ' Profit and Loss Statement';

$lang->trade->excel->help = new stdclass();
$lang->trade->excel->help->depositor = "This report will not list currencies in different columns.";
