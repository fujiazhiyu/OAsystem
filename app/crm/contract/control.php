<?php
/**
 * The control file for contract of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     contract
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
class contract extends control
{
    /**
     * Construct method.
     * 
     * @param  string $moduleName 
     * @param  string $methodName 
     * @param  string $appName 
     * @access public
     * @return void
     */
    public function __construct($moduleName = '', $methodName = '', $appName = '')
    {
        parent::__construct($moduleName, $methodName, $appName);
        $this->app->loadLang('order', 'crm');
    }

    /**
     * Contract index page. 
     * 
     * @access public
     * @return void
     */
    public function index()
    {
        $this->locate(inlink('browse'));
    }

    /**
     * Browse all contracts; 
     * 
     * @param  string $mode 
     * @param  string $orderBy 
     * @param  int    $recTotal 
     * @param  int    $recPerPage 
     * @param  int    $pageID 
     * @access public
     * @return void
     */
    public function browse($mode = 'all', $orderBy = 'id_desc', $recTotal = 0, $recPerPage = 20, $pageID = 1)
    {   
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal, $recPerPage, $pageID);

        $contracts = $this->contract->getList(0, $mode, '', $orderBy, $pager);

        /* Save session for return link. */
        $this->session->set('contractList', $this->app->getURI(true));

        /* Build search form. */
        $this->loadModel('search');
        $this->config->contract->search['actionURL'] = $this->createLink('contract', 'browse', 'mode=bysearch');
        $this->config->contract->search['params']['product']['values']  = array('' => '') + $this->loadModel('product')->getPairs();
        $this->search->setSearchParams($this->config->contract->search);

        /* Set allowed edit contract ID list. */
        $this->app->user->canEditContractIdList = ',' . implode(',', $this->contract->getContractsSawByMe('edit', array_keys($contracts))) . ',';

        $this->view->title        = $this->lang->contract->browse;
        $this->view->contracts    = $contracts;
        $this->view->customers    = $this->loadModel('customer')->getPairs('client');
        $this->view->pager        = $pager;
        $this->view->mode         = $mode;
        $this->view->orderBy      = $orderBy;
        $this->view->currencySign = $this->loadModel('common')->getCurrencySign();
        $this->view->currencyList = $this->common->getCurrencyList();
        if($contracts) $this->view->totalAmount = $this->contract->countAmount($contracts);

        $this->display();
    }

    /**
     * Create contract. 
     * 
     * @param  int    $customerID
     * @param  int    $orderID 
     * @access public
     * @return void
     */
    public function create($customerID = 0, $orderID = 0)
    {
        if($_POST)
        {
            $contractID = $this->contract->create();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('contract', $contractID, 'Created');
            $this->loadModel('action')->create('customer', $this->post->customer, 'createContract', '', html::a($this->createLink('contract', 'view', "contractID=$contractID"), $this->post->name));

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('browse')));
        }

        if($customerID) $this->view->customer = $customerID;
        if($orderID)
        {
            $this->view->currentOrder = $this->loadModel('order', 'crm')->getByID($orderID);
            $this->view->orders       = $this->order->getList($mode = 'query', "customer={$customerID} and o.status = 'normal'");
        }

        unset($this->lang->contract->menu);
        $this->view->title        = $this->lang->contract->create;
        $this->view->orderID      = $orderID;
        $this->view->customers    = $this->loadModel('customer')->getPairs('client', $emptyOption = true, $orderBy = 'id_desc', $limit = $this->config->customerLimit, $customerID);
        $this->view->users        = $this->loadModel('user')->getPairs('nodeleted,noclosed');
        $this->view->currencyList = $this->loadModel('common')->getCurrencyList();
        $this->view->currencySign = $this->loadModel('common')->getCurrencySign();
        $this->display();
    }

    /**
     * Edit contract.
     * 
     * @param  int    $contractID 
     * @param  bool   $comment
     * @access public
     * @return void
     */
    public function edit($contractID, $comment = false)
    {
        $contract = $this->contract->getByID($contractID);

        if($_POST)
        {
            $changes = array();
            if($comment == false)
            {
                $changes = $this->contract->update($contractID);
                if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            }
            $files = $this->loadModel('file')->saveUpload('contract', $contractID);

            if($this->post->remark or $changes or $files)
            {
                $fileAction = '';
                if($this->post->remark) $fileAction = $this->post->remark;
                if($files) $fileAction = $this->lang->addFiles . join(',', $files);

                $action           = $this->post->remark == '' ? 'Edited' : 'Commented';
                $contractActionID = $this->loadModel('action')->create('contract', $contractID, $action, $fileAction);
                if($changes) $this->action->logHistory($contractActionID, $changes);

                $customerActionID = $this->loadModel('action')->create('customer', $contract->customer, 'editContract', $fileAction, html::a($this->createLink('contract', 'view', "contractID=$contractID"), $contract->name));
                if($changes) $this->action->logHistory($customerActionID, $changes);

                if($contract->order)
                {
                    foreach($contract->order as $orderID)
                    {
                        $orderActionID = $this->loadModel('action')->create('order', $orderID, 'editContract', $fileAction, html::a($this->createLink('contract', 'view', "contractID=$contractID"), $contract->name));
                        if($changes) $this->action->logHistory($orderActionID, $changes);
                    }
                }
            }

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "contractID=$contractID")));
        }

        $this->view->title          = $this->lang->contract->edit;
        $this->view->contract       = $contract; 
        $this->view->contractOrders = $this->loadModel('order', 'crm')->getByIdList($contract->order);
        $this->view->orders         = array('' => '') + $this->order->getList($mode = 'query', "customer={$contract->customer}");
        $this->view->customers      = $this->loadModel('customer')->getPairs('client');
        $this->view->contacts       = $this->loadModel('contact', 'crm')->getPairs($contract->customer);
        $this->view->users          = $this->loadModel('user')->getPairs('nodeleted,noforbidden,noclosed');
        $this->view->addresses      = $this->loadModel('address', 'crm')->getPairsByObject('customer', $contract->customer); 
        $this->view->currencyList   = $this->loadModel('common')->getCurrencyList();
        $this->view->currencySign   = $this->loadModel('common')->getCurrencySign();
        $this->display();
    }

    /**
     * The delivery of the contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return void
     */
    public function delivery($contractID)
    {
        $contract = $this->contract->getByID($contractID);
        if(!empty($_POST))
        {
            $this->contract->delivery($contractID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            if($this->post->finish)
            {
                $this->loadModel('action')->create('contract', $contractID, 'finishDelivered', $this->post->comment, '', $this->post->deliveredBy);
                $this->action->create('customer', $contract->customer, 'finishDeliverContract', $this->post->comment, html::a($this->createLink('contract', 'view', "contractID=$contractID"), $contract->name), $this->post->deliveredBy);
            }
            else
            {
                $this->loadModel('action')->create('contract', $contractID, 'Delivered', $this->post->comment, '', $this->post->deliveredBy);
                $this->action->create('customer', $contract->customer, 'deliverContract', $this->post->comment, html::a($this->createLink('contract', 'view', "contractID=$contractID"), $contract->name), $this->post->deliveredBy);
            }

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title    = $this->lang->contract->delivery;
        $this->view->contract = $contract;
        $this->view->users    = $this->loadModel('user')->getPairs('nodeleted,noforbidden,noclosed');
        $this->display();
    }

    /**
     * Edit delivery.
     * 
     * @param  int    $deliveryID 
     * @access public
     * @return void
     */
    public function editDelivery($deliveryID)
    {
        $delivery = $this->contract->getDeliveryByID($deliveryID);
        $contract = $this->contract->getByID($delivery->contract);
        if(!empty($_POST))
        {
            $this->contract->editDelivery($delivery, $contract);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title    = $this->lang->contract->editDelivery;
        $this->view->delivery = $delivery;
        $this->view->contract = $contract;
        $this->view->users    = $this->loadModel('user')->getPairs('nodeleted,noforbidden,noclosed');
        $this->display();
    }

    /**
     * Delete delivery.
     * 
     * @param  int    $deliveryID 
     * @access public
     * @return void
     */
    public function deleteDelivery($deliveryID)
    {
        $delivery = $this->contract->getDeliveryByID($deliveryID);
        $contract = $this->contract->getByID($delivery->contract);

        $this->contract->deleteDelivery($deliveryID);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => $this->lang->fail));

        $deleteInfo = sprintf($this->lang->contract->deleteDeliveryInfo, $delivery->deliveredDate);
        $this->loadModel('action')->create('contract', $contract->id, 'deletedelivered', '', $deleteInfo);

        $actionExtra = html::a($this->createLink('contract', 'view', "contractID=$contract->id"), $contract->name) . $deleteInfo; 
        $this->loadModel('action')->create('customer', $contract->customer, 'deletedelivered', $this->post->comment, $actionExtra, $this->post->returnedBy);

        $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
    }

    /**
     * Receive payments of the contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return void
     */
    public function receive($contractID)
    {
        $this->loadModel('trade', 'cash');
        $contract     = $this->contract->getByID($contractID);
        $currencySign = $this->loadModel('common')->getCurrencySign();
        if(!empty($_POST))
        {
            $return = $this->contract->receive($contractID);
            if(!empty($return['result']) && $return['result'] == 'fail') $this->send($return);

            $actionExtra = html::a($this->createLink('contract', 'view', "contractID=$contractID"), $contract->name) . $this->lang->contract->return . zget($currencySign, $this->post->currency, '') . $this->post->amount;

            if($this->post->finish)
            {
                $this->loadModel('action')->create('contract', $contractID, 'finishReturned', $this->post->comment, zget($currencySign, $this->post->currency, '') . $this->post->amount, $this->post->returnedBy);
                $this->loadModel('action')->create('customer', $contract->customer, 'finishReceiveContract', $this->post->comment, $actionExtra, $this->post->returnedBy);
            }
            else
            {
                $this->loadModel('action')->create('contract', $contractID, 'returned', $this->post->comment, zget($currencySign, $this->post->currency, '') . $this->post->amount, $this->post->returnedBy);
                $this->loadModel('action')->create('customer', $contract->customer, 'receiveContract', $this->post->comment, $actionExtra, $this->post->returnedBy);
            }
            $this->send($return);
        }

        $user   = $this->loadModel('user')->getByAccount($contract->createdBy);
        $dept   = $this->loadModel('tree')->getByID($user->dept);
        $orders = $this->dao->select('`order`')->from(TABLE_CONTRACTORDER)->where('contract')->eq($contractID)->andWhere('`order`')->ne('0')->fetchPairs();

        $productList = $this->loadModel('product')->getPairs();
        if($orders)
        {
            $productList = array();
            $this->loadModel('order', 'crm');
            foreach($orders as $orderID)
            {
                $order = $this->order->getByID($orderID);
                if($order->product) $productList += $this->dao->select('id, name')->from(TABLE_PRODUCT)->where('id')->in($order->product)->fetchPairs();
            }
        }

        $this->view->title         = $contract->name;
        $this->view->contract      = $contract;
        $this->view->users         = $this->loadModel('user')->getPairs('nodeleted,noforbidden');
        $this->view->currencySign  = $currencySign;
        $this->view->depositorList = array('') + $this->loadModel('depositor', 'cash')->getPairs($status = 'normal');
        $this->view->deptList      = $this->loadModel('tree')->getOptionMenu('dept', 0, $removeRoot = true);
        $this->view->categories    = $this->loadModel('tree')->getOptionMenu('in', 0);
        $this->view->productList   = array(0 => '') + $productList;
        $this->view->product       = isset($order->product) && strpos(trim($order->product, ','), ',') === false ? $order->product : '';
        $this->view->dept          = $dept;
        $this->display();
    }

    /**
     * Edit return.
     * 
     * @param  int    $returnID 
     * @access public
     * @return void
     */
    public function editReturn($returnID)
    {
        $return       = $this->contract->getReturnByID($returnID);
        $contract     = $this->contract->getByID($return->contract);
        $currencySign = $this->loadModel('common')->getCurrencySign();
        if(!empty($_POST))
        {
            $this->contract->editReturn($return, $contract);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title        = $this->lang->contract->editReturn;
        $this->view->return       = $return;
        $this->view->contract     = $contract;
        $this->view->users        = $this->loadModel('user')->getPairs('nodeleted,noforbidden');
        $this->view->currencySign = $currencySign;
        $this->display();
    }

    /**
     * Delete return.
     * 
     * @param  int    $returnID 
     * @access public
     * @return void
     */
    public function deleteReturn($returnID)
    {
        $return   = $this->contract->getReturnByID($returnID);
        $contract = $this->contract->getByID($return->contract);
        $currencySign = $this->loadModel('common')->getCurrencySign();

        $this->contract->deleteReturn($returnID);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => $this->lang->fail));

        $deleteInfo = sprintf($this->lang->contract->deleteReturnInfo, $return->returnedDate, zget($currencySign, $contract->currency, '') . $return->amount);
        $this->loadModel('action')->create('contract', $contract->id, 'deletereturned', '', $deleteInfo);

        $actionExtra = html::a($this->createLink('contract', 'view', "contractID=$contract->id"), $contract->name) . $deleteInfo; 
        $this->loadModel('action')->create('customer', $contract->customer, 'deletereturned', $this->post->comment, $actionExtra, $this->post->returnedBy);

        $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess));
    }

    /**
     * Cancel contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return void
     */
    public function cancel($contractID)
    {
        $contract = $this->contract->getByID($contractID);
        if(!empty($_POST))
        {
            $this->contract->cancel($contractID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action');
            $this->action->create('contract', $contractID, 'Canceled', $this->post->comment);
            $this->action->create('customer', $contract->customer, 'cancelContract', $this->post->comment, html::a($this->createLink('contract', 'view', "contractID=$contractID"), $contract->name));

            if($contract->order)
            {
                foreach($contract->order as $orderID)
                {
                    $this->action->create('order', $orderID, 'cancelContract', $this->post->comment, html::a($this->createLink('contract', 'view', "contractID=$contractID"), $contract->name));
                }
            }
            
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title      = $this->lang->cancel;
        $this->view->contractID = $contractID;
        $this->display();
    }

    /**
     * Finish contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return void
     */
    public function finish($contractID)
    {
        $contract = $this->contract->getByID($contractID);
        if(!empty($_POST))
        {
            $this->contract->finish($contractID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $this->loadModel('action')->create('contract', $contractID, 'Finished', $this->post->comment);
            $this->loadModel('action')->create('customer', $contract->customer, 'finishContract', $this->post->comment, html::a($this->createLink('contract', 'view', "contractID=$contractID"), $contract->name));

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title      = $this->lang->finish;
        $this->view->contractID = $contractID;
        $this->display();
    }

    /**
     * View contract. 
     * 
     * @param  int    $contractID 
     * @access public
     * @return void
     */
    public function view($contractID)
    {
        $this->loadModel('trade', 'cash');
        $contract = $this->contract->getByID($contractID);
        $this->loadModel('common')->checkPrivByCustomer(empty($contract) ? '0' : $contract->customer);

        /* Set allowed edit contract ID list. */
        $this->app->user->canEditContractIdList = ',' . implode(',', $this->contract->getContractsSawByMe('edit', (array)$contractID)) . ',';

        /* Save session for return link. */
        $uri = $this->app->getURI(true);
        $this->session->set('customerList', $uri);
        $this->session->set('contactList',  $uri);
        if(!$this->session->orderList) $this->session->set('orderList', $uri);

        $expenseTypes = $this->loadModel('tree')->getOptionMenu('out', 0, $removeRoot = true);
        $incomeTypes  = $this->loadModel('tree')->getOptionMenu('in', 0, $removeRoot = true);

        foreach($expenseTypes as $key => $expenseType)
        {
            $path = explode('/', trim($expenseType, '/'));
            if(count($path) > 1) array_shift($path);

            $expenseTypes[$key] = implode('/', $path);
        }

        foreach($incomeTypes as $key => $incomeType)
        {
            $path = explode('/', trim($incomeType, '/'));
            if(count($path) > 1) array_shift($path);

            $incomeTypes[$key] = implode('/', $path);
        }

        $this->view->title         = $this->lang->contract->view;
        $this->view->orders        = $this->loadModel('order', 'crm')->getByIdList($contract->order);
        $this->view->customers     = $this->loadModel('customer')->getPairs('client');
        $this->view->allCustomers  = $this->customer->getPairs();
        $this->view->contacts      = $this->loadModel('contact', 'crm')->getPairs($contract->customer);
        $this->view->products      = $this->loadModel('product')->getPairs();
        $this->view->users         = $this->loadModel('user')->getPairs();
        $this->view->addresses     = $this->loadModel('address', 'crm')->getPairsByObject('customer', $contract->customer); 
        $this->view->contract      = $contract;
        $this->view->actions       = $this->loadModel('action')->getList('contract', $contractID);
        $this->view->currencySign  = $this->loadModel('common')->getCurrencySign();
        $this->view->depositorList = $this->loadModel('depositor', 'cash')->getPairs();
        $this->view->deptList      = $this->loadModel('tree')->getPairs(0, 'dept');
        $this->view->categories    = $expenseTypes + $incomeTypes; 
        $this->view->preAndNext    = $this->common->getPreAndNextObject('contract', $contractID);
        $this->view->members       = $this->contract->getMembers($contractID);

        $this->display();
    }

    /**
     * Delete contract. 
     * 
     * @param  int    $contractID 
     * @access public
     * @return void
     */
    public function delete($contractID)
    {
        $this->contract->delete(TABLE_CONTRACT, $contractID);
        if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
        $this->send(array('result' => 'success', 'locate' => inlink('browse')));
    }

    /**
     * Manage team.
     *
     * @param  int    $contractID
     * @access public
     * @return void
     */
    public function manageTeam($contractID)
    {
        if($_POST)
        {
            $result = $this->contract->checkTeam();
            if(!empty($result['result']) && $result['result'] == 'fail') $this->send($result);

            $oldMembers = $this->contract->getMembers($contractID);
            $this->contract->manageTeam($contractID);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $members = $this->contract->getMembers($contractID);
            if($members && $members != $oldMembers)
            {
                $actionID = $this->loadModel('action')->create('contract', $contractID, 'manageTeam');
                $this->sendmail($contractID, $actionID);
            }

            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title    = $this->lang->contract->manageTeam;
        $this->view->contract = $this->contract->getByID($contractID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed,nodelete,noforbidden');
        $this->view->members  = $this->contract->getMembers($contractID);
        $this->display();
    }

    /**
     * Confirm contribution of a team.
     *
     * @param  int    $contractID
     * @param  string $status
     * @access public
     * @return void
     */
    public function confirmTeam($contractID, $status = '')
    {
        if($status == 'accept' or $status == 'reject')
        {
            $this->contract->confirmTeam($contractID, $status);
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));

            $actionID = $this->loadModel('action')->create('contract', $contractID, 'confirmTeam', '', $this->lang->contract->team->statusList[$status]);
            if($status == 'reject') $this->sendmail($contractID, $actionID);

            $this->send(array('result' => 'success', 'locate' => inlink('browse')));
        }

        $this->view->title    = $this->lang->contract->confirmTeam;
        $this->view->contract = $this->contract->getByID($contractID);
        $this->view->users    = $this->loadModel('user')->getPairs('noclosed,nodelete,noforbidden');
        $this->view->members  = $this->contract->getMembers($contractID);
        $this->display();
    }

    /**
     * Send notice or email.
     *
     * @param  int    $contractID
     * @param  int    $actionID
     * @access public
     * @return void
     */
    public function sendmail($contractID, $actionID)
    {
        /* Reset $this->output. */
        $this->clear();

        /* Get action info. */
        $action          = $this->loadModel('action')->getById($actionID);
        $history         = $this->action->getHistory($actionID);
        $action->history = isset($history[$actionID]) ? $history[$actionID] : array();

        /* Set toList and ccList. */
        $toList  = '';
        $users   = $this->loadModel('user')->getPairs();
        $subject = "{$this->lang->contract->common}#{$contractID} " . str_replace('(%)', '', $this->lang->contract->team->contribution) . ' - ' . zget($users, $this->app->user->account);
        if($action->action == 'manageteam')
        {
            $toList = $this->dao->select('account')->from(TABLE_TEAM)
                ->where('type')->eq('contract')
                ->andWhere('id')->eq($contractID)
                ->andWhere('status')->ne('accept')
                ->fetchPairs();
            $toList = implode(',', $toList);
        }
        elseif($action->action == 'confirmteam')
        {
            $toList = $this->dao->select('actor')->from(TABLE_ACTION)
                ->where('objectType')->eq('contract')
                ->andWhere('objectID')->eq($contractID)
                ->andWhere('action')->eq('manageteam')
                ->orderBy('id_desc')
                ->limit(1)
                ->fetch('actor');

            $subject .= '#' . $this->lang->contract->team->statusList['reject'];
        }

        /* send notice if user is online and return failed accounts. */
        $toList = $this->loadModel('action')->sendNotice($actionID, $toList);

        /* Create the email content. */
        $this->view->contract = $this->contract->getById($contractID);
        $this->view->members  = $this->contract->getMembers($contractID); 
        $this->view->action   = $action;
        $this->view->users    = $users;

        $mailContent = $this->parse($this->moduleName, 'sendmail');

        /* Send emails. */
        $this->loadModel('mail')->send($toList, $subject, $mailContent);
        if($this->mail->isError()) trigger_error(join("\n", $this->mail->getError()));
    }

    /**
     * Get order.
     *
     * @param  int       $customerID
     * @param  string    $status
     * @access public
     * @return string
     */
    public function getOrder($customerID, $status = '')
    {
        $orders = $this->loadModel('order', 'crm')->getOrderForCustomer($customerID, $status);

        if($this->app->getViewType() == 'json')
        {
            $this->send(array_values($orders));
        }

        $html = "<div class='input-group'><select name='order[]' class='select-order form-control'>";

        foreach($orders as $order)
        {
            if(empty($order))
            {
                $html .= "<option value='' data-real='' data-currency=''></option>";
            }
            else
            {
                $html .= "<option value='{$order->id}' data-real='{$order->plan}' data-currency='{$order->currency}'>{$order->title}</option>";
            }
        }

        $html .= '</select>';
        $html .= "<span class='input-group-addon fix-border order-currency'>￥</span>" . html::input('real[]', '', "class='order-real form-control' placeholder='{$this->lang->contract->placeholder->real}'");
        $html .= "<span class='input-group-btn'>" . html::a('javascript:;', "<i class='icon-plus'></i>", "class='plus btn'") . html::a('javascript:;', "<i class='icon-remove'></i>", "class='minus btn'") . "</span></div>";

        echo $html;
    }

    /**
     * Get option menu.
     * 
     * @param  int    $customer 
     * @access public
     * @return void
     */
    public function getOptionMenu($customer)
    {
        $contractList = $this->contract->getList($customer);
        echo "<option value=''></option>";
        foreach($contractList as $id => $contract) 
        {
            $date = date('Y-m-d', strtotime($contract->createdDate));
            echo "<option value='{$id}' data-amount='{$contract->amount}'>{$contract->name}({$contract->amount}  {$date})</option>";
        }
        exit;
    }

    /**
     * get data to export.
     * 
     * @param  string $mode 
     * @access public
     * @return void
     */
    public function export($mode = 'all')
    { 
        if($_POST)
        {
            $contractLang   = $this->lang->contract;
            $contractConfig = $this->config->contract;

            /* Create field lists. */
            $fields = explode(',', $contractConfig->list->exportFields);
            foreach($fields as $key => $fieldName)
            {
                $fieldName = trim($fieldName);
                $fields[$fieldName] = isset($contractLang->$fieldName) ? $contractLang->$fieldName : $fieldName;
                unset($fields[$key]);
            }

            $contracts = array();
            if($mode == 'all')
            {
                $contractQueryCondition = $this->session->contractQueryCondition;
                if(strpos($contractQueryCondition, 'LIMIT') !== false) $contractQueryCondition = substr($contractQueryCondition, 0, strpos($contractQueryCondition, 'LIMIT'));
                $stmt = $this->dbh->query($contractQueryCondition);
                while($row = $stmt->fetch()) $contracts[$row->id] = $row;
            }

            if($mode == 'thisPage')
            {
                $stmt = $this->dbh->query($this->session->contractQueryCondition);
                while($row = $stmt->fetch()) $contracts[$row->id] = $row;
            }

            $users        = $this->loadModel('user')->getPairs();
            $customers    = $this->loadModel('customer')->getPairs();
            $contacts     = $this->loadModel('contact', 'crm')->getPairs();
            $relatedFiles = $this->dao->select('id, objectID, pathname, title')->from(TABLE_FILE)->where('objectType')->eq('contract')->andWhere('objectID')->in(@array_keys($contracts))->fetchGroup('objectID');

            $contractOrderList = $this->dao->select('*')->from(TABLE_CONTRACTORDER)->fetchGroup('contract');
            foreach($contracts as $id => $contract)
            {
                if(isset($contractOrderList[$id]))
                {
                    $contract->order = array();
                    foreach($contractOrderList[$id] as $contractOrder)
                    {
                        $contract->order[] = $contractOrder->order;
                    }
                }
                else
                {
                    $contract->order = '';
                }
            }

            /* Get related products names. */
            $orderPairs = array();
            $products   = array();
            $orders = $this->dao->select('*')->from(TABLE_ORDER)->fetchAll('id');
            $this->loadModel('order', 'crm')->setProductsForOrders($orders);
            foreach($orders as $key => $order)
            {
                $productName = count($order->products) > 1 ? current($order->products) . $this->lang->etc : current($order->products);
                $orderPairs[$key] = sprintf($this->lang->order->titleLBL, zget($customers, $order->customer), $productName, date('Y-m-d', strtotime($order->createdDate))); 
                $products[$key]   = $order->products;
            }

            foreach($contracts as $contract)
            {
                $contract->items = htmlspecialchars_decode($contract->items);
                $contract->items = str_replace("<br />", "\n", $contract->items);
                $contract->items = str_replace('"', '""', $contract->items);

                /* fill some field with useful value. */
                if(isset($customers[$contract->customer])) $contract->customer = $customers[$contract->customer] . "(#$contract->customer)";
                if(isset($contacts[$contract->contact]))   $contract->contact  = $contacts[$contract->contact] . "(#$contract->contact)";

                if(isset($contractLang->statusList[$contract->status]))     $contract->status   = $contractLang->statusList[$contract->status];
                if(isset($contractLang->deliveryList[$contract->delivery])) $contract->delivery = $contractLang->deliveryList[$contract->delivery];
                if(isset($contractLang->returnList[$contract->return]))     $contract->return   = $contractLang->returnList[$contract->return];
                if(isset($this->lang->currencyList[$contract->currency]))   $contract->currency = $this->lang->currencyList[$contract->currency];

                if(isset($users[$contract->createdBy]))   $contract->createdBy   = $users[$contract->createdBy];
                if(isset($users[$contract->editedBy]))    $contract->editedBy    = $users[$contract->editedBy];
                if(isset($users[$contract->signedBy]))    $contract->signedBy    = $users[$contract->signedBy];
                if(isset($users[$contract->deliveredBy])) $contract->deliveredBy = $users[$contract->deliveredBy];
                if(isset($users[$contract->returnedBy]))  $contract->returnedBy  = $users[$contract->returnedBy];
                if(isset($users[$contract->finishedBy]))  $contract->finishedBy  = $users[$contract->finishedBy];
                if(isset($users[$contract->canceledBy]))  $contract->canceledBy  = $users[$contract->canceledBy];
                if(isset($users[$contract->contactedBy])) $contract->contactedBy = $users[$contract->contactedBy];

                $contract->begin         = formatTime($contract->begin, DT_DATE1);
                $contract->end           = formatTime($contract->end, DT_DATE1);
                $contract->createdDate   = formatTime($contract->createdDate, DT_DATE1);
                $contract->editedDate    = formatTime($contract->editedDate, DT_DATE1);
                $contract->signedDate    = formatTime($contract->signedDate, DT_DATE1);
                $contract->deliveredDate = formatTime($contract->deliveredDate, DT_DATE1);
                $contract->returnedDate  = formatTime($contract->returnedDate, DT_DATE1);
                $contract->finishedDate  = formatTime($contract->finishedDate, DT_DATE1);
                $contract->canceledDate  = formatTime($contract->canceledDate, DT_DATE1);
                $contract->contactedDate = formatTime($contract->contactedDate, DT_DATE1);
                $contract->nextDate      = formatTime($contract->contactedDate, DT_DATE1);

                if($contract->handlers)
                {
                    $tmpHandlers = array();
                    $handlers = explode(',', $contract->handlers);
                    foreach($handlers as $handler)
                    {
                        if(!$handler) continue;
                        $handler = trim($handler);
                        $tmpHandlers[] = isset($users[$handler]) ? $users[$handler] : $handler;
                    }

                    $contract->handlers = join("; \n", $tmpHandlers);
                }

                $contract->product = '';
                if(!empty($contract->order))
                {
                    $tmpOrders   = array();
                    $tmpProducts = array();
                    foreach($contract->order as $orderID)
                    {
                        if(!$orderID) continue;
                        $orderID = trim($orderID);
                        $tmpOrders[] = isset($orderPairs[$orderID]) ? $orderPairs[$orderID] : $orderID;
                        if(isset($products[$orderID]))
                        {
                            foreach($products[$orderID] as $product) $tmpProducts[] = $product;
                        }
                    }

                    $contract->order   = join("; \n", $tmpOrders);
                    $contract->product = join("; \n", $tmpProducts);
                }

                /* Set related files. */
                $contract->files = '';
                if(isset($relatedFiles[$contract->id]))
                {
                    foreach($relatedFiles[$contract->id] as $file)
                    {
                        $fileURL = 'http://' . $this->server->http_host . $this->config->webRoot . "data/upload/" . $file->pathname;
                        $contract->files .= html::a($fileURL, $file->title, '_blank') . '<br />';
                    }
                }
            }

            $this->post->set('fields', $fields);
            $this->post->set('rows', $contracts);
            $this->post->set('kind', 'contract');
            $this->fetch('file', 'export2CSV' , $_POST);
        }

        $this->display();
    }

    /**
     * Get addresses by ajax. 
     * 
     * @param  int    $customer 
     * @access public
     * @return void
     */
    public function ajaxGetAddresses($customer = 0)
    {
        $html      = "<option value='0'></option>";
        $addresses = $this->loadModel('address', 'crm')->getPairsByObject('customer', $customer);
        foreach($addresses as $id => $location)
        {
            $html .= "<option value='$id'>$location</option>";
        }
        echo $html;
    }
}
