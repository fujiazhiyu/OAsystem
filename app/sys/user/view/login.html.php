<?php
/**
 * The login view file of user module of RanZhi.
 *
 * @copyright   Copyright 2009-2018 青岛易软天创网络科技有限公司(QingDao Nature Easy Soft Network Technology Co,LTD, www.cnezsoft.com)
 * @license     ZPL (http://zpl.pub/page/zplv12.html)
 * @author      Chunsheng Wang <chunsheng@cnezsoft.com>
 * @package     user
 * @version     $Id: login.html.php 4060 2016-09-29 00:51:35Z daitingting $
 * @link        http://www.ranzhi.org
 */
?>
<?php
include '../../common/view/header.lite.html.php';
js::import($jsRoot . 'md5.js');
js::set('scriptName', $_SERVER['SCRIPT_NAME']);
js::set('random', $this->session->random);
js::set('notEncryptedPwd', empty($config->notEncryptedPwd) ? false : $config->notEncryptedPwd);
css::internal('body{background-color:#f6f5f5}');
?>
<div class='container'>
  <div id='login'>
    <div class='panel-head'>
      <h4><?php printf($lang->welcome, isset($config->company->name) ? $config->company->name : '');?></h4>
      <div class='panel-actions'>
        <div class='dropdown' id='langs'>
          <button class='btn' data-toggle='dropdown' title='Change Language/更换语言/更換語言'><?php echo $config->langs[$this->app->getClientLang()]; ?> <span class="caret"></span></button>
          <ul class='dropdown-menu'>
            <?php foreach($config->langs as $key => $value):?>
            <li class="<?php echo $key==$this->app->getClientLang()?'active':''; ?>"><a href="###" data-value="<?php echo $key; ?>"><?php echo $value; ?></a></li>
            <?php endforeach;?>
          </ul>
        </div>
      </div>
    </div>
    <div class="panel-body" id="loginForm">
      <form method='post' target='hiddenwin' class='form-condensed'>
        <div id='responser' class='text-center'></div>
        <div class='row'>
          <div class='col-xs-4 text-center'>
          <?php echo html::image($this->config->webRoot . 'theme/default/images/main/logo.png'); ?>
          </div>
          <div class='col-xs-8'>
            <table class='table table-form'>
              <tr>
                <th><?php echo $lang->user->account;?></th>
                <td><?php echo html::input('account','',"class='form-control' placeholder='{$lang->user->inputAccount}'");?></td>
              </tr>
              <tr>
                <th><?php echo $lang->user->password;?></th>
                <td><?php echo html::password('password','',"class='form-control' placeholder='{$lang->user->inputPassword}'");?></td>
              </tr>
              <tr>
                <th></th>
                <td>
                  <?php echo html::submitButton($lang->login) . html::hidden('referer', $referer);?>
                  <?php echo html::checkbox('keepLogin', array('on' => $lang->user->keepLogin), $this->cookie->keepLogin ? $this->cookie->keepLogin : 'off');?>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </form>
    </div>
  </div>
  <!-- <div class='notice text-center'>
  </div> -->
</div>
<?php
if($config->debug) js::import($jsRoot . 'jquery/form/min.js');
if(isset($pageJS)) js::execute($pageJS);
if($config->checkVersion and isset($_SERVER['https']))  js::import("https://api.ranzhi.org/updater-latest-{$config->version}.html");
if($config->checkVersion and !isset($_SERVER['https'])) js::import("http://api.ranzhi.org/updater-latest-{$config->version}.html");
js::set('ignoreNotice', $ignoreNotice);
js::set('ignore', $lang->user->ignore);
?>
</body>
</html>
