<?php
/**
 * The model file for contract of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     contract
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
class contractModel extends model
{
    /**
     * Get contract by ID.
     * 
     * @param  int    $contractID 
     * @access public
     * @return object.
     */
    public function getByID($contractID = 0)
    {
        $contract = $this->dao->select('*')->from(TABLE_CONTRACT)->where('id')->eq($contractID)->fetch();

        $this->loadModel('file');
        if($contract)
        {
            $contract->order = array();
            $contractOrders  = $this->dao->select('*')->from(TABLE_CONTRACTORDER)->where('contract')->eq($contractID)->fetchAll();
            foreach($contractOrders as $contractOrder) $contract->order[] = $contractOrder->order;

            $contract->files        = $this->file->getByObject('contract', $contractID);
            $contract->returnList   = $this->getReturnList($contractID);
            $contract->deliveryList = $this->getDeliveryList($contractID);
            $contract->tradeList    = $this->getTradeList($contractID);
        }

        $contract = $this->file->replaceImgURL($contract, 'items');
        return $contract;
    }

    /**
     * Get contract list.
     * 
     * @param  int    $customer
     * @param  string $mode
     * @param  string $owner
     * @param  string $orderBy 
     * @param  object $pager 
     * @access public
     * @return array
     */
    public function getList($customer = 0, $mode = 'all', $owner = 'all', $orderBy = 'id_desc', $pager = null)
    {
        $customerIdList = $this->loadModel('customer')->getCustomersSawByMe();
        if(empty($customerIdList)) return array();
        
        /* process search condition. */
        if($this->session->contractQuery == false) $this->session->set('contractQuery', ' 1 = 1');
        $contractQuery = $this->loadModel('search')->replaceDynamic($this->session->contractQuery);

        if(strpos($orderBy, 'id') === false) $orderBy .= ', id_desc';

        if($mode == 'contactedby')
        {
            $contracts = $this->dao->select('t1.*')->from(TABLE_CONTRACT)->alias('t1')
                ->leftJoin(TABLE_DATING)->alias('t2')->on('t1.id=t2.objectID')
                ->where('t1.deleted')->eq(0)
                ->andWhere('t2.status')->eq('wait')
                ->andWhere('t2.objectType')->eq('contract')
                ->andWhere('t2.account')->eq($this->app->user->account)
                ->beginIF($customer)->andWhere('t1.customer')->eq($customer)->fi()
                ->andWhere('t1.customer')->in($customerIdList)
                ->orderBy("t1.{$orderBy}")
                ->page($pager, 't1.id')
                ->fetchAll('id');

            $this->session->set('contractOnlyCondition', false);
            $this->session->set('contractQueryCondition', $this->dao->get());
        }
        else
        {
            $contracts = $this->dao->select('*')->from(TABLE_CONTRACT)
                ->where('deleted')->eq(0)
                ->beginIF($owner == 'my' and strpos('returnedBy,deliveredBy', $mode) === false)
                ->andWhere('createdBy', true)->eq($this->app->user->account)
                ->orWhere('editedBy')->eq($this->app->user->account)
                ->orWhere('signedBy')->eq($this->app->user->account)
                ->orWhere('returnedBy')->eq($this->app->user->account)
                ->orWhere('deliveredBy')->eq($this->app->user->account)
                ->orWhere('finishedBy')->eq($this->app->user->account)
                ->orWhere('canceledBy')->eq($this->app->user->account)
                ->orWhere('contactedBy')->eq($this->app->user->account)
                ->orWhere('handlers')->like("%{$this->app->user->account}%")
                ->markRight(1)
                ->fi()
                ->beginIF($customer)->andWhere('customer')->eq($customer)->fi()
                ->andWhere('customer')->in($customerIdList)
                ->beginIF($mode == 'unfinished')->andWhere('`status`')->eq('normal')->fi()
                ->beginIF($mode == 'unreceived')->andWhere('`return`')->ne('done')->andWhere('`status`')->ne('canceled')->fi()
                ->beginIF($mode == 'undeliveried')->andWhere('`delivery`')->ne('done')->andWhere('`status`')->ne('canceled')->fi()
                ->beginIF($mode == 'canceled')->andWhere('`status`')->eq('canceled')->fi()
                ->beginIF($mode == 'finished')->andWhere('`status`')->eq('closed')->fi()
                ->beginIF($mode == 'expired')->andWhere('`end`')->lt(date(DT_DATE1))->andWhere('`status`')->ne('canceled')->fi()
                ->beginIF($mode == 'returnedBy')->andWhere('returnedBy')->eq($this->app->user->account)->fi()
                ->beginIF($mode == 'deliveredBy')->andWhere('deliveredBy')->eq($this->app->user->account)->fi()
                ->beginIF($mode == 'expire')
                ->andWhere('`end`')->ge(date(DT_DATE1))
                ->andWhere('`end`')->lt(date(DT_DATE1, strtotime('+1 month')))
                ->andWhere('`status`')->ne('canceled')
                ->fi()
                ->beginIF($mode == 'bysearch')->andWhere($contractQuery)->fi()
                ->orderBy($orderBy)
                ->page($pager)
                ->fetchAll('id');

            $this->session->set('contractOnlyCondition', true);
            $this->session->set('contractQueryCondition', $this->dao->get());
        }

        return $contracts;
    }

    /**
     * Get contract pairs.
     * 
     * @param  int    $customerID
     * @access public
     * @return array
     */
    public function getPairs($customerID = 0, $orderBy = 'id_desc')
    {
        return $this->dao->select('id, name')->from(TABLE_CONTRACT)
            ->where(1)
            ->beginIF($customerID)->andWhere('customer')->eq($customerID)->fi()
            ->andWhere('deleted')->eq(0)
            ->orderBy($orderBy)
            ->fetchPairs('id', 'name');
    }

    /**
     * Get my contract id list.
     * 
     * @param  string $type        view|edit
     * @param  array  $contractIdList 
     * @param  object $user
     * @access public
     * @return array
     */
    public function getContractsSawByMe($type = 'view', $contractIdList = array(), $user = null)
    {
        if(!$user) $user = $this->app->user;

        $customerIdList = $this->loadModel('customer')->getCustomersSawByMe($type, array(), $user);
        $contractList   = $this->dao->select('id')->from(TABLE_CONTRACT)
            ->where('deleted')->eq(0)
            ->beginIF(!empty($contractIdList))->andWhere('id')->in($contractIdList)->fi()
            ->beginIF(!isset($user->rights['crm']['manageall']) and ($user->admin != 'super'))
            ->andWhere('customer')->in($customerIdList)
            ->fi()
            ->fetchPairs();

        return $contractList;
    }

    /**
     * Get return by ID.
     * 
     * @param  int    $returnID 
     * @access public
     * @return object
     */
    public function getReturnByID($returnID = 0)
    {
        return $this->dao->select('*')->from(TABLE_PLAN)->where('id')->eq($returnID)->fetch();
    }

    /**
     * Get returnList of its contract.
     * 
     * @param  int|array $contractID 
     * @param  string    $orderBy
     * @access public
     * @return array
     */
    public function getReturnList($contractID = 0, $orderBy = 'id_desc')
    {
        $returnList = $this->dao->select('*')->from(TABLE_PLAN)
            ->where(1)
            ->beginIF(is_array($contractID))->andWhere('contract')->in($contractID)->fi()
            ->beginIF(!is_array($contractID))->andWhere('contract')->eq($contractID)->fi()
            ->orderBy($orderBy)
            ->fetchAll();
        if(empty($returnList)) return $returnList;

        $tradeIdList = array();
        foreach($returnList as $return) $tradeIdList[] = $return->trade;
        if(empty($tradeIdList)) return $returnList;

        $tradeDepositorList = array();
        $tradeList     = $this->dao->select('id,depositor,currency')->from(TABLE_TRADE)->where('id')->in($tradeIdList)->fetchAll('id');
        $depositorList = $this->loadModel('depositor', 'cash')->getPairs();
        foreach($tradeList as $trade) $tradeDepositorList[$trade->id] = zget($depositorList, $trade->depositor);
        foreach($returnList as $return)
        {
            $return->depositor = zget($tradeDepositorList, $return->trade, '');
            $return->currency  = isset($tradeList[$return->trade]->currency) ? $tradeList[$return->trade]->currency : zget($this->config->setting, 'mainCurrency', 'rmb');
        }
        return $returnList;
    }

    /**
     * Get delivery by ID.
     * 
     * @param  int    $deliveryID 
     * @access public
     * @return object
     */
    public function getDeliveryByID($deliveryID = 0)
    {
        return $this->dao->select('*')->from(TABLE_DELIVERY)->where('id')->eq($deliveryID)->fetch();
    }

    /**
     * Get trade by contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return object
     */
    public function getTradeList($contractID = 0, $orderBy = '`type`_desc')
    {
        return $this->dao->select('*')->from(TABLE_TRADE)->where('contract')->eq($contractID)->andWhere('parent')->eq(0)->orderBy($orderBy)->fetchAll();
    }

    /**
     * Get deliveryList of its contract.
     * 
     * @param  int    $contractID 
     * @param  string $orderBy
     * @access public
     * @return array
     */
    public function getDeliveryList($contractID = 0, $orderBy = 'id_desc')
    {
        return $this->dao->select('*')->from(TABLE_DELIVERY)->where('contract')->eq($contractID)->orderBy($orderBy)->fetchAll();
    }

    /**
     * Get member list.
     *
     * @param  int    $contractID
     * @access public
     * @return array
     */
    public function getMembers($contractID)
    {
        return $this->dao->select('account, contribution, status')->from(TABLE_TEAM)
            ->where('type')->eq('contract')
            ->andWhere('id')->eq($contractID)
            ->orderBy('contribution_desc')
            ->fetchAll('account');
    }

    /**
     * Create contract.
     * 
     * @access public
     * @return int|bool
     */
    public function create()
    {
        $now = helper::now();
        $contract = fixer::input('post')
            ->add('createdBy', $this->app->user->account)
            ->add('createdDate', $now)
            ->add('status', 'normal')
            ->add('delivery', 'wait')
            ->add('return', 'wait')
            ->setDefault('order', array())
            ->setDefault('real', array())
            ->setDefault('begin', '0000-00-00')
            ->setDefault('end', '0000-00-00')
            ->setDefault('signedDate', substr($now, 0, 10))
            ->join('handlers', ',')
            ->stripTags('items', $this->config->allowedTags)
            ->remove('createAddress, newAddress')
            ->get();

        if($this->post->createAddress)
        {
            $address = new stdclass();
            $address->objectType = 'customer';
            $address->objectID   = $contract->customer;
            $address->title      = $this->lang->contract->address;
            $address->location   = $this->post->newAddress;

            $this->dao->insert(TABLE_ADDRESS)->data($address)->autoCheck()->exec();

            if(dao::isError()) return false;

            $addressID = $this->dao->lastInsertId();
            $contract->address = $addressID;
        }

        if($contract->order)
        {
            $products = array();
            $orders   = $this->dao->select('id, product')->from(TABLE_ORDER)->where('id')->in($contract->order)->fetchAll();
            foreach($orders as $order)
            {
                foreach(explode(',', trim($order->product, ',')) as $product)
                {
                    $products[$product] = $product;
                }
            }
            $contract->product = empty($products) ? '' : ',' . implode(',', $products) . ',';
        }

        $contract = $this->loadModel('file')->processImgURL($contract, $this->config->contract->editor->create['id']);
        $this->dao->insert(TABLE_CONTRACT)->data($contract, 'order,uid,files,labels,real')
            ->autoCheck()
            ->batchCheck($this->config->contract->require->create, 'notempty')
            ->checkIF($contract->end != '0000-00-00', 'end', 'ge', $contract->begin)
            ->exec();

        if(dao::isError()) return false;

        $contractID = $this->dao->lastInsertID();

        $this->file->updateObjectID($this->post->uid, $contractID, 'contract');

        foreach($contract->order as $key => $orderID)
        {
            if($orderID)
            {
                $data = new stdclass();
                $data->contract = $contractID;
                $data->order    = $orderID;
                $this->dao->insert(TABLE_CONTRACTORDER)->data($data)->exec();

                $order = new stdclass();
                $order->status     = 'signed';
                $order->real       = $contract->real[$key];
                $order->signedBy   = $contract->signedBy;
                $order->signedDate = $contract->signedDate;
                $this->dao->update(TABLE_ORDER)->data($order)->where('id')->eq($orderID)->exec();

                if(dao::isError()) return false;
                $this->loadModel('action')->create('order', $orderID, 'Signed', '', $contract->real[$key]);
            }
        }

        /* Update customer info. */
        $customer = new stdclass();
        $customer->status = 'signed';
        $customer->editedDate = helper::now();
        $this->dao->update(TABLE_CUSTOMER)->data($customer)->where('id')->eq($contract->customer)->exec();

        $this->loadModel('file')->saveUpload('contract', $contractID);

        return $contractID;
    }

    /**
     * Update contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return bool
     */
    public function update($contractID)
    {
        $now         = helper::now();
        $oldContract = $this->getByID($contractID);
        $contract    = fixer::input('post')
            ->join('handlers', ',')
            ->add('editedBy', $this->app->user->account)
            ->add('editedDate', $now)
            ->setDefault('order', array())
            ->setDefault('real', array())
            ->setDefault('customer', $oldContract->customer)
            ->setDefault('signedDate', '0000-00-00')
            ->setDefault('finishedDate', '0000-00-00')
            ->setDefault('canceledDate', '0000-00-00')
            ->setDefault('deliveredDate', '0000-00-00')
            ->setDefault('returnedDate', '0000-00-00')
            ->setDefault('begin', '0000-00-00')
            ->setDefault('end', '0000-00-00')
            ->setIF($this->post->status == 'normal', 'canceledBy', '')
            ->setIF($this->post->status == 'normal', 'canceledDate', '0000-00-00')
            ->setIF($this->post->status == 'normal', 'finishedBy', '')
            ->setIF($this->post->status == 'normal', 'finishedDate', '0000-00-00')
            ->setIF($this->post->status == 'cancel' and $this->post->canceledBy == '', 'canceledBy', $this->app->user->account)
            ->setIF($this->post->status == 'cancel' and $this->post->canceledDate == '0000-00-00', 'canceledDate', $now)
            ->setIF($this->post->status == 'finished' and $this->post->finishedBy == '', 'finishedBy', $this->app->user->account)
            ->setIF($this->post->status == 'finished' and $this->post->finishedDate == '0000-00-00', 'finishedDate', $now)
            ->remove('files,labels')
            ->stripTags('items', $this->config->allowedTags)
            ->get();

        if($oldContract->order != $contract->order)
        {
            $products = array();
            $orders   = $this->dao->select('id, product')->from(TABLE_ORDER)->where('id')->in($contract->order)->fetchAll();
            foreach($orders as $order)
            {
                foreach(explode(',', trim($order->product, ',')) as $product)
                {
                    $products[$product] = $product;
                }
            }
            $contract->product = empty($products) ? '' : ',' . implode(',', $products) . ',';
        }

        $contract = $this->loadModel('file')->processImgURL($contract, $this->config->contract->editor->edit['id']);
        $this->dao->update(TABLE_CONTRACT)->data($contract, 'uid,order,real')
            ->where('id')->eq($contractID)
            ->autoCheck()
            ->batchCheck($this->config->contract->require->edit, 'notempty')
            ->checkIF($oldContract->end != '0000-00-00', 'end', 'ge', $oldContract->begin)
            ->exec();
        
        $this->file->updateObjectID($this->post->uid, $contractID, 'contract');

        if(!dao::isError())
        {
            if($contract->order)
            {
                $oldOrders = $this->loadModel('order', 'crm')->getByIdList($contract->order);
                foreach($contract->order as $key => $orderID)
                {
                    if(!$orderID) continue;
                    $real[$key] = $oldOrders[$orderID]->real;
                }

                if($oldContract->order != $contract->order || $real != $contract->real)
                {
                    $this->dao->delete()->from(TABLE_CONTRACTORDER)->where('contract')->eq($contractID)->exec();
                    foreach($contract->order as $key => $orderID)
                    {
                        $oldOrder = $this->loadModel('order', 'crm')->getByID($orderID);

                        $contractOrder = new stdclass();
                        $contractOrder->contract = $contractID;
                        $contractOrder->order    = $orderID;
                        $this->dao->insert(TABLE_CONTRACTORDER)->data($contractOrder)->exec();

                        $order = new stdclass();
                        $order->real       = $contract->real[$key];
                        $order->signedBy   = $contract->signedBy;
                        $order->signedDate = $contract->signedDate;
                        $order->status     = 'signed';

                        $this->dao->update(TABLE_ORDER)->data($order)->where('id')->eq($orderID)->exec();

                        if(dao::isError()) return false;

                        $changes  = commonModel::createChanges($oldOrder, $order);
                        $actionID = $this->loadModel('action')->create('order', $orderID, 'Edited');
                        $this->action->logHistory($actionID, $changes);
                    }
                }
            }

            if($oldContract->status == 'canceled' and $contract->status == 'normal')
            {
                foreach($contract->order as $key => $orderID)
                {
                    $order = new stdclass();
                    $order->status     = 'signed';
                    $order->real       = $contract->real[$key];
                    $order->signedBy   = $contract->signedBy;
                    $order->signedDate = $contract->signedDate;

                    $this->dao->update(TABLE_ORDER)->data($order)->where('id')->eq($orderID)->exec();
                    if(dao::isError()) return false;
                }
            }

            if($oldContract->status == 'normal' and $contract->status == 'canceled')
            {
                foreach($contract->order as $orderID)
                {
                    $order = new stdclass();
                    $order->status     = 'normal';
                    $order->real       = 0;
                    $order->signedBy   = '';
                    $order->signedDate = '0000-00-00';

                    $this->dao->update(TABLE_ORDER)->data($order)->where('id')->eq($orderID)->exec();
                    if(dao::isError()) return false;
                }
            }
            
            return commonModel::createChanges($oldContract, $contract);
        }

        return false;
    }

    /**
     * The delivery of the contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return bool
     */
    public function delivery($contractID)
    {
        $now = helper::now();
        $data = fixer::input('post')
            ->add('contract', $contractID)
            ->setDefault('deliveredBy', $this->app->user->account)
            ->setDefault('deliveredDate', $now)
            ->stripTags('comment', $this->config->allowedTags)
            ->get();

        $data = $this->loadModel('file')->processImgURL($data, $this->config->contract->editor->delivery['id']);
        $this->dao->insert(TABLE_DELIVERY)->data($data, $skip = 'uid, handlers, finish')->autoCheck()->exec();

        if(!dao::isError())
        {
            $contract = fixer::input('post')
                ->add('delivery', 'doing')
                ->add('editedBy', $this->app->user->account)
                ->add('editedDate', $now)
                ->setDefault('deliveredBy', $this->app->user->account)
                ->setDefault('deliveredDate', $now)
                ->setIF($this->post->finish, 'delivery', 'done')
                ->join('handlers', ',')
                ->remove('finish')
                ->get();

            $this->dao->update(TABLE_CONTRACT)->data($contract, $skip = 'uid, comment')
                ->autoCheck()
                ->where('id')->eq($contractID)
                ->exec();

            return !dao::isError();
        }

        return false;
    }

    /**
     * Edit delivery of the contract.
     * 
     * @param  object $delivery 
     * @param  object $contract 
     * @access public
     * @return bool
     */
    public function editDelivery($delivery, $contract)
    {
        $now = helper::now();
        $data = fixer::input('post')
            ->add('contract', $contract->id)
            ->setDefault('deliveredBy', $this->app->user->account)
            ->setDefault('deliveredDate', $now)
            ->stripTags('comment', $this->config->allowedTags)
            ->get();

        $data = $this->loadModel('file')->processImgURL($data, $this->config->contract->editor->editdelivery['id']);
        $this->dao->update(TABLE_DELIVERY)->data($data, $skip = 'uid, handlers, finish')->where('id')->eq($delivery->id)->autoCheck()->exec();

        if(!dao::isError())
        {
            $changes = commonModel::createChanges($delivery, $data);
            if($changes)
            {
                $actionID = $this->loadModel('action')->create('contract', $contract->id, 'editDelivered');
                $this->action->logHistory($actionID, $changes);
            }

            $deliveryList = $this->getDeliveryList($delivery->contract, 'deliveredDate_desc');

            $contractData = new stdclass();
            $contractData->delivery      = 'doing';
            $contractData->editedBy      = $this->app->user->account;
            $contractData->editedDate    = $now;
            $contractData->handlers      = implode(',', $this->post->handlers);
            $contractData->deliveredBy   = current($deliveryList)->deliveredBy;
            $contractData->deliveredDate = current($deliveryList)->deliveredDate;

            if($this->post->finish) $contractData->delivery = 'done';

            $this->dao->update(TABLE_CONTRACT)->data($contractData, $skip = 'uid, comment')->where('id')->eq($contract->id)->exec();

            return !dao::isError();
        }
        return false;
    }

    /**
     * Delete return.
     * 
     * @param  int   $returnID
     * @access public
     * @return bool
     */
    public function deleteDelivery($deliveryID)
    {
        $delivery = $this->getDeliveryByID($deliveryID);

        $this->dao->delete()->from(TABLE_DELIVERY)->where('id')->eq($deliveryID)->exec();

        $deliveryList = $this->getDeliveryList($delivery->contract, 'deliveredDate_desc');
        $contract = new stdclass();
        if(empty($deliveryList))
        {
            $contract->delivery      = 'wait';
            $contract->deliveredBy   = '';
            $contract->deliveredDate = '0000-00-00';
        }
        else
        {
            $contract->delivery       = 'doing';
            $contract->deliveredBy   = current($deliveryList)->deliveredBy;
            $contract->deliveredDate = current($deliveryList)->deliveredDate;
        }

        $this->dao->update(TABLE_CONTRACT)->data($contract)->where('id')->eq($delivery->contract)->autoCheck()->exec();

        return !dao::isError();
    }

    /**
     * Receive payments of the contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return bool
     */
    public function receive($contractID)
    {
        $contract = $this->getByID($contractID);

        $now  = helper::now();
        $data = fixer::input('post')
            ->add('contract', $contractID)
            ->setDefault('returnedBy', $this->app->user->account)
            ->setDefault('returnedDate', $now)
            ->setIF($this->post->returnedDate == '0000-00-00', 'returnedDate', $now)
            ->remove($this->config->contract->receiveNoneedFields)
            ->get();

        if($this->post->createTrade)
        {
            if(!$this->post->depositor) return array('result' => 'fail', 'message' => array('depositor' => sprintf($this->lang->error->notempty, $this->lang->trade->depositor)));
            if($this->post->currency != $this->config->setting->mainCurrency && !$this->post->exchangeRate) return array('result' => 'fail', 'message' => array('depositor' => sprintf($this->lang->error->notempty, $this->lang->trade->exchangeRate)));
        }

        if(!$this->post->continue and $this->post->createTrade)
        {
            $existTrades = $this->dao->select('*')->from(TABLE_TRADE)
                ->where('money')->eq($data->amount)
                ->andWhere('date')->eq(substr($data->returnedDate, 0, 10))
                ->andWhere('contract')->eq($contractID)
                ->fetchAll();

            if(!empty($existTrades)) return array('result' => 'fail', 'error' => $this->lang->trade->unique);
        }

        $this->dao->insert(TABLE_PLAN)
            ->data($data, $skip = 'uid, comment')
            ->autoCheck()
            ->batchCheck($this->config->contract->require->receive, 'notempty')
            ->exec();
        $planID = $this->dao->lastInsertId();

        if(!dao::isError())
        {
            $contractData = new stdclass();
            $contractData->return       = 'doing';
            $contractData->editedBy     = $this->app->user->account;
            $contractData->editedDate   = $now;
            $contractData->handlers     = implode(',', $this->post->handlers);
            $contractData->returnedBy   = $this->post->returnedBy ? $this->post->returnedBy : $this->app->user->account;
            $contractData->returnedDate = $this->post->returnedDate ? $this->post->returnedDate : $now;
            if($this->post->finish) $contractData->return = 'done';

            $this->dao->update(TABLE_CONTRACT)->data($contractData, $skip = 'uid, comment')->where('id')->eq($contractID)->exec();

            if(!dao::isError() and $this->post->finish) $this->dao->update(TABLE_CUSTOMER)->set('status')->eq('payed')->where('id')->eq($contract->customer)->exec();

            if($this->post->createTrade)
            {
                $trade = fixer::input('post')
                    ->add('type', 'in')
                    ->add('trader', $contract->customer)
                    ->add('contract', $contractID)
                    ->add('money', $this->post->amount)
                    ->add('handlers', $contractData->returnedBy)
                    ->add('date', substr($contractData->returnedDate, 0, 10))
                    ->add('desc', strip_tags($this->post->comment))
                    ->add('createdBy', $this->app->user->account)
                    ->add('createdDate', $now)
                    ->remove('finish,amount,returnedBy,returnedDate,createTrade,continue,currencyLabel')
                    ->get();

                $this->dao->insert(TABLE_TRADE)->data($trade, $skip = 'uid,comment')->autoCheck()->exec();
                $tradeID = $this->dao->lastInsertId();
                $this->dao->update(TABLE_PLAN)->set('trade')->eq($tradeID)->where('id')->eq($planID)->exec();

                $actionExtra = html::a(helper::createLink('contract', 'view', "contractID=$contractID"), $contract->name) . $this->lang->contract->return . ' ' . zget($this->lang->currencySymbols, $trade->currency, '') . $this->post->amount;
                $this->loadModel('action')->create('trade', $tradeID, 'receiveContract', $this->post->comment, $actionExtra, $this->post->returnedBy);
            }
            
            if(dao::isError()) return array('result' => 'fail', 'message' => dao::getError());
            return array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => inlink('view', "contractID=$contractID"));
        }

        return array('result' => 'fail', 'message' => dao::getError());
    }

    /**
     * Edit return.
     * 
     * @param  object    $return 
     * @access public
     * @return bool
     */
    public function editReturn($return, $contract)
    {
        $now = helper::now();
        $data = fixer::input('post')
            ->add('contract', $contract->id)
            ->setDefault('returnedBy', $this->app->user->account)
            ->setDefault('returnedDate', $now)
            ->setIF($this->post->returnedDate == '0000-00-00', 'returnedDate', $now)
            ->remove('finish,handlers')
            ->get();

        $this->dao->update(TABLE_PLAN)
            ->data($data, $skip = 'uid, comment')
            ->where('id')->eq($return->id)
            ->autoCheck()
            ->batchCheck($this->config->contract->require->receive, 'notempty')
            ->exec();

        if(!dao::isError())
        {
            $changes = commonModel::createChanges($return, $data);
            if($changes or $this->post->comment)
            {
                $actionID = $this->loadModel('action')->create('contract', $contract->id, 'editReturned', $this->post->comment);
                if($changes) $this->action->logHistory($actionID, $changes);
            }

            $returnList = $this->getReturnList($return->contract, 'returnedDate_desc');

            $contractData = new stdclass();
            $contractData->return       = 'doing';
            $contractData->editedBy     = $this->app->user->account;
            $contractData->editedDate   = $now;
            $contractData->handlers     = implode(',', $this->post->handlers);
            $contractData->returnedBy   = current($returnList)->returnedBy;
            $contractData->returnedDate = current($returnList)->returnedDate;

            if($this->post->finish) $contractData->return = 'done';

            $this->dao->update(TABLE_CONTRACT)->data($contractData, $skip = 'uid, comment')->where('id')->eq($contract->id)->exec();

            if(!dao::isError() and $this->post->finish) $this->dao->update(TABLE_CUSTOMER)->set('status')->eq('payed')->where('id')->eq($contract->customer)->exec();

            return !dao::isError();
        }

        return false;
    }

    /**
     * Delete return.
     * 
     * @param  int   $returnID
     * @access public
     * @return bool
     */
    public function deleteReturn($returnID)
    {
        $return = $this->getReturnByID($returnID);

        $this->dao->delete()->from(TABLE_PLAN)->where('id')->eq($returnID)->exec();

        $returnList = $this->getReturnList($return->contract, 'returnedDate_desc');
        $contract = new stdclass();
        if(empty($returnList))
        {
            $contract->return       = 'wait';
            $contract->returnedBy   = '';
            $contract->returnedDate = '0000-00-00';
        }
        else
        {
            $contract->return       = 'doing';
            $contract->returnedBy   = current($returnList)->returnedBy;
            $contract->returnedDate = current($returnList)->returnedDate;
        }

        $this->dao->update(TABLE_CONTRACT)->data($contract)->where('id')->eq($return->contract)->autoCheck()->exec();

        return !dao::isError();
    }

    /**
     * Cancel contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return bool
     */
    public function cancel($contractID)
    {
        $contract = new stdclass();
        $contract->status       = 'canceled';
        $contract->canceledBy   = $this->app->user->account;
        $contract->canceledDate = helper::now();
        $contract->editedBy     = $this->app->user->account;
        $contract->editedDate   = helper::now();

        $this->dao->update(TABLE_CONTRACT)->data($contract, $skip = 'uid, comment')
            ->autoCheck()
            ->where('id')->eq($contractID)
            ->exec();

        if(!dao::isError()) 
        {
            $contract = $this->getByID($contractID);
            if($contract->order)
            {
                foreach($contract->order as $orderID)
                {
                    $order = new stdclass(); 
                    $order->status     = 'normal';
                    $order->signedDate = '0000-00-00';
                    $order->real       = 0;
                    $order->signedBy   = '';

                    $this->dao->update(TABLE_ORDER)->data($order)->autoCheck()->where('id')->eq($orderID)->exec();
                }
                return !dao::isError();
            }
            return true;
        }
        return false;
    }

    /**
     * Finish contract.
     * 
     * @param  int    $contractID 
     * @access public
     * @return bool
     */
    public function finish($contractID)
    {
        $contract = new stdclass();
        $contract->status       = 'closed';
        $contract->finishedBy   = $this->app->user->account;
        $contract->finishedDate = helper::now();
        $contract->editedBy     = $this->app->user->account;
        $contract->editedDate   = helper::now();

        $this->dao->update(TABLE_CONTRACT)->data($contract, $skip = 'uid, comment')
            ->autoCheck()
            ->where('id')->eq($contractID)
            ->exec();

        return !dao::isError();
    }

    /**
     * Check team.
     *
     * @access public
     * @return bool | array
     */
    public function checkTeam()
    {
        $total  = 0;
        $errors = array();
        foreach($this->post->account as $key => $account)
        {
            $contribution = $this->post->contribution[$key];

            if(!$account or !$contribution) continue;

            if(!is_numeric($contribution)) $errors["contribution{$key}"] = $this->lang->contract->error->wrongContribution;

            $total += (float)$contribution;
        }
        if($total > 100) $errors['totalContribution'] = $this->lang->contract->error->wrongTotalContribution;

        if($errors) return array('result' => 'fail', 'message' => $errors);

        return true;
    }

    /**
     * Check if all members of a contract accepted the contribution.
     *
     * @param  int    $contractID
     * @access public
     * @return bool
     */
    public function checkAllMembersAccepted($contractID)
    {
        $members = $this->dao->select('status')->from(TABLE_TEAM)
            ->where('type')->eq('contract')
            ->andWhere('id')->eq($contractID)
            ->fetchPairs();

        return count($members) == 1 && reset($members) == 'accept';
    }

    /**
     * Manage team.
     *
     * @param  int    $contractID
     * @access public
     * @return bool
     */
    public function manageTeam($contractID)
    {
        $this->dao->delete()->from(TABLE_TEAM)->where('type')->eq('contract')->andWhere('id')->eq($contractID)->exec();

        if(!$this->post->account) return true;

        $member = new stdclass();
        $member->type = 'contract';
        $member->id   = $contractID;
        foreach($this->post->account as $key => $account)
        {
            $contribution = (float)$this->post->contribution[$key];

            if(!$account or !$contribution) continue;

            $member->account      = $account;
            $member->contribution = $contribution;
            $member->status       = $account == $this->app->user->account ? 'accept' : 'wait';

            $this->dao->insert(TABLE_TEAM)->data($member)->autoCheck()->exec();
        }

        return !dao::isError();
    }

    /**
     * Accept or reject the contribution of a contract.
     *
     * @param  int    $contractID
     * @param  string $status
     * @access public
     * @return bool
     */
    public function confirmTeam($contractID, $status)
    {
        if($status != 'accept' && $status != 'reject') return false;

        $this->dao->update(TABLE_TEAM)
            ->set('status')->eq($status)
            ->where('type')->eq('contract')
            ->andWhere('id')->eq($contractID)
            ->andWhere('account')->eq($this->app->user->account)
            ->exec();

        return !dao::isError();
    }

    /**
     * Build operate menu.
     * 
     * @param  object $contract 
     * @param  string $class 
     * @param  string $type 
     * @access public
     * @return string
     */
    public function buildOperateMenu($contract, $class = '', $type = 'browse')
    {
        $canReceive  = commonModel::hasPriv('contract', 'receive');
        $canDelivery = commonModel::hasPriv('contract', 'delivery');
        $canFinish   = commonModel::hasPriv('contract', 'finish');
        $canEdit     = commonModel::hasPriv('contract', 'edit');
        $canCancel   = commonModel::hasPriv('contract', 'cancel');
        $canDelete   = commonModel::hasPriv('contract', 'delete');

        $menu = '';
        if($type == 'view') $menu .= "<div class='btn-group'>";

        $history = $type == 'view' ? '&history=' : '';
        $menu .= commonModel::printLink('action', 'createRecord', "objectType=contract&objectID={$contract->id}&customer={$contract->customer}$history", $this->lang->contract->record, "class='$class' data-toggle='modal' data-width='800'", false);
        $menu .= commonModel::printLink('customer', 'contact', "customerID={$contract->customer}", $this->lang->contract->contact, "data-toggle='modal' class='$class'", false);

        if($contract->return != 'done' and $contract->status == 'normal' and $canReceive)
        {
            $menu .= html::a(helper::createLink('crm.contract', 'receive',  "contractID=$contract->id"), $this->lang->contract->return, "data-toggle='modal' class='$class'");
        }
        else
        {
            $menu .= "<a href='###' disabled='disabled' class='disabled  $class'>" . $this->lang->contract->return . '</a> ';
        }

        if($contract->delivery != 'done' and $contract->status == 'normal' and $canDelivery)
        {
            $menu .= html::a(helper::createLink('crm.contract', 'delivery', "contractID=$contract->id"), $this->lang->contract->delivery, "data-toggle='modal' class='$class'");
        }
        else
        {
            $menu .= "<a href='###' disabled='disabled' class='disabled $class'>" . $this->lang->contract->delivery . '</a> ';
        }

        if($type == 'view') $menu .= "</div><div class='btn-group'>";

        if($contract->status == 'normal' and $contract->return == 'done' and $contract->delivery == 'done' and $canFinish)
        {
            $menu .= html::a(helper::createLink('crm.contract', 'finish', "contractID=$contract->id"), $this->lang->finish, "data-toggle='modal' class='$class'");
        }
        else
        {
            $menu .= "<a href='###' disabled='disabled' class='disabled $class'>" . $this->lang->finish . '</a> ';
        }

        if($canEdit) $menu .= commonModel::printLink('crm.contract', 'edit', "contractID=$contract->id", $this->lang->edit, "class='$class'", false);
        if($type == 'view') $menu .= commonModel::printLink('crm.contract', 'manageTeam', "contractID=$contract->id", $this->lang->contract->team->common, "data-toggle='modal' class='$class'", false);

        if($type == 'view')
        {
            $menu .= "</div><div class='btn-group'>";
            if($contract->status == 'normal' and !($contract->return == 'done' and $contract->delivery == 'done') and $canCancel)
            {
                $menu .= html::a(helper::createLink('crm.contract', 'cancel', "contractID=$contract->id"), $this->lang->cancel, "data-toggle='modal' class='$class'");
            }
            else
            {
                $menu .= "<a href='###' disabled='disabled' class='disabled $class'>" . $this->lang->cancel . '</a> ';
            }

            if(($contract->status == 'canceled' or ($contract->status == 'normal' and !($contract->return == 'done' and $contract->delivery == 'done'))) and $canDelete)
            {
                $menu .= html::a(helper::createLink('crm.contract', 'delete', "contractID=$contract->id"), $this->lang->delete, "class='deleter $class'");
            }
            else
            {
                $menu .= "<a href='###' disabled='disabled' class='disabled $class'>" . $this->lang->delete . '</a> ';
            }
            if(commonModel::hasPriv('crm.contract', 'edit')) $menu .= html::a('#commentBox', $this->lang->comment, "class='btn btn-default' onclick=setComment()");
        }

        if($type == 'browse')
        {
            $menu .="<div class='dropdown'><a data-toggle='dropdown' href='javascript:;'>" . $this->lang->more . "<span class='caret'></span> </a><ul class='dropdown-menu pull-right'>";
            $menu .= commonModel::printLink('crm.contract', 'manageTeam', "contractID=$contract->id", $this->lang->contract->team->common, "data-toggle='modal' class='$class'", false, '', 'li');

            if($contract->status == 'normal' and !($contract->return == 'done' and $contract->delivery == 'done') and $canCancel)
            {
                $menu .= '<li>' . html::a(helper::createLink('crm.contract', 'cancel', "contractID=$contract->id"), $this->lang->cancel, "data-toggle='modal' class='$class'") . '</li>';
            }
            else
            {
                $menu .= "<li><a href='###' disabled='disabled' class='disabled $class'>" . $this->lang->cancel . '</a></li> ';
            }

            if(($contract->status == 'canceled' or ($contract->status == 'normal' and !($contract->return == 'done' and $contract->delivery == 'done'))) and $canDelete)
            {
                $menu .= '<li>' . html::a(helper::createLink('crm.contract', 'delete', "contractID=$contract->id"), $this->lang->delete, "class='reloadDeleter $class'") . '<li>';
            }
            else
            {
                $menu .= "<li><a href='###' disabled='disabled' class='disabled $class'>" . $this->lang->delete . '</a></li> ';
            }
            $menu .= '</ul>';
        }

        $menu .= "</div>";

        return $menu;
    }

    /**
     * Count amount.
     * 
     * @param  array  $contracts 
     * @access public
     * @return array
     */
    public function countAmount($contracts)
    {
        $totalAmount  = array();
        $currencyList = $this->loadModel('common')->getCurrencyList();
        $currencySign = $this->common->getCurrencySign();
        $totalReturn  = $this->dao->select('*')->from(TABLE_PLAN)->fetchGroup('contract');

        foreach($contracts as $contract)
        {
            if($contract->status == 'canceled') continue;
            foreach($currencyList as $key => $currency)
            {
                if($contract->currency == $key)
                {
                    if(!isset($totalAmount['contract'][$key]))     $totalAmount['contract'][$key] = 0;
                    if(!isset($totalAmount['return'][$key]))       $totalAmount['return'][$key] = 0;
                    if(!isset($totalAmount['currentMonth'][$key])) $totalAmount['currentMonth'][$key] = 0;

                    $totalAmount['contract'][$key] += $contract->amount;
                    
                    if(isset($totalReturn[$contract->id])) 
                    {
                        foreach($totalReturn[$contract->id] as $return) 
                        {
                            $totalAmount['return'][$key] += $return->amount;
                            if(date('Y-m', strtotime($return->returnedDate)) == date('Y-m')) $totalAmount['currentMonth'][$key] += $return->amount;
                        }
                    }
                }
            }
        }

        foreach($totalAmount as $type => $currencyAmount) foreach($currencyAmount as $currency => $amount) $totalAmount[$type][$currency] = "<span title='$amount'>" . $currencySign[$currency] . commonModel::tidyMoney($amount) . "</span>";

        return $totalAmount;
    }

    /**
     * Update contract product.
     *
     * @param  int    $contractID
     * @access public
     * @return bool
     */
    public function updateContractProduct($contractID)
    {
        $products = array();
        $orders   = $this->dao->select('`order`')->from(TABLE_CONTRACTORDER)->where('contract')->eq($contractID)->fetchPairs();
        if($orders)
        {
            $orders = $this->dao->select('id, product')->from(TABLE_ORDER)->where('id')->in($orders)->fetchAll();
            foreach($orders as $order)
            {
                foreach(explode(',', trim($order->product, ',')) as $product)
                {
                    $products[$product] = $product;
                }
            }
        }
        $product = empty($products) ? '' : ',' . implode(',', $products) . ',';
        $this->dao->update(TABLE_CONTRACT)->set('product')->eq($product)->where('id')->eq($contractID)->exec();

        return !dao::isError();
    }
}
