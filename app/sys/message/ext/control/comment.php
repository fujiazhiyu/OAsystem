<?php
/**
 * 自定义扩展 覆盖 message => control -> comment.
 *
 * @author      Yu Fujia <fujiazhiyu@sina.com>
 * @package     message/ext
 */

include '../../control.php';

class myMessage extends message
{
    /**
     * Show the comment of one object, and print the comment form.
     *
     * @param string $objectType
     * @param string $objectID
     * @access public
     * @return void
     */
    public function comment($objectType, $objectID, $pageID = 1)
    {
        $this->app->loadClass('pager', $static = true);
        $pager = new pager($recTotal = 0 , $recPerPage = 10, $pageID);

        $this->view->objectType  = $objectType;
        $this->view->objectID    = $objectID;
        $this->view->comments    = $this->message->getByObject($type = 'comment', $objectType, $objectID, $pager);
        $this->view->replies     = $this->message->getReplies($this->view->comments);
        $this->view->pager       = $pager;
        $this->view->startNumber = ($pageID - 1) * 10;
        $this->lang->message     = $this->lang->comment;
        /* 自定义评论显示开关 */
        $this->view->postComments = $this->app->user->rights["message"]["post"];
        $this->display();
    }

}
