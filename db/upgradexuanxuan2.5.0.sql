UPDATE `sys_config` SET `key` = 'https' WHERE `owner` = 'system' AND `module` = 'common' AND `section` = 'xuanxuan' AND `key` = 'isHttps';
UPDATE `sys_config` SET `key` = 'backendLang' WHERE `owner` = 'system' AND `module` = 'common' AND `section` = 'xuanxuan' AND `key` = 'xxbLang';

UPDATE `sys_grouppriv` SET `module` = 'client', `method` = 'browse' WHERE `module` = 'setting' AND `method` = 'xxcversion';
UPDATE `sys_grouppriv` SET `module` = 'client', `method` = 'create' WHERE `module` = 'setting' AND `method` = 'createxxcversion';
UPDATE `sys_grouppriv` SET `module` = 'client', `method` = 'edit'   WHERE `module` = 'setting' AND `method` = 'editxxcversion';
UPDATE `sys_grouppriv` SET `module` = 'client', `method` = 'delete' WHERE `module` = 'setting' AND `method` = 'deletexxcversion';

RENAME TABLE `im_xxcversion` TO `im_client`;

ALTER TABLE `im_client` CHANGE `readme` `changeLog` text NOT NULL;
ALTER TABLE `im_client` ADD `status` ENUM('released','wait')  NOT NULL  DEFAULT 'wait'  AFTER `editedBy`;
