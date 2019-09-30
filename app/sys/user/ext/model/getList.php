<?php
/**
 * Get users List. 自定义方法by yufujia
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
public function getList($dept = 0, $mode = 'normal', $accountList = '', $search = '', $orderBy = 'id', $pager = null)
{
    // 当前用户的dept部门id
    $selfDept = (int)$this->app->user->dept;
    // 需要展示的部门id list
    $visableDeptList = array(0 => -1);
    // 该用户可见的部门id list
    $accessableDeptList = array();
    $this->loadModel('tree');
    if(!is_array($selfDept)) $selfDept = explode(',', $selfDept);

    // 获取所有该user可以查看的用户id
    foreach($selfDept as $d)
    {
        // 得到该用户所在部门的上级部门list
        $originDepts = $this->tree->getOrigin($d);
        // 上级部门的所有人员加入到可查看列表
        foreach($originDepts as $deptID => $deptValue)
        {
            $accessableDeptList = array_merge($accessableDeptList, array($deptID => $deptValue->id));
        }

        // 得到该用户所在部门下属所有部门id
        $childrenDepts = $this->tree->getAllChildId($d);
        // 下属部门素有人员加入可查看列表
        $accessableDeptList = array_merge($accessableDeptList, $childrenDepts);
    }

    if($dept)
    {
        // 列表去重
        $accessableDeptList = array_unique($accessableDeptList);
        if(!is_array($dept)) $dept = explode(',', $dept);
        // 根据当前用户可查看列表 和 当前选择的浏览列表 筛选 当前选择下的可见列表
        foreach ($dept as $d) {
            $depts           = $this->tree->getFamily($d);
            $visableDeptList = array_merge($visableDeptList, array_intersect($accessableDeptList, $depts));
        }
    }
    else
    {
        // 直接选择“同事”栏
        $visableDeptList = $accessableDeptList;
    }

    return $this->dao->select('*')->from(TABLE_USER)
        ->where('dept')->in($visableDeptList)
        ->beginIF($accountList)->andWhere('account')->in($accountList)->fi()
        ->beginIF($mode != 'all')->andWhere('deleted')->eq('0')->fi()
        ->beginIF($mode == 'forbid')->andWhere('locked')->ge(helper::now())->fi()

        ->beginIF($mode == 'normal')
        ->andWhere('locked', true)->eq('0000-00-00 00:00:00')
        ->orWhere('locked')->lt(helper::now())
        ->markRight(1)
        ->fi()

        ->beginIF($search)
        ->andWhere('account', true)->like("%$search%")
        ->orWhere('realname')->like("%$search%")
        ->markRight(1)
        ->fi()

        ->orderBy($orderBy)
        ->page($pager)
        ->fetchAll();
}
