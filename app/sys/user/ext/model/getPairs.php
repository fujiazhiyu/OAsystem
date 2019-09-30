<?php
/**
 * Get user pairs list. 自定义方法by yufujia
 *
 * @param  int|array $dept
 * @param  string    $mode
 * @param  mixed     $accountList   string | array
 * @param  string    $search
 * @param  string    $orderBy
 * @param  object    $pager
 * @access public
 * @return array
 */
public function getPairs($params = '', $dept = 0, $pager = null)
{
    // 当前用户的dept部门id
    $selfDept = (int)$this->app->user->dept;
    $accessableDeptList = array();

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

    $users = $this->dao->select('account, realname')->from(TABLE_USER)
        ->where(1)
        ->beginIF(strpos($params, 'nodeleted') !== false)->andWhere('deleted')->eq('0')->fi()
        ->beginIF(strpos($params, 'noforbidden') !== false)
        ->andWhere('locked', true)->eq('0000-00-00 00:00:00')
        ->orWhere('locked')->lt(helper::now())
        ->markRight(1)
        ->fi()
        ->beginIF(strpos($params, 'admin') !== false)->andWhere('admin')->ne('no')->fi()
        ->beginIF($dept)->andWhere('dept')->in($dept)->fi()
        ->andWhere('dept')->in($accessableDeptList)
        ->orderBy('id_asc')
        ->beginIF($pager)->page($pager)->fi()
        ->fetchPairs();

    foreach($users as $account => $realname) if($realname == '') $users[$account] = $account;

    if(!$accessableDeptList)
    {
        $users[$this->app->user->account] = $this->app->user->account;
    }

    /* Append empty users. */
    if(strpos($params, 'noempty') === false) $users = array('' => '') + $users;
    if(strpos($params, 'noclosed') === false) $users = $users + array('closed' => 'Closed');

    return $users;
}
