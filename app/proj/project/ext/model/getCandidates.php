<?php

/**
* 获取候选者，用户只可获取自己和自己管辖部门的员工作为候选者
* @author      Yu Fujia <fujiazhiyu@sina.com>
* @package     project/ext
*/
public function getCandidates()
{
    // 当前用户的dept部门id
    $selfDept = (int)$this->app->user->dept;
    $accessableDeptList = array();

    $this->loadModel('user');
    $this->loadModel('tree');
    if(!is_array($selfDept)) $selfDept = explode(',', $selfDept);

    // 获取所有该user可以查看的用户id
    foreach($selfDept as $d)
    {
        // 当前用户管理的dept
        $manageDepts = $this->tree->getDeptManagedByMe($this->app->user->account);
        foreach($manageDepts as $mdId => $mdInfo)
        {
            // 得到该用户所在部门下属所有部门id
            $childrenDepts = $this->tree->getAllChildId($mdId);
            // 当前部门和下属部门dept id加入可查看dept列表
            $accessableDeptList = array_merge($accessableDeptList, $childrenDepts);
        }
    }
    // 列表去重
    $accessableDeptList = array_unique($accessableDeptList);
    $accessableUsers = $accessableDeptList ? $this->user->getPairs('noclosed,nodeleted,noforbidden', $accessableDeptList) : array();
    $accessableUsers[$this->app->user->account] = $this->app->user->realname;
    return $accessableUsers;
}
