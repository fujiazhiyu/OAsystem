<?php 
/**
 * The view of view function of contact module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Yidong Wang <yidong@cnezsoft.com>
 * @package     contact 
 * @version     $Id$
 * @link        http://www.ranzhi.org
 */
?>
<?php include '../../common/view/header.html.php';?>
<?php include '../../../sys/common/view/kindeditor.html.php';?>
<ul id='menuTitle'>
  <li><?php commonModel::printLink('contact', 'browse', '', "<i class='icon-list-ul'></i> " . $lang->contact->list);?></li>
  <li class='divider angle'></li>
  <li class='title'><?php echo $contact->realname;?></li>
</ul>
<div class='row-table'>
  <div class='col-main'>
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $lang->contact->desc;?></strong></div>
      <div class='panel-body'><?php echo $contact->desc;?></div>
    </div>
    <?php echo $this->fetch('file', 'printFiles', array('files' => $fileList, 'fieldset' => 'true'))?>
    <?php echo $this->fetch('action', 'history', "objectType=contact&objectID={$contact->id}")?>
    <div class='page-actions'>
      <?php
      echo "<div class='btn-group'>";
      commonModel::printLink('action', 'createRecord', "objectType=contact&objectID={$contact->id}&customer={$contact->customer}&history=", $lang->contact->record, "data-toggle='modal' data-width='800' class='btn'");
      commonModel::printLink('address', 'browse', "objectType=contact&objectID=$contact->id", $lang->contact->address, "data-toggle='modal' class='btn'");
      commonModel::printLink('resume', 'browse', "contactID=$contact->id", $lang->contact->resume, "data-toggle='modal' class='btn'");
      echo "</div>";

      echo "<div class='btn-group'>";
      commonModel::printLink('contact', 'edit', "contactID=$contact->id", $lang->edit, "class='btn'");
      commonModel::printLink('contact', 'delete', "contactID=$contact->id", $lang->delete, "class='deleter btn'");
      echo html::a('#commentBox', $this->lang->comment, "class='btn btn-default' onclick=setComment()");
      echo "</div>";

      $browseLink = $this->session->contactList ? $this->session->contactList : inlink('browse');
      commonModel::printRPN($browseLink, $preAndNext);
      ?>
    </div>
    <fieldset id='commentBox' class='hide'>
      <legend><?php echo $lang->comment;?></legend>
      <form id='ajaxForm' method='post' action='<?php echo inlink('edit', "contactID={$contact->id}&comment=true")?>'>
        <div class='form-group'><?php echo html::textarea('comment', '',"rows='5' class='w-p100'");?></div>
        <?php echo html::submitButton();?>
      </form>
    </fieldset>      
  </div>
  <div class='col-side'>
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $lang->contact->basicInfo;?></strong></div>
      <div class='panel-body'>
        <table class='table table-info'>
          <tr>
            <th class='w-70px'><?php echo $lang->contact->customer;?></th>
            <td>
              <?php
              if(isset($customers[$contact->customer])) echo html::a($this->createLink('customer', 'view', "customerID={$contact->customer}"), $customers[$contact->customer]);
              if($contact->maker) echo " ({$lang->resume->maker})";
              ?>
            </td>
          </tr>
          <tr>
            <th><?php echo $lang->resume->dept;?></th>
            <td><?php echo  $contact->dept;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->resume->title;?></th>
            <td><?php echo  $contact->title;?></td>
          </tr>
          <tr>
            <th><?php echo $lang->resume->join;?></th>
            <td><?php echo  formatTime($contact->join, DT_DATE1);?></td>
          </tr>
          <tr>
            <th class='w-70px'><?php echo $lang->contact->birthday;?></th>
            <td><?php echo formatTime($contact->birthday, DT_DATE1);?></td>
          </tr>
          <tr>
            <th><?php echo $lang->contact->gender;?></th>
            <td><?php echo zget($lang->genderList, $contact->gender, '');?></td>
          </tr>
          <tr>
            <th><?php echo $lang->contact->createdDate;?></th>
            <td><?php echo formatTime($contact->createdDate, DT_DATETIME1);?></td>
          </tr>
        </table>
      </div>
    </div>
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $lang->contact->contactInfo;?></strong></div>
      <div class='panel-body'>
        <table class='table table-info contact-info'>
          <tr>
            <td>
              <div class='row'>
                <div class='col-sm-11'>
                  <dl class='contact-info'>
                  <?php foreach($config->contact->contactWayList as $item):?>
                  <?php if(!empty($contact->{$item})):?>
                    <dd>
                      <span><?php echo $lang->contact->{$item};?></span>
                      <?php $site = isset($config->company->name) ? $config->company->name : '';?>
                      <?php if($item == 'qq') echo html::a("http://wpa.qq.com/msgrd?v=3&uin={$contact->$item}&site={$site}&menu=yes", $contact->$item, "target='_blank'");?>
                      <?php if($item == 'email') echo html::mailto($contact->{$item}, $contact->{$item});?>
                      <?php if($item != 'qq' and $item != 'email') echo $contact->{$item};?>
                    </dd>
                  <?php endif;?>
                  <?php endforeach;?>
                  </dl>
                  <p class='vcard'><?php echo html::image(inlink('vcard', "contactID={$contact->id}"))?></p>
                </div>
                <div class='col-sm-1'><i class='btn-vcard icon icon-qrcode icon-large'> </i></div>
              </div>
            </td>
          </tr>
        </table>
      </div>
    </div>
    <div class='panel'>
      <div class='panel-heading'>
        <div class='row'>
        <div class='col-sm-3'><strong><?php echo $lang->resume->time;?></strong></div>
        <div class='col-sm-4 text-center'><strong><?php echo $lang->resume->customer;?></strong></div>
        <div class='col-sm-2'><strong><?php echo $lang->resume->dept;?></strong></div>
        <div class='col-sm-3 text-center'><strong><?php echo $lang->resume->title;?></strong></div>
        </div>
      </div>
      <table class='table table-data'>
        <?php foreach($resumes as $resume):?>
        <tr class='text-center'>
          <td class='w-p25'><?php echo formatTime($resume->join, DT_DATE1) . $lang->minus . formatTime($resume->left, DT_DATE1);?></td>
          <td class='w-p30'><?php if(isset($customers[$resume->customer])) commonModel::printLink('customer', 'view', "id={$resume->customer}", $customers[$resume->customer]);?></td>
          <td class='w-p20'><?php echo $resume->dept?></td>
          <td class='w-p25'><?php echo $resume->title?></td>
       </tr>
        <?php endforeach;?>
      </table>
    </div>
    <div class='panel'>
      <div class='panel-heading'><strong><?php echo $lang->contact->address;?></strong></div>
      <table class='table table-data'>
        <?php foreach($addresses as $address):?>
        <tr>
          <td><?php echo $address->title . $lang->colon . $address->fullLocation;?></td>
        </tr>
        <?php endforeach;?>
      </table>
    </div>
  </div>
</div>
<?php include '../../common/view/footer.html.php';?>
