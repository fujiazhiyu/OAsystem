<?php
/**
 * The customer block view file of block module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<table class='table table-data table-hover block-contract table-fixed'>
  <?php foreach($customers as $id => $customer):?>
  <?php $appid = ($this->get->app == 'sys' and isset($_GET['entry'])) ? "class='app-btn' data-id='{$this->get->entry}'" : ''?>
  <tr data-url='<?php echo $this->createLink('crm.customer', 'view', "id=$id");?>' <?php echo $appid;?>>
    <td class='nobr'><?php echo $customer->name;?></td>
    <td class='w-80px text-center'><?php echo formatTime($customer->nextDate, DT_DATE1);?></td>
    <td class='w-80px text-center'><?php echo zget($lang->customer->statusList, $customer->status);?></td>
  </tr>
  <?php endforeach;?>
</table>
<script>if(!$.ipsStart) $('.block-contract').dataTable();</script>
