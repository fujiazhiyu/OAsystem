<?php
/**
 * The announce block view file of block module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<table class='table table-data table-hover table-fixed' id='oaBlockAnnounce'>
  <?php foreach($announces as $id => $announce):?>
  <tr>
    <td><?php echo html::a($this->createLink('oa.announce', 'view', "announceID=$id"), $announce->title, "data-toggle='modal'")?></td>
    <td class='w-50px'><?php echo formatTime($announce->createdDate, DT_DATE6)?></td>
  </tr>
  <?php endforeach;?>
</table>
