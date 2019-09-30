<?php
/**
 * Get version of xuanxuan.
 *
 * @access public
 * @return string
 */
public function getXuanxuanVersion()
{
    return !empty($this->config->xuanxuan->global->version) ? $this->config->xuanxuan->global->version : '1.0';
}

/**
 * Upgrade xuanxuan.
 *
 * @param  string $fromVersion
 * @access public
 * @return void
 */
public function upgradeXuanxuan($fromVersion)
{
    switch($fromVersion)
    {
        case '1.0'   : $this->execSQL($this->getUpgradeFile('xuanxuan1.0'));
        case '1.1.0' :
        case '1.1.1' : $this->execSQL($this->getUpgradeFile('xuanxuan1.1.1'));
        case '1.3.0' : $this->execSQL($this->getUpgradeFile('xuanxuan1.3.0'));
        case '1.4.0' : $this->execSQL($this->getUpgradeFile('xuanxuan1.4.0'));
            $this->processMessageStatus();
        case '1.5.0' :
        case '1.6.0' : $this->execSQL($this->getUpgradeFile('xuanxuan1.6.0'));
        case '2.0.0' : $this->execSQL($this->getUpgradeFile('xuanxuan2.0.0'));
			$this->installSSOEntry();
        case '2.1.0' : $this->execSQL($this->getUpgradeFile('xuanxuan2.1.0'));
        case '2.2.0' :
            $this->processUserStatus();
            $this->processXuanxuanKey();
        case '2.3.0' : $this->execSQL($this->getUpgradeFile('xuanxuan2.3.0'));
        case '2.4.0' : $this->execSQL($this->getUpgradeFile('xuanxuan2.4.0'));
            $this->changeMessageStatusTable();
        case '2.5.0' : $this->execSQL($this->getUpgradeFile('xuanxuan2.5.0'));
        case '2.5.1' :
        case '2.5.2' :
        case '2.5.3' :
        case '2.5.4' :
        default : $this->loadModel('setting')->setItem('system.sys.xuanxuan.global.version', $this->config->xuanxuan->version);
    }
}

/**
 * Change status of table user to clientStatus.
 *
 * @access public
 * @return bool
 */
public function processUserStatus()
{
    $hasStatus       = false;
    $hasClientStatus = false;

    $fields = $this->dbh->query('DESC ' . TABLE_USER)->fetchAll();
    foreach($fields as $field)
    {
        if($field->Field == 'status')       $hasStatus = true;
        if($field->Field == 'clientStatus') $hasClientStatus = true;
    }

    if($hasStatus && !$hasClientStatus)
    {
        try
        {
            $this->dbh->exec('ALTER TABLE ' . TABLE_USER . "CHANGE `status` `clientStatus` enum('online', 'away', 'busy', 'offline') NOT NULL DEFAULT 'offline'");
        }
        catch (PDOException $e)
        {
            return false;
        }
    }

    return true;
}

/**
 * Process message status.
 *
 * @access public
 * @return bool
 */
public function processMessageStatus()
{
    $table = $this->dbh->query("SHOW TABLES LIKE '{$this->config->db->prefix}im_usermessage'")->fetch();
    if(empty($table)) return false;

    $userMessages = array();
    $messagesList = $this->dao->select('*')->from($this->config->db->prefix . 'im_usermessage')->fetchAll();
    foreach($messagesList as $messages)
    {
        $user     = $messages->user;
        $messages = json_decode($messages->message);
        foreach($messages as $message)
        {
            if(isset($userMessages[$user][$message->gid])) continue;

            $data = new stdClass();
            $data->user   = $user;
            $data->gid    = $message->gid;
            $data->status = 'waiting';
            $this->dao->insert(TABLE_IM_MESSAGESTATUS)->data($data)->exec();

            $userMessages[$user][$message->gid] = $message->gid;
        }
    }

    return !dao::isError();
}

/**
 * Process key of xuanxuan.
 *
 * @access public
 * @return bool
 */
public function processXuanxuanKey()
{
    $this->loadModel('setting')->setItem('system.sys.common.xuanxuan.key', $this->config->xuanxuan->key);
    $this->setting->deleteItems('owner=system&module=xuanxuan&key=key');
    return !dao::isError();
}

/**
 * Fix the history and Change messagestatus table.
 * @return bool
 */
public function changeMessageStatusTable()
{
    $prefix     = $this->config->db->prefix;
    $errorTable = $this->dbh->query("show tables like '{$prefix}im_messsagestatus'")->fetch();
    if(!empty($errorTable)) $this->dbh->query("RENAME TABLE `{$prefix}im_messsagestatus` TO `{$prefix}im_messagestatus`");
    $needUpdate = false;
    $fields = $this->dbh->query('DESC ' . TABLE_IM_MESSAGESTATUS)->fetchAll();
    foreach($fields as $field)
    {
        if($field->Field == 'gid')
        {
            $needUpdate = true;
            break;
        }
    }
    if(!$needUpdate) return false;

    $gids = $this->dao->select('gid')->from(TABLE_IM_MESSAGESTATUS)->where('status')->ne('sent')->fetchPairs('gid');
    if(!empty($gids))
    {
        $messages = $this->dao->select('gid, id')->from(TABLE_IM_MESSAGE)->where('gid')->in($gids)->fetchPairs();
        foreach($messages as $gid => $message)
        {
            $this->dao->update(TABLE_IM_MESSAGESTATUS)->set('message')->eq($message)->where('gid')->eq($gid)->exec();
        }
    }
    $this->dao->delete()->from(TABLE_IM_MESSAGESTATUS)->where('status')->eq('sent')->exec();

    $this->dbh->exec('ALTER TABLE ' . TABLE_IM_MESSAGESTATUS . ' DROP INDEX `user`;');
    $this->dbh->exec('ALTER TABLE ' . TABLE_IM_MESSAGESTATUS . ' DROP `gid`;');
    $this->dbh->exec('ALTER TABLE ' . TABLE_IM_MESSAGESTATUS . ' ADD UNIQUE INDEX `user` (`user`, `message`);');
    return !dao::isError();
}
