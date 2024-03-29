<?php 
/**
 * The view file for the method of view of refund module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     refund 
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php include '../../common/view/header.html.php'; ?>
<?php js::set('mode', $mode == 'review' ? $status : $mode);?>
<?php js::set('createTradeTip', $lang->refund->createTradeTip);?>
<div class='row-table'>
  <div class='col-main'>
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $refund->name?></strong></div>
      <div class='panel-body'>
        <p><?php echo sprintf($lang->refund->descTip, zget($users, $refund->createdBy), zget($currencySign, $refund->currency) . $refund->money)?></p>
        <?php if(!empty($refund->reason)):?>
        <p><?php echo "{$lang->refund->statusList[$refund->status]}{$lang->refund->reason}:{$refund->reason}"?></p>
        <?php endif;?>
        <p><?php echo $refund->desc?></p>
        <div><?php echo $this->fetch('file', 'printFiles', array('files' => $refund->files, 'fieldset' => 'false'))?></div>
      </div>
    </div> 
    <?php if(!empty($refund->detail)):?>
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $lang->refund->detail?></strong></div>
      <div class='panel-body no-padding'>
        <table class='table table-fixed table-hover text-center'>
          <tr class="text-center">
            <th class='w-50px'><?php echo $lang->refund->id;?></th>
            <th class='w-100px'><?php echo $lang->refund->money;?></th>
            <th class='w-100px'><?php echo $lang->refund->invoice;?></th>
            <th class='w-80px'><?php echo $lang->refund->date;?></th>
            <th class='w-200px text-left'><?php echo $lang->refund->category;?></th>
            <th class='w-100px'><?php echo $lang->refund->related;?></th>
            <th class='w-80px'><?php echo $lang->refund->status;?></th>
            <th class='text-left'><?php echo $lang->refund->desc;?></th>
          </tr>
          <?php foreach($refund->detail as $d):?>
          <tr>
            <td><?php echo $d->id;?></td>
            <td><?php echo zget($currencySign, $d->currency) . $d->money;?></td>
            <td><?php echo zget($currencySign, $d->currency) . $d->invoice;?></td>
            <td><?php echo formatTime($d->date, DT_DATE1);?></td>
            <td class='text-left' title='<?php echo zget($categories, $d->category, '');?>'><?php echo zget($categories, $d->category, '');?></td>
            <?php $related = ''; foreach(explode(',', trim($d->related, ',')) as $account) $related .= ' ' . zget($users, $account);?>
            <td title='<?php echo $related;?>'><?php echo $related;?></td>
            <td><span data-toggle='tooltip' data-original-title="<?php echo $d->reason;?>"><?php echo zget($lang->refund->statusList, $d->status);?></span></td>
            <td class='text-left' title='<?php echo $d->desc;?>'><?php echo $d->desc;?></td>
          </tr>
          <?php endforeach;?>
        </table>
      </div>
    </div> 
    <?php endif;?>
    <?php echo $this->fetch('action', 'history', "objectType=refund&objectID={$refund->id}");?>
    <div class='page-actions'>
      <?php
      $switchLabel = $refund->status == 'wait' ? $lang->refund->cancel : $lang->refund->commit;
      if($refund->createdBy == $this->app->user->account)
      {
          if(strpos(',wait,draft,', ",{$refund->status},") !== false)
          {
              commonModel::printLink('refund', 'switchStatus', "id=$refund->id", $switchLabel, "class='btn switchStatus'");
          }
          if(strpos(',wait,draft,reject,', ",{$refund->status},") !== false)
          {
              echo "<div class='btn-group'>";
              commonModel::printLink('refund', 'edit', "refundID={$refund->id}", $lang->edit, "class='btn btn-default'");
              commonModel::printLink('refund', 'delete', "refundID={$refund->id}&referer={$referer}", $lang->delete, "class='btn btn-default deleter'");
              echo '</div>';
          }
      }
      if(strpos(',wait,doing,', $refund->status) !== false) 
      {
          commonModel::printLink('refund', 'review', "refundID={$refund->id}", $lang->refund->review, "class='btn btn-default' data-toggle='modal' data-width='800'");
      }
      if($refund->status == 'pass')
      {
          commonModel::printLink('refund', 'reimburse', "refundID={$refund->id}", $lang->refund->common, "class='btn btn-default' data-toggle='modal' data-width='540'");
      }
      $browseLink = $this->session->refundList ? $this->session->refundList : inlink('personal');
      commonModel::printRPN($browseLink, $preAndNext);
      ?>
    </div>
  </div>
  <div class='col-side'>
    <div class='panel'>
      <div class='panel-heading'><strong><i class='icon-file-text-alt'></i> <?php echo $lang->refund->baseInfo;?></strong></div>
      <div class='panel-body'>
        <table class='table table-info'>
          <tr>
            <th class='w-100px'><?php echo $lang->refund->dept;?></th>
            <td><?php echo zget($deptList, $refund->dept, '')?></td>
          </tr>
          <tr>
            <th><?php echo $lang->refund->category;?></th>
            <td><?php echo zget($categories, $refund->category, '')?></td>
          </tr>
          <?php if($customer):?>
          <tr>
            <th><?php echo $lang->refund->customer;?></th>
            <td><?php if(!commonModel::printLink('crm.customer', 'view', "customer=$refund->customer", $customer->name)) echo $customer->name;?></td>
          </tr>
          <?php endif;?>
          <?php if($order):?>
          <tr>
            <th><?php echo $lang->refund->order;?></th>
            <td><?php if(!commonModel::printLink('crm.order', 'view', "order=$refund->order", $order->title)) echo $order->title;?></td>
          </tr>
          <?php endif;?>
          <?php if($contract):?>
          <tr>
            <th><?php echo $lang->refund->contract;?></th>
            <td><?php if(!commonModel::printLink('crm.contract', 'view', "contract=$refund->contract", $contract->name)) echo $contract->name;?></td>
          </tr>
          <?php endif;?>
          <?php if($project):?>
          <tr>
            <th><?php echo $lang->refund->project;?></th>
            <td><?php if(!commonModel::printLink('proj.project', 'view', "project=$refund->project", $project->name, "data-toggle='modal'")) echo $project->name;?></td>
          </tr>
          <?php endif;?>
          <tr>
            <th><?php echo $lang->refund->money;?></th>
            <td><?php echo zget($currencySign, $refund->currency) . $refund->money?></td>
          </tr>
          <tr>
            <th><?php echo $lang->refund->invoice;?></th>
            <td><?php echo zget($currencySign, $refund->currency) . $refund->invoice?></td>
          </tr>
          <tr>
            <th><?php echo $lang->refund->status;?></th>
            <td><?php echo zget($lang->refund->statusList, $refund->status)?></td>
          </tr>
          <tr>
            <th><?php echo $lang->refund->date;?></th>
            <td><?php echo formatTime($refund->date, DT_DATE1);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->refund->related;?></th>
            <?php
            $related = '';
            if(!empty($refund->detail))
            {
                $relatedList = array();
                foreach($refund->detail  as $detail)
                {
                    foreach(explode(',', trim($detail->related, ',')) as $account)
                    {
                        if(!$account) continue;
                        $relatedList[$account] = zget($users, $account);
                    }
                }
                $related = implode(' ', $relatedList);
            }
            else
            {
                foreach(explode(',', trim($refund->related, ',')) as $account)
                {
                    $related .= ' ' . zget($users, $account);
                }
            }
            ?>
            <td><?php echo $related;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->refund->payee;?></th>
            <td><?php echo zget($users, $refund->payee);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->refund->createdBy;?></th>
            <td><?php echo zget($users, $refund->createdBy) . $lang->at . formatTime($refund->createdDate, DT_DATETIME1);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->refund->editedBy;?></th>
            <td><?php if($refund->editedBy) echo zget($users, $refund->editedBy) . $lang->at . formatTime($refund->editedDate, DT_DATETIME1);?></td>
          </tr>
          <tr id='firstReviewer'>
            <th><?php echo $lang->refund->firstReviewer;?></th>
            <td><?php echo $refund->firstReviewerLabel;?></td>
          </tr>
          <tr id='secondReviewer'>
            <th><?php echo $lang->refund->secondReviewer;?></th>
            <td><?php echo $refund->secondReviewerLabel;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->refund->refundBy;?></th>
            <td><?php if($refund->refundBy) echo zget($users, $refund->refundBy) . $lang->at . formatTime($refund->refundDate, DT_DATETIME1);?></td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
