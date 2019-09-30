<?php
/**
 * Get signed time.
 *
 * @param  string $account
 * @access public
 * @return string | int
 */
public function getSignedTime($account = '')
{
    $this->app->loadModuleConfig('attend');
    if(!isset($this->config->attend->signInClient) || strpos(',all,xuanxuan,', ",{$this->config->attend->signInClient},") === false) return '';

    $attend = $this->dao->select('*')->from(TABLE_ATTEND)->where('account')->eq($account)->andWhere('`date`')->eq(date('Y-m-d'))->fetch();
    if($attend) return strtotime("$attend->date $attend->signIn");

    return time();
}

/**
 * Get extension list.
 * @param $userID
 * @return array
 */
public function getExtensionList($userID)
{
    $entries    = array();
    $allEntries = array();
    $time       = time();

    $_SERVER['SCRIPT_NAME'] = str_replace('x.php', 'sys/index.php', $_SERVER['SCRIPT_NAME']);
    $this->config->webRoot  = getRanzhiWebRoot();

    $baseURL   = commonModel::getSysURL();
    $entryList = $this->dao->select('*')->from(TABLE_ENTRY)->orderBy('`order`, id')->fetchAll();
    $files     = $this->dao->select('id, pathname, objectID')->from(TABLE_FILE)->where('objectType')->eq('entry')->fetchAll('objectID');

    foreach($entryList as $entry)
    {
        if($entry->code == 'sso') $entry->login = $baseURL;

        $data = new stdclass();
        $data->id     = $entry->id;
        $data->url    = strpos($entry->login, 'http') !== 0 ? str_replace('../', $baseURL . $this->config->webRoot, $entry->login) : $entry->login;
        $allEntries[] = $data;
    }

    foreach($entryList as $entry)
    {
        if($entry->status != 'online') continue;
        if(strpos(',' . $entry->platform . ',', ',xuanxuan,') === false) continue;

        $token = '';
        if(isset($files[$entry->id]->pathname))
        {
            $token = '&time=' . $time . '&token=' . md5($files[$entry->id]->pathname . $time);
        }
        $data = new stdClass();
        $data->entryID     = (int)$entry->id;
        $data->name        = $entry->code;
        $data->displayName = $entry->name;
        $data->abbrName    = $entry->abbr;
        $data->webViewUrl  = strpos($entry->login, 'http') !== 0 ? str_replace('../', $baseURL . $this->config->webRoot, $entry->login) : $entry->login;
        $data->download    = empty($entry->package) ? '' : $baseURL . helper::createLink('file', 'download', "fileID={$entry->package}&mouse=" . $token);
        $data->md5         = empty($entry->package) ? '' : md5($entry->package);
        $data->logo        = empty($entry->logo)    ? '' : $baseURL . $this->config->webRoot . ltrim($entry->logo, '/');

        if($entry->sso) $data->data = $allEntries;

        $entries[] = $data;
    }

    return $entries;
}
