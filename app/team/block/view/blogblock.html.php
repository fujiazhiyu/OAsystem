<?php
/**
 * The blog block view file of block module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Tingting Dai <daitingting@xirangit.com>
 * @package     block
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<table class='table table-data table-hover table-fixed block-blog' id='oaBlockAnnounce'>
  <?php foreach($blogs as $id => $blog):?>
  <?php $appID = ($this->get->app == 'sys' and isset($_GET['entry'])) ? "class='app-btn' data-id={$this->get->entry}" : ''?>
  <tr data-url='<?php echo $this->createLink('team.blog', 'view', "blogID=$id"); ?>' <?php echo $appID?>>
    <td><?php echo $blog->title;?></td>
    <td class='w-80px'><?php echo zget($users, $blog->author);?></td>
    <td class='w-50px'><?php echo formatTime($blog->createdDate, DT_DATE6)?></td>
  </tr>
  <?php endforeach;?>
</table>
<script>$('.block-blog').dataTable();</script>
