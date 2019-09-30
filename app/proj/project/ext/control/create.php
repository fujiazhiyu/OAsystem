<?php
/**
 * 自定义扩展 覆盖project => control -> create.
 *
 * @author      Yu Fujia <fujiazhiyu@sina.com>
 * @package     project/ext
 */

include '../../control.php';

class myProject extends project
{
    /**
    * 覆盖原有create方法，限制项目中 负责人/团队 的候选人
    */
    public function create()
    {
        if($_POST)
        {
            $projectID = $this->project->create();
            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->loadModel('action')->create('project', $projectID, 'Created');
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => $this->createLink('task', 'browse', "projectID={$projectID}")));
        }

        $this->view->title  = $this->lang->project->create;
        $this->view->users  = $this->loadModel('user')->getPairs('noclosed,nodeleted,noforbidden');
        $this->view->users  = $this->project->getCandidates();
        $this->view->groups = $this->loadModel('group')->getPairs();
        $this->display();
    }

}
