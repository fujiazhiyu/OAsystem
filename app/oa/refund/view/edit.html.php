<?php
/**
 * The create view file of refund module of Ranzhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      chujilu <chujilu@cnezsoft.com>
 * @package     refund
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../../sys/common/view/datepicker.html.php';?>
<?php include '../../../sys/common/view/chosen.html.php';?>
<form id='ajaxForm' method='post' action="<?php echo $this->createLink('oa.refund', 'edit', "refundID={$refund->id}")?>">
  <div class='panel'>
    <div class='panel-heading'>
      <strong><?php echo $lang->refund->edit;?></strong>
    </div>
    <div class='panel-body'>
      <table class='table table-form w-1000px'>
        <tr>
          <th class='w-100px'><?php echo $lang->refund->name?></th>
          <td class='w-400px'><?php echo html::input('name', $refund->name, "class='form-control'")?></td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->refund->dept?></th>
          <td><?php echo html::select('dept', $deptList, $refund->dept, "class='form-control chosen'")?></td>
        </tr>
        <tr>
          <th><?php echo $lang->refund->category?></th>
          <td>
            <div class='input-group'>
              <?php echo html::select('category', $categories, $refund->category, "class='form-control chosen'");?>
              <span class='input-group-addon'>
                <?php echo html::checkbox('objectType', $lang->refund->objectTypeList, $refund->objectType);?> 
              </span>
            </div>
          </td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->refund->customer;?></th>
          <td>
            <div class='required required-wrapper'></div>
            <?php echo html::select('customer', $customers, $refund->customer, "class='form-control chosen' data-no_results_text='" . $lang->searchMore . "'");?>
          </td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->refund->order;?></th>
          <td>
            <div class='required required-wrapper'></div>
            <?php echo html::select('order', $orders, $refund->order, "class='form-control chosen'");?>
          </td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->refund->contract;?></th>
          <td>
            <div class='required required-wrapper'></div>
            <?php echo html::select('contract', $contracts, $refund->contract, "class='form-control chosen'");?>
          </td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->refund->project;?></th>
          <td>
            <div class='required required-wrapper'></div>
            <?php echo html::select('project', $projects, $refund->project, "class='form-control chosen'");?>
          </td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->refund->money?></th>
          <td>
            <div class='input-group'>
              <div class='input-group-btn w-90px'><?php echo html::select('currency', $currencyList, $refund->currency, "class='form-control'")?></div>
              <?php echo html::input('money', $refund->money, "class='form-control'")?>
              <div class='input-group-addon fix-border'><?php echo $lang->refund->invoice?></div>
              <?php echo html::input('invoice', $refund->invoice, "class='form-control'")?>
              <div class='input-group-btn'><?php echo html::a("javascript:void(0)", "<i class='icon-double-angle-down'></i> " . $lang->refund->detail, "class='btn detail'")?></div>
            </div>
          </td>
          <td></td>
        </tr>
        <tr id='refund-date'>
          <th><?php echo $lang->refund->date?></th>
          <td><?php echo html::input('date', $refund->date, "class='form-control form-date'")?></td>
          <td></td>
        </tr>
        <tr id='refund-related'>
          <th><?php echo $lang->refund->related?></th>
          <td><?php echo html::select('related[]', $users, $refund->related, "class='form-control chosen' multiple")?></td>
          <td></td>
        </tr>
        <tr>
          <th><?php echo $lang->refund->payee?></th>
          <td><?php echo html::select('payee', $users, $refund->payee, "class='form-control chosen'");?></td>
        </tr>
        <tr id='refund-detail' class='hidden'>
          <th><?php echo $lang->refund->detail?></th>
          <td colspan='2' id='detailBox'>
            <table class='table table-detail'>
              <?php $key = 0;?>
              <?php if($refund->detail):?>
              <?php foreach($refund->detail as $d):?>
              <tr>
                <td class='w-100px'><?php echo html::input("dateList[$key]", $d->date, "class='form-control form-date' placeholder='{$lang->refund->date}'")?></td>
                <?php if($categories):?>
                <td class='w-100px'><?php echo html::select("categoryList[$key]", $categories, $d->category, "class='form-control chosen' placeholder='{$lang->refund->category}'")?></td>
                <?php endif;?>
                <td class='w-90px'><?php echo html::input("moneyList[$key]", $d->money, "class='form-control' placeholder='{$lang->refund->money}'")?></td>
                <td class='w-90px'><?php echo html::input("invoiceList[$key]", $d->invoice, "class='form-control' placeholder='{$lang->refund->invoice}'")?></td>
                <td class='w-200px'><?php echo html::select("relatedList[$key][]", $users, $d->related, "class='form-control chosen' multiple data-placeholder='{$lang->refund->related}'")?></td>
                <td><?php echo html::textarea("descList[$key]", $d->desc, "class='form-control' style='height:32px;' placeholder='{$lang->refund->desc}'")?></td>
                <td class='w-70px text-right'><i class='btn btn-mini icon-plus plus'></i>&nbsp;&nbsp;<i class='btn btn-mini icon-remove minus'></i></td>
              </tr>
              <?php $key++;?>
              <?php endforeach;?>
              <?php else:?>
              <tr>
                <td class='w-100px'><?php echo html::input("dateList[$key]", '', "class='form-control form-date' placeholder='{$lang->refund->date}'")?></td>
                <?php if($categories):?>
                <td class='w-100px'><?php echo html::select("categoryList[$key]", $categories, '', "class='form-control chosen' placeholder='{$lang->refund->category}'")?></td>
                <?php endif;?>
                <td class='w-90px'><?php echo html::input("moneyList[$key]", '', "class='form-control' placeholder='{$lang->refund->money}'")?></td>
                <td class='w-90px'><?php echo html::input("invoiceList[$key]", '', "class='form-control' placeholder='{$lang->refund->invoice}'")?></td>
                <td class='w-200px'><?php echo html::select("relatedList[$key][]", $users, '', "class='form-control chosen' multiple data-placeholder='{$lang->refund->related}'")?></td>
                <td><?php echo html::textarea("descList[$key]", '', "class='form-control' style='height:32px;' placeholder='{$lang->refund->desc}'")?></td>
                <td class='w-70px text-right'><i class='btn btn-mini icon-plus plus'></i>&nbsp;&nbsp;<i class='btn btn-mini icon-remove minus'></i></td>
              </tr>
              <?php $key++;?>
              <?php endif;?>
            </table>
          </td>
        </tr>
        <tr>
          <th><?php echo $lang->refund->desc?></th>
          <td colspan='2'><?php echo html::textarea('desc', $refund->desc, "class='form-control'")?></td>
        </tr>
        <?php if(commonModel::hasPriv('file', 'uplaod')):?>
        <tr>
          <th><?php echo $lang->refund->files;?></th>
          <td colspan='2'><?php echo $this->fetch('file', 'buildForm')?></td>
        </tr>
        <?php endif;?>
        <tr><th></th><td colspan='2'><?php echo html::submitButton() . '&nbsp;&nbsp;' . html::backButton();?></td></tr>
      </table>
    </div>
  </div>
</form>
<script type='text/template' id='detailTpl'>
<tr>
  <td class='w-100px'><?php echo html::input('dateList[key]', '', "class='form-control form-date' placeholder='{$lang->refund->date}'")?></td>
  <?php if($categories):?>
  <td class='w-100px'><?php echo html::select('categoryList[key]', $categories, '', "class='form-control chosen' placeholder='{$lang->refund->category}'")?></td>
  <?php endif;?>
  <td class='w-90px'><?php echo html::input('moneyList[key]', '', "class='form-control' placeholder='{$lang->refund->money}'")?></td>
  <td class='w-90px'><?php echo html::input('invoiceList[key]', '', "class='form-control' placeholder='{$lang->refund->invoice}'")?></td>
  <td class='w-200px'><?php echo html::select('relatedList[key][]', $users, '', "class='form-control chosen' multiple data-placeholder='{$lang->refund->related}'")?></td>
  <td><?php echo html::textarea('descList[key]', '', "class='form-control' style='height:32px;' placeholder='{$lang->refund->desc}'")?></td>
  <td class='w-70px text-right'><i class='btn btn-mini icon-plus plus'></i>&nbsp;&nbsp;<i class='btn btn-mini icon-remove minus'></i></td>
</tr>
</script>
<?php js::set('key', $key)?>
<script>
<?php helper::import('../js/searchcustomer.js');?>
</script>
<?php include '../../common/view/footer.html.php';?>
