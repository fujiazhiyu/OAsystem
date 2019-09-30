<?php
/**
 * Install sso entry.
 *
 * @access public
 * @return bool
 */
public function installSSOEntry()
{
    $file = new stdclass();
    $file->pathname    = '201810/f_8db2fa542a1e087d63d45d8bc1185361.zip';
    $file->title       = 'sso';
    $file->extension   = 'zip';
    $file->size        = 89674;
    $file->objectType  = 'entry';
    $file->objectID    = 0;
    $file->createdBy   = $this->app->user->account;
    $file->createdDate = helper::now();
    $file->public      = 1;
    $this->dao->insert(TABLE_FILE)->data($file)->exec();

    if(dao::isError()) return false;

    $fileID = $this->dao->lastInsertId();

    $entry = new stdclass();
    $entry->name        = 'sso';
    $entry->abbr        = 'sso';
    $entry->code        = 'sso';
    $entry->buildin     = 1;
    $entry->version     = '1.0.0';
    $entry->platform    = 'xuanxuan';
    $entry->package     = $fileID;
    $entry->integration = 1;
    $entry->open        = 'iframe';
    $entry->key         = '7a171c33d02d172fc0f1cf4cb93edfd6';
    $entry->ip          = '*';
    $entry->logo        = '';
    $entry->login       = 'http://xuan.im';
    $entry->control     = 'none';
    $entry->size        = 'max';
    $entry->position    = 'default';
    $entry->sso         = 1;

    $this->dao->insert(TABLE_ENTRY)->data($entry)->exec();

    $entryID = $this->dao->lastInsertId();

    $this->dao->update(TABLE_FILE)->set('objectID')->eq($entryID)->where('id')->eq($fileID)->exec();

    return !dao::isError();
}
