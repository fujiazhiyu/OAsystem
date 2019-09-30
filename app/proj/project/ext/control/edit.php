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
     * Edit project.
     *
     * @param  int    $projectID
     * @access public
     * @return void
     */
    public function edit($projectID)
    {
        $this->checkPriv($projectID);

        if($_POST)
        {
            $changes  = $this->project->update($projectID);
            $actionID = $this->loadModel('action')->create('project', $projectID, 'Edited');
            if($changes) $this->action->logHistory($actionID, $changes);

            if(dao::isError()) $this->send(array('result' => 'fail', 'message' => dao::getError()));
            $this->send(array('result' => 'success', 'message' => $this->lang->saveSuccess, 'locate' => 'reload'));
        }

        $this->view->title   = $this->lang->project->edit;
        $this->view->users  = $this->project->getCandidates();
        $this->view->project = $this->project->getByID($projectID);
        $this->view->groups  = $this->loadModel('group')->getPairs();
        $this->display();
    }

}
