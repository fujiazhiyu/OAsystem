<?php 
/**
 * The browse view file of customer module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     customer 
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php include $app->getAppRoot() . 'common/view/header.html.php';?>
<?php include '../../common/view/chosen.html.php';?>
<?php js::set('mode', $mode);?>
<li id='bysearchTab'><?php echo html::a('#', "<i class='icon-search icon'></i>" . $lang->search->common)?></li>
<div id='menuActions'>
  <?php if(commonModel::hasPriv('customer', 'export')):?>
  <div class='btn-group'>
    <button data-toggle='dropdown' class='btn btn-primary dropdown-toggle' type='button'><?php echo $lang->exportIcon . $lang->export;?> <span class='caret'></span></button>
    <ul id='exportActionMenu' class='dropdown-menu'>
      <li><?php commonModel::printLink('customer', 'export', "mode=$mode&type=all&orderBy={$orderBy}", $lang->exportAll, "class='iframe' data-width='700'");?></li>
      <li><?php commonModel::printLink('customer', 'export', "mode=$mode&type=thisPage&orderBy={$orderBy}", $lang->exportThisPage, "class='iframe' data-width='700'");?></li>
    </ul>
  </div>
  <?php endif;?>
  <?php commonModel::printLink('customer', 'create', '', '<i class="icon-plus"></i> ' . $lang->customer->create, 'class="btn btn-primary"');?>
</div>
<div class='panel'>
  <form id='browseForm' method='post' action='<?php echo inlink('batchAssign');?>'>
    <table class='table table-hover table-striped table-bordered tablesorter table-data table-fixed table-fixedHeader'>
      <thead>
        <tr class='text-center'>
          <?php $vars = "mode={$mode}&param=&orderBy=%s&recTotal={$pager->recTotal}&recPerPage={$pager->recPerPage}&pageID={$pager->pageID}";?>
          <th class='w-60px'> <?php commonModel::printOrderLink('id',     $orderBy, $vars, $lang->customer->id);?></th>
          <th>                <?php commonModel::printOrderLink('name',   $orderBy, $vars, $lang->customer->name);?></th>
          <th class='w-70px'> <?php commonModel::printOrderLink('assignedTo', $orderBy, $vars, $lang->customer->assignedTo);?></th>
          <th class='w-70px'> <?php commonModel::printOrderLink('level',  $orderBy, $vars, $lang->customer->level);?></th>
          <th class='w-70px'> <?php commonModel::printOrderLink('status', $orderBy, $vars, $lang->customer->status);?></th>
          <th class='w-80px visible-lg'><?php commonModel::printOrderLink('size', $orderBy, $vars, $lang->customer->size);?></th>
          <th class='w-80px'> <?php commonModel::printOrderLink('type', $orderBy, $vars, $lang->customer->type);?></th>
          <th class='w-100px visible-lg'><?php commonModel::printOrderLink('createdDate', $orderBy, $vars, $lang->customer->createdDate);?></th>
          <th class='w-100px visible-lg'><?php commonModel::printOrderLink('contactedDate', $orderBy, $vars, $lang->customer->contactedDate);?></th>
          <?php
          /* The next date is searched from the table crm_dating, so use date instead of nextDate to avoid occur errors when order by this field. */
          $date = strpos(',past,today,tomorrow,thisweek,thismonth,', ",{$mode},") != false ? 'date' : 'nextDate';
          ?>
          <th class='w-110px'><?php commonModel::printOrderLink($date, $orderBy, $vars, $lang->customer->nextDate);?></th>
          <th class='w-<?php echo $lang->customer->actionWidth;?>px'><?php echo $lang->actions;?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($customers as $customer):?>
        <tr class='text-center'>
          <?php if(commonModel::hasPriv('customer', 'batchAssign')):?>
          <td><?php echo html::checkbox('customerIDList', array($customer->id => $customer->id));?></td>
          <?php else:?>
          <td><?php echo $customer->id;?></td>
          <?php endif;?>
          <td class='text-left'><?php if(!commonModel::printLink('customer', 'view', "customerID={$customer->id}", $customer->name)) echo $customer->name;?></td>
          <td><?php if(isset($users[$customer->assignedTo])) echo $users[$customer->assignedTo];?></td>
          <td><?php echo isset($lang->customer->levelNameList[$customer->level]) ? $lang->customer->levelNameList[$customer->level] : '';?></td>
          <td class='<?php echo "customer-{$customer->status}";?>'><?php if($customer->status) echo $lang->customer->statusList[$customer->status];?></td>
          <td class='visible-lg'><?php echo $lang->customer->sizeNameList[$customer->size];?></td>
          <td><?php echo $lang->customer->typeList[$customer->type];?></td>
          <td class='visible-lg'><?php echo formatTime($customer->createdDate, DT_DATE1);?></td>
          <td class='visible-lg'><?php echo formatTime($customer->contactedDate, DT_DATETIME1);?></td>
          <td><?php echo formatTime($customer->nextDate, DT_DATE1);?></td>
          <td class='actions'>
            <?php
            commonModel::printLink('action',   'createRecord', "objectType=customer&objectID=$customer->id&customer=$customer->id", $lang->customer->record, "data-toggle='modal' data-width='800'");
            commonModel::printLink('customer', 'assign', "customerID=$customer->id", $lang->customer->assign, "data-toggle='modal'");
            commonModel::printLink('customer', 'contact', "customerID=$customer->id", $lang->customer->contact,  "data-toggle='modal'");
            commonModel::printLink('customer', 'edit', "customerID=$customer->id", $lang->edit);
            echo "<div class='dropdown'><a data-toggle='dropdown' href='javascript:;'>" . $this->lang->more . "<span class='caret'></span> </a><ul class='dropdown-menu pull-right'>";
            commonModel::printLink('customer', 'order', "customerID=$customer->id", $lang->customer->order, "data-toggle='modal'", '', '', 'li');
            commonModel::printLink('customer', 'contract', "customerID=$customer->id", $lang->customer->contract, "data-toggle='modal'", '', '', 'li');
            commonModel::printLink('crm.address', 'browse', "objectType=customer&objectID=$customer->id", $lang->customer->address, "data-toggle='modal'", '', '', 'li');
            commonModel::printLink('customer', 'merge', "customerID=$customer->id", $lang->customer->merge, "data-toggle='modal'", '', '', 'li');
            commonModel::printLink('customer', 'delete', "customerID=$customer->id", $lang->delete, "class='deleter'", '', '', 'li');
            echo "</ul></div>";
            ?>
          </td>
        </tr>
        <?php endforeach;?>
      </tbody>
    </table>
    <div class='table-footer'>
      <?php if($customers && commonModel::hasPriv('customer', 'batchAssign')):?>
      <div class='pull-left batch-actions'>
        <div class='pull-left'><?php echo html::selectButton();?></div>
          <div class='input-group assign-action'>
            <?php echo html::select('assignedTo', $validUsers, '', "class='form-control chosen'");?>
            <span class='input-group-btn'><?php echo html::a('#', $lang->customer->assign, "class='btn btn-primary batchAssign'");?></span>
        </div>
      </div>
      <?php endif;?>
      <?php $pager->show();?>
    </div>
  </form>
</div>
<?php include $app->getAppRoot() . 'common/view/footer.html.php';?>
