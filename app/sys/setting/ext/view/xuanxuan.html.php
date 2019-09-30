<?php
/**
 * The configure xuanxuan view file of setting module of XXB.
 *
 * @copyright   Copyright 2009-2017 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Gang Liu <liugang@cnezsoft.com>
 * @package     setting
 * @version     $Id$
 * @link        http://xuan.im
 */
?>
<?php include '../../../common/view/header.html.php';?>
<div class='panel'>
  <div class='panel-heading'>
    <strong><?php echo $lang->setting->param;?></strong>
  </div>
  <form method='post' id='ajaxForm' class='form-ajax'>
    <table class='table table-form'>
      <tr>
        <th class='w-100px'><?php echo $lang->chat->version;?></th>
        <td class='w-300px'><?php echo $config->xuanxuan->global->version;?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->chat->key;?></th>
        <td>
          <?php $key   = zget($config->xuanxuan, 'key', '');?>
          <?php $style = (!$key or $key == str_repeat(8, 32)) ? "style='margin-bottom: 0px; border-color: rgb(149, 59, 57);'" : '';?>
          <?php echo $type == 'edit' ? html::input('key', zget($config->xuanxuan, 'key', ''), "class='form-control' readonly='readonly' $style") : zget($config->xuanxuan, 'key', '');?>
          <?php if($type == 'edit' && (!$key or $key == str_repeat(8, 32))):?>
          <span id="keyLabel" for="key" class="text-error red"><?php echo !$key ? $lang->chat->errorKey : $lang->chat->defaultKey;?></span>
          <?php endif;?>
        </td>
        <td><?php echo $type == 'edit' ? html::a('javascript:void(0)', $lang->chat->createKey, 'onclick="createKey()"') : '';?></td>
      </tr>
      <tr>
        <th><?php echo $lang->chat->backendLang;?></th>
        <td><?php echo $type == 'edit' ? html::select('backendLang', $config->langs, $config->xuanxuan->backendLang, "class='form-control'") : zget($config->langs, $config->xuanxuan->backendLang, '');?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->chat->xxdServer;?></th>
        <td><?php echo $type == 'edit' ? html::input('server', $server, "class='form-control'") : $server;?></td>
        <td><?php if($type == 'edit') echo $lang->chat->xxdServerTip;?></td>
      </tr>
      <tr>
        <th><?php echo $lang->chat->xxd->ip;?></th>
        <td><?php echo $type == 'edit' ? html::input('ip', zget($config->xuanxuan, 'ip', '0.0.0.0'), "class='form-control' placeholder='{$lang->chat->placeholder->xxd->ip}'") : zget($config->xuanxuan, 'ip', '0.0.0.0');?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->chat->xxd->chatPort;?></th>
        <td><?php echo $type == 'edit' ? html::input('chatPort', zget($config->xuanxuan, 'chatPort', 11444), "placeholder='{$lang->chat->placeholder->xxd->chatPort}' class='form-control'") : zget($config->xuanxuan, 'chatPort', 11444);?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->chat->xxd->commonPort;?></th>
        <td><?php echo $type == 'edit' ? html::input('commonPort', zget($config->xuanxuan, 'commonPort', 11443), "placeholder='{$lang->chat->placeholder->xxd->commonPort}' class='form-control'") : zget($config->xuanxuan, 'commonPort', 11443);?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->chat->xxd->uploadFileSize;?></th>
        <td>
          <?php if($type == 'edit'):?>
          <div class='input-group'>
            <span class='input-group-addon'><?php echo $lang->chat->xxd->max;?></span>
            <?php echo html::input('uploadFileSize', zget($config->xuanxuan, 'uploadFileSize', 20), "class='form-control' placeholder='{$lang->chat->placeholder->xxd->uploadFileSize}' ");?>
            <span class='input-group-addon'>M</span>
          </div>
          <?php else:?>
          <?php echo $lang->chat->xxd->max . zget($config->xuanxuan, 'uploadFileSize', 20) . 'M';?>
          <?php endif;?>
        </td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->chat->xxd->https;?></th>
        <td>
          <?php $https = zget($config->xuanxuan, 'https', 'off');?>
          <?php echo $type == 'edit' ? html::radio('https', $lang->chat->httpsOptions, $https, "class='checkbox'") : zget($lang->chat->httpsOptions, $https);?>
        </td>
        <td></td>
      </tr>
      <tr class='sslTR <?php if($https == 'off' || empty($type)) echo 'hide';?>'>
        <th><?php echo $lang->chat->xxd->sslcrt;?></th>
        <td><?php echo html::textarea('sslcrt',  zget($config->xuanxuan, 'sslcrt', ''), "placeholder='{$lang->chat->placeholder->xxd->sslcrt}' class='form-control'");?></td>
        <td></td>
      </tr>
      <tr class='sslTR <?php if($https == 'off' || empty($type)) echo 'hide';?>'>
        <th><?php echo $lang->chat->xxd->sslkey;?></th>
        <td><?php echo html::textarea('sslkey',  zget($config->xuanxuan, 'sslkey', ''), "placeholder='{$lang->chat->placeholder->xxd->sslkey}' class='form-control'");?></td>
        <td></td>
      </tr>
      <tr>
        <th><?php echo $lang->chat->debug;?></th>
        <td>
          <?php $debug = zget($config->xuanxuan, 'debug', 0);?>
          <?php echo $type == 'edit' ? html::radio('debug', $lang->chat->debugStatus, $debug) : zget($lang->chat->debugStatus, $debug);?>
        </td>
        <td></td>
      </tr>
      <?php if($type != 'edit'):?>
      <tr>
        <th><?php echo $lang->chat->xxd->os;?></th>
        <td><?php echo html::select('os', $lang->chat->osList, zget($config->xuanxuan, $os), "class='form-control chosen'");?></td>
        <td></td>
      </tr>
      <?php endif;?>
      <tr>
        <th></th>
        <td colspan='2'>
          <?php if($type == 'edit'):?>
            <?php echo html::submitButton();?>
            <?php echo html::a(helper::createLink('setting', 'xuanxuan'), $lang->goback, 'class="btn"');?>
          <?php else:?>
            <?php echo html::a(helper::createLink('setting', 'downloadXXD', 'type=package'), $lang->chat->downloadXXD, "class='btn btn-primary download download-package' target='hiddenwin'");?>
            <?php echo html::a(helper::createLink('setting', 'downloadXXD', 'type=config'), $lang->chat->downloadConfig, "class='btn btn-primary download' target='hiddenwin'");?>
            <?php if($debug) echo html::a(helper::createLink('chat', 'debug'), $lang->chat->viewDebug, "class='btn btn-primary viewDebug' data-toggle='modal'");?>
            <?php echo html::a(helper::createLink('setting', 'xuanxuan', 'type=edit'), $lang->chat->changeSetting, "class='btn'");?>
          <?php endif;?>
        </td>
      </tr>
    </table>
  </form>
</div>
<?php include '../../../common/view/footer.html.php';?>
