<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class NewtaskController extends ApplicationController
{

    /**
     * Construct the MilestoneController
     *
     * @access public
     * @param void
     * @return MilestoneController
     */
    function __construct()
    {
        parent::__construct();
        prepare_company_website_controller($this, 'website');
    } // __construct

    /**
     * 获取任务列表
     */
    function new_list_tasks()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        // $userRole = logged_user()->getUserRole();

        //  查询当前部门名称
        DB::beginWork();
        tpl_assign('userRole', logged_user()->getUserRole());
        // 指定科室的时候，查询指定科室的，否则查询当前用户科室的
        if (isset($_GET['depart_id'])) {
            $sql = "SELECT y.depart_id,y.depart_name,y.manager_id
          FROM  " . TABLE_PREFIX . "department AS y
          WHERE y.depart_id=" . $_GET['depart_id'];
        } else {
            $sql = "SELECT y.depart_id,y.depart_name,y.manager_id
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.depart_id = z.depart_id
            AND z.id =" . logged_user()->getId();
        }
        $year = '2015';
        if (isset($_GET['year'])) {
            $year = $_GET['year'];
        }

        tpl_assign('year', $year);

        $rows1 = DB::executeAll($sql);
        DB::commit();
        tpl_assign('departInfo', $rows1);
        $depart_id = $rows1[0]['depart_id'];
        tpl_assign('departId', $depart_id);
        $depart_name = $rows1[0]['depart_name'];
        $manager_id = $rows1[0]['manager_id'];
        $isSelf = 'other';
        if ($manager_id == logged_user()->getId()) {
            $isSelf = 'self';
        }
        //   echo $isSelf;
        tpl_assign('isSelf', $isSelf);
        tpl_assign('depart_name', $depart_name);
        // 查询当前登录用户的科室
        $sql = "SELECT depart_name
FROM  `og_users` AS x, og_department AS y
WHERE  `id` =" . logged_user()->getId() . "
AND x.depart_id = y.depart_id ";
        $rows1 = DB::executeAll($sql);
        tpl_assign('logged_user_depart', $rows1[0]['depart_name']);
        // 查询督察岗位职责
        if ($depart_name == '效能办') {
            $sql = "SELECT x.id, x.title,x.text,z.username,x.due_date,x.light_status,x.supervise_status,
            x.advanced_supervise,x.assigned_to_departid
            FROM " . TABLE_PREFIX . "project_tasks AS x, " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE x.assigned_to_departid = y.depart_id
            AND z.id  = y.manager_id
            and (x.supervise_status != 0 or x.advanced_supervise != 0 )and x.deleted = !1
            order by x.light_status DESC";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('supervise_task_list', $rows);
        }

        // 查询待评价岗位职责
        if ($depart_name == '效能办' || logged_user()->getUserRole() == '局长'
            || logged_user()->getUserRole() == '副局长'
        ) {
            $sql = "SELECT x.id, x.title,x.text,z.username,x.due_date,x.light_status,x.supervise_status,
            x.advanced_supervise,x.assigned_to_departid,x.completed_on
            FROM " . TABLE_PREFIX . "project_tasks AS x, " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE   z.id  = y.manager_id
             and x.`assigned_to_departid` = y.depart_id
            and x.deleted = !1
            and x.light_status=1";
            if ($depart_name == '效能办' && $isSelf == 'self') {
                $sql .= " and comment_status_xiaoneng=0";
            } else if (logged_user()->getUserRole() == '副局长') { // 副局长等效能办评完了才能评
                $sql .= " and comment_status_fujuzhang=0
                and comment_status_xiaoneng!=0
                and comment_status_xiaoneng!=-1
                 and x.assigned_to_departid =$depart_id"; //-1为不用评价的任务
            } else if (logged_user()->getUserRole() == '局长') {
                $sql .= " and comment_status_juzhang=0
                and comment_status_fujuzhang!=0
                and comment_status_xiaoneng!=-1
                and x.assigned_to_departid =$depart_id";
            }
            $sql .= " order by x.completed_on DESC";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('comment_task_list', $rows);
            //tpl_assign('comment_task_list',  array());
            tpl_assign('has_comment_auth', true);
        }


        //科室列表
        $sql = "SELECT depart_id,depart_name FROM " . TABLE_PREFIX . "department";
        $rows = DB::executeAll($sql);
        tpl_assign('depart_list', $rows);

        // 查询岗位职责状态的汇总情况
        DB::beginWork();
        $sql = "SELECT y.depart_name AS name,  `light_status` , COUNT(  `light_status` ) AS light_count, y.score,
            y.xiaoneng_score
            FROM  `og_project_tasks` AS x,  `og_department` AS y
            WHERE  `assigned_to_departid` =$depart_id AND y.depart_id =$depart_id  and x.deleted = !1
            GROUP BY light_status
            ORDER BY light_status ASC";
        $rows = DB::executeAll($sql);
        DB::commit();
        tpl_assign('task_overview_data', json_encode($rows));

        // 查询岗位职责列表
        DB::beginWork();

        $sql = "SELECT x.id, x.title,x.text,z.username,x.due_date,x.light_status,
            x.supervise_status,x.supervise_feedback,
            x.advanced_supervise,x.advanced_supervise_feedback,
            x.assigned_to_departid,
            x.complete_detail,x.completed_on,
            x.tuijian,x.pishi,
            x.created_on,x.reason_deliver,x.xiaoneng_score
            FROM og_project_tasks AS x, og_users AS z, og_department AS y
            WHERE x.assigned_to_departid = y.depart_id
            AND x.assigned_to_departid  = $depart_id
            AND z.id  = y.manager_id  and x.deleted = !1 and year(due_date)=$year
            order by x.light_status DESC,x.due_date ASC";

        $rows = DB::executeAll($sql);
        DB::commit();
        tpl_assign('task_list', $rows);

        // 查询延期申请
        DB::beginWork();
        $sql = "SELECT x.id,x.reason, y.id as task_id,y.title, x.hope_day, x.agree_day, x.status,x.create_time, x.handle_time,z.username
            FROM " . TABLE_PREFIX . "project_task_delay_apply AS x, " . TABLE_PREFIX . "project_tasks AS y, " . TABLE_PREFIX . "users AS z
            WHERE x.task_id = y.id
            AND z.id  = x.user_id
            AND x.depart_id =$depart_id  and y.deleted = !1
            order by x.create_time DESC,x.status ASC
            ";
        $rows = DB::executeAll($sql);
        DB::commit();
        tpl_assign('apply_list', $rows);


        $tab = 'task';
        if (isset($_GET['tab'])) {
            $tab = $_GET['tab'];
        }
        tpl_assign('tab', $tab);

    }

    function new_list_tasks_of_juzhang()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        // 查询岗位职责列表
        DB::beginWork();
        // 局长查询科室的汇总
        $sql = "SELECT y.depart_id,y.depart_name, y.xiaoneng_score as score, MAX( x.light_status ) AS light_status
            FROM " . TABLE_PREFIX . "project_tasks AS x, "
            . TABLE_PREFIX . "department AS y
            WHERE x.`assigned_to_departid` = y.depart_id and x.deleted=0
            GROUP BY x.`assigned_to_departid`";

        /* $sql = "SELECT y.depart_id,y.depart_name, y.score, MAX( x.light_status ) AS light_status,count(id)
             FROM " . TABLE_PREFIX . "project_tasks AS x, "
             . TABLE_PREFIX . "department AS y, "
             . TABLE_PREFIX . "og_project_task_delay_apply AS z
             WHERE x.`assigned_to_departid` = y.depart_id and z.depart_id=x.id and z.status=0
             GROUP BY x.`assigned_to_departid`";*/
        // 副局长只能看到分管科室的
        if (logged_user()->getUserRole() == '副局长') {
            $sql = "SELECT y.depart_id,y.depart_name,y.xiaoneng_score as score, MAX( x.light_status ) AS light_status
                FROM " . TABLE_PREFIX . "project_tasks AS x, " . TABLE_PREFIX . "department AS y, " . TABLE_PREFIX . "users AS z
                WHERE x.`assigned_to_departid` = y.depart_id "
                . "and  FIND_IN_SET(z.id, y.fujuzhang_id) !=0 and x.deleted=0
                and z.id=" . logged_user()->getId() . "
                GROUP BY x.`assigned_to_departid`";
        }
        $rows = DB::executeAll($sql);
        DB::commit();

        $i = 0;
        foreach ($rows as $groupItem) {
            $dep_id = $groupItem['depart_id'];
            $sql2 = "SELECT count(*) as count
            FROM  " . TABLE_PREFIX . "project_task_delay_apply as x," . TABLE_PREFIX . "project_tasks as y
            WHERE  `depart_id` =$dep_id
            and x.task_id = y.id
            and y.deleted!=1
            AND STATUS =0
            ";
            $rows2 = DB::executeAll($sql2);
            // print_r($rows2);
            $rows[$i]['apply_num'] = $rows2[0]['count'];

            // 计算待评价任务
            if (logged_user()->getUserRole() == '副局长') {
                // 效能办评价完了  副局长才能评
                $sql3 = "SELECT count(*) as count
            FROM  " . TABLE_PREFIX . "project_tasks as y
            WHERE  `assigned_to_departid` =$dep_id
            and y.deleted!=1
            AND light_status =1 and comment_status_fujuzhang=0 and comment_status_xiaoneng!=0
            ";
            }
            else if (logged_user()->getUserRole() == '局长') {
                $sql3 = "SELECT count(*) as count
            FROM  " . TABLE_PREFIX . "project_tasks as y
            WHERE  `assigned_to_departid` =$dep_id
            and y.deleted!=1
            AND light_status =1 and comment_status_juzhang=0 and comment_status_fujuzhang!=0
            ";
            }
            // 效能办可以查看全局任务
            else {
                $sql3 = "SELECT count(*) as count
            FROM  " . TABLE_PREFIX . "project_tasks as y
            WHERE  `assigned_to_departid` =$dep_id
            and y.deleted!=1
            AND light_status =1 and comment_status_xiaoneng=0 and comment_status_xiaoneng!=-1
            ";
            }
            $rows3 = DB::executeAll($sql3);
            $rows[$i]['comment_num'] = $rows3[0]['count'];
            $i++;
        }
        tpl_assign('group_task_list', $rows);
    }


    /**
     * @param $dayDiff
     * 编辑的时候，重新设置灯的状态
     */
    function getLightStatus($dayDiff)
    {
        // 还未到期
        if ($dayDiff <= 0) {
            return 2; //灰色
        } else if ($dayDiff >= 8) {
            return 4; //红色
        } else { // 黄色
            return 3;
        }

    }

    function detail_task()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        DB::beginWork();
        $id = $_POST['taskId'];
        DB::beginWork();


        $sql = "SELECT x.id, x.title,x.text,z.username,x.due_date,x.light_status,
            x.supervise_status,x.supervise_feedback,
            x.advanced_supervise,x.advanced_supervise_feedback,
            x.assigned_to_departid,
            x.complete_detail,x.completed_on,
            x.created_on,x.reason_deliver,x.deliver_to_departid,x.deliver_from_departid,
            x.comment_juzhang,
            x.comment_fujuzhang,
            x.comment_xiaoneng,
            x.comment_status_juzhang,
            x.comment_status_fujuzhang,
            x.tuijian,
            x.pishi,
            x.pishi_juzhang,
            x.comment_status_xiaoneng
            FROM og_project_tasks AS x, og_users AS z, og_department AS y
            WHERE x.assigned_to_departid = y.depart_id
            AND z.id  = y.manager_id
            AND x.id= " . $id;
        $rows = DB::executeAll($sql);
        DB::commit();
        if ($rows[0]['light_status'] == 0) {
            $sql = "select * from og_department where depart_id=" . $rows[0]['deliver_to_departid'];
            $dep = DB::executeAll($sql);
            $rows[0]['toDepart'] = $dep[0]['depart_name'];
        }
        // 是转交的任务
        if ($rows[0]['deliver_from_departid']) {
            $sql = "select * from og_department where depart_id=" . $rows[0]['deliver_from_departid'];
            $dep = DB::executeAll($sql);
            $rows[0]['fromDepart'] = $dep[0]['depart_name'];
        }
        $rows[0]['text'] = str_replace("\n", "&#13;&#10;", $rows[0]['text']);
        tpl_assign('taskDetail', $rows[0]);
    }

    function detail_xiaonengscore()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        DB::beginWork();
        $id = $_POST['taskId'];
        DB::beginWork();


        $sql = "SELECT x.id, x.title,x.text,z.username,x.due_date,x.light_status,
            x.supervise_status,x.supervise_feedback,
            x.advanced_supervise,x.advanced_supervise_feedback,
            x.assigned_to_departid,
            x.complete_detail,x.completed_on,
            x.created_on,x.reason_deliver,x.deliver_to_departid,x.deliver_from_departid,
            x.comment_juzhang,
            x.comment_fujuzhang,
            x.comment_xiaoneng,
            x.comment_status_juzhang,
            x.comment_status_fujuzhang,
            x.tuijian,
            x.pishi,
            x.comment_status_xiaoneng
            FROM og_project_tasks AS x, og_users AS z, og_department AS y
            WHERE x.assigned_to_departid = y.depart_id
            AND z.id  = y.manager_id
            AND x.id= " . $id;
        $rows = DB::executeAll($sql);
        tpl_assign('taskDetail', $rows[0]);
    }
    /**
     * 添加任务的处理
     */
    function add_task()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $due_date = $_POST['dueDate'];
        // 处理请求
        if (isset($_GET['opt']) && $_GET['opt'] == 'add') {

            $sql = "INSERT INTO `" . TABLE_PREFIX . "project_tasks` (
           `supervise_status`,`assigned_to_departid`,  `title`, `text`, `due_date`,
           `created_on`, `created_by_id`,`light_status`) VALUES (
           '0', '" . $_POST['depart'] . "','" . $_POST['title'] . "','" . $_POST['detail'] . "','" . date("Y-m-d H:i:s", strtotime("$due_date +1   day") - 1) . "',
           now(),'" . logged_user()->getId() . "',2)";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
        } else if (isset($_GET['opt']) && $_GET['opt'] == 'edit') {
            // 统一一下比较的时间
            $dayDiff = (strtotime(date('Y-m-d', time())) - strtotime($due_date)) / 86400;
            $lightStatus = $this->getLightStatus($dayDiff);

            $sql = "update `" . TABLE_PREFIX . "project_tasks`
            set assigned_to_departid= " . $_POST['depart'] . ",title='" . $_POST['title']
                . "', light_status='" . $lightStatus . "', text='" . $_POST['detail'] . "',due_date='" . date("Y-m-d H:i:s", strtotime("$due_date +1   day") - 1) . "'
                where id= " . $_POST['id'];
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
        } else {
            DB::beginWork();
            $sql = "SELECT depart_id,depart_name FROM " . TABLE_PREFIX . "department";
            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('depart_list', json_encode($rows));
            if ($_GET['opt'] == 'view') {
                $id = $_POST['taskId'];
                DB::beginWork();
                $sql = "SELECT x.id, x.title,x.text,x.due_date,y.depart_name FROM " . TABLE_PREFIX . "project_tasks as x," . TABLE_PREFIX . "department as y
            where x.id=" . $id . " and x.assigned_to_departid = y.depart_id";
                $rows = DB::executeAll($sql);
                DB::commit();

                $rows[0]['text'] = str_replace("\n", "&#13;&#10;", $rows[0]['text']);
                tpl_assign('taskContent', json_encode($rows[0]));
                tpl_assign('opt', 'view');

            }
        }
    }

    /**
     * 设置任务完成
     */
    function complete_task()
    {
        DB::beginWork();
        $nowDate = date('Y-m-d', time());
        $id = get_id();
        //$randNum = rand(2, 2);
        $supervise_status = $_POST['supervise_status'];
        $adv_supervise_status = $_POST['adv_supervise_status'];
        $randNum = rand(1, 3);
        $sql = "update " . TABLE_PREFIX . "project_tasks set
         light_status=1,
         completed_on=now(),
         complete_detail='" . $_POST['detail'] . "',
         comment_xiaoneng_deadline='" . date("Y-m-d H:i:s", strtotime("$nowDate +30   day") - 1) . "'";
        // 主动督察不通过的时候再完成，不用随机督察
        if ($randNum == 2 && $adv_supervise_status != 3) {
            $sql .= ',supervise_status=1';
        }
        // 如果是督察不通过，继续督察
        if ($adv_supervise_status == 3) {
            $sql .= ',advanced_supervise=1';
        }
        if ($supervise_status == 3) {
            $sql .= ',supervise_status=1';
        }

        $sql .= " where id=" . $id;
        $rows = DB::executeAll($sql);
        // 计算任务基本得分
        $this->updateTaskScore($id);
        DB::commit();
        ajx_current("empty");
    }

    function advanced_supervise()
    {
        DB::beginWork();
        $id = get_id();
        // $randNum = rand(8, 8);
        $sql = "update " . TABLE_PREFIX . "project_tasks set advanced_supervise=1";
        $sql .= " where id=" . $id;
        $rows = DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 生成任务
     */
    function delete_task()
    {
        DB::beginWork();
        $id = get_id();
        $sql = "update " . TABLE_PREFIX . "project_tasks set deleted = 1";
        $sql .= " where id=" . $id;
        $rows = DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 处理任务转交
     */
    function task_deliver()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $taskId = $_POST['taskId'];
        $reason = $_POST['reason'];
        $departId = $_POST['departId'];

        DB::beginWork();
        $sql = "SELECT y.depart_id,y.depart_name,y.manager_id
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.depart_id = z.depart_id
            AND z.id =" . logged_user()->getId();

        $rows1 = DB::executeAll($sql);
        DB::commit();
        tpl_assign('departInfo', $rows1);

        $from_depart_id = $rows1[0]['depart_id'];

        $sql = "SELECT * FROM og_project_tasks WHERE id=" . $taskId;
        $rows = DB::executeAll($sql);
        $data = $rows[0];
        $due_date = $data['due_date'];

        $sql = "INSERT INTO `" . TABLE_PREFIX . "project_tasks` (
           `supervise_status`,`assigned_to_departid`,  `title`, `text`, `due_date`,
           `created_on`, `created_by_id`,`light_status`,`reason_deliver`,
           `deliver_from_taskid`,`deliver_from_departid`) VALUES (
           '0', '" . $departId . "','" . $data['title'] . "','" . $data['text'] . "','" . date("Y-m-d H:i:s", strtotime("$due_date +7   day")) . "',
           now(),'" . logged_user()->getId() . "',2,'" . $reason . "'," . $data['id'] . "," . $from_depart_id . ")";

        DB::execute($sql);

        //更新状态
        $sql = "UPDATE `og_project_tasks` SET `light_status`=0,`deliver_to_departid`=" . $departId . " WHERE id=" . $taskId;
        DB::execute($sql);

        DB::commit();
        ajx_current("empty");
    }

    function task_comment()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $taskId = $_POST['taskId'];
        $tuijianReason = $_POST['tuijianReason'];
        $pishiReason = $_POST['pishiReason'];
        $status = $_POST['status'];
        $role = logged_user()->getUserRole();
        DB::beginWork();
        $nowDate = date('Y-m-d', time());
        if ($role == '局长') {
            //更新状态
            $sql = "UPDATE `og_project_tasks` SET
 `comment_juzhang`='" . $pishiReason. "' ,
 `pishi_juzhang`='" . $_POST['pishi'] . "'  ,
  `comment_status_juzhang`=" . $status . " WHERE id=" . $taskId;
        } else if ($role == '副局长') {
            //更新状态
            $sql = "UPDATE `og_project_tasks` SET
`comment_fujuzhang`='" . $pishiReason. "'  ,
`pishi`='" . $_POST['pishi'] . "'  ,
 `comment_status_fujuzhang`=" . $status . "
  WHERE id=" . $taskId;
        } else {
            //更新状态
            $sql = "UPDATE `og_project_tasks` SET
`comment_xiaoneng`='" . $tuijianReason. "' ,
 `comment_status_xiaoneng`=" . $status . ",
 comment_fujuzhang_deadline='" . date("Y-m-d H:i:s", strtotime("$nowDate +30   day") - 1) . "',
 `tuijian`=" . $_POST['tuijian']. "
 WHERE id=" . $taskId;
        }
        DB::execute($sql);
        // 计算任务基本得分
        $this->updateTaskScore($taskId);
        DB::commit();
        ajx_current("empty");
    }

    /**更新任务的效能得分
     * @param $taskId
     * @throws Exception
     */
    function updateTaskScore ($taskId) {
        $sql = " select  assigned_to_departid from  og_project_tasks where id=".$taskId;
        DB::beginWork();
        $row = DB::executeAll($sql);
        $item = $row[0];
        $sql = " call computeTaskScore($taskId)";
        DB::executeOne($sql);
        $sql = " call computeDepartScore(".$item['assigned_to_departid'].")";
        DB::executeOne($sql);
    }


    /**
     * 根据效能等级计算得分
     * @param $status
     * @return int
     */
function  getXiaonengScore($status) {
    if ($status ==1) {
        return 50;
    }
    if ($status ==2) {
        return 40;
    }
    if ($status ==3) {
        return 30;
    }
}
    /**
     * 处理任务延期的申请
     */
    function task_delay_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $taskId = get_id();
        $reason = $_POST['reason'];
        $hopeDay = $_POST['hopeDay'];
        $departId = $_POST['departId'];
        $now = date('Y-m-d H:i:s', time());
        $project = $_GET['active_project'];
        DB::beginWork();
        $sql = "INSERT INTO  `" . TABLE_PREFIX . "project_task_delay_apply` ( `task_id` ,`reason` , `create_time` , `hope_day` ,
`status`,`user_id`,`depart_id` ) VALUES ( '$taskId','$reason' ,now(),  '$hopeDay','0','" . logged_user()->getId() . "','$departId' ); ";
        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 同意延期申请的请求
     */
    function agree_delay_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $taskId = $_POST['taskId'];
        ajx_current("empty");
        $id = get_id();
        $agreeDay = $_POST['agreeDay'];
        DB::beginWork();
        $sql = "update `" . TABLE_PREFIX . "project_task_delay_apply` set agree_day=$agreeDay,
        status=1, handle_time=now() where id=$id";
        DB::execute($sql);
        // 更新任务的到期时间
        $sql2 = "update `" . TABLE_PREFIX . "project_tasks` set due_date = DATE_ADD(due_date ,INTERVAL $agreeDay DAY)
where id = $taskId";
        DB::execute($sql2);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 不同意延期申请处理
     */
    function disagree_delay_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $id = get_id();
        DB::beginWork();
        $sql = "update `" . TABLE_PREFIX . "project_task_delay_apply` set
        status=2 where id=$id";
        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 处理编辑延期申请的请求
     */
    function task_delay_apply_edit()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $id = get_id();
        $reason = $_POST['reason'];
        $hopeDay = $_POST['hopeDay'];

        DB::beginWork();

        $sql = "update `" . TABLE_PREFIX . "project_task_delay_apply` set hope_day=$hopeDay,reason ='$reason' where id=$id";

        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 撤回申请
     */
    function task_delay_apply_cancel()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $id = get_id();
        DB::beginWork();
        $sql = "update `" . TABLE_PREFIX . "project_task_delay_apply` set status= 3 where id=$id";
        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**通过督察
     * @param $task_id
     */
    function pass_supervise()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $id = get_id();
        DB::beginWork();
        $sql = "update `" . TABLE_PREFIX . "project_tasks` set
        supervise_status=2,supervise_feedback='" . $_POST['feedback'] . "'  where id=$id";
        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 主动督察通过
     */
    function pass_adv_supervise()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $id = get_id();
        DB::beginWork();
        $sql = "update `" . TABLE_PREFIX . "project_tasks` set
       advanced_supervise=2,advanced_supervise_feedback='" . $_POST['feedback'] . "'   where id=$id";
        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }


    function reject_supervise()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $id = get_id();
        $due_date = $_POST['due_date'];
        $assigned_to_departid = $_POST['assigned_to_departid'];
        DB::beginWork();
        $sql = "update `" . TABLE_PREFIX . "project_tasks` set
        supervise_status=3,supervise_feedback='" . $_POST['feedback'] . "' where id=$id";
        DB::execute($sql);
        $MINUS_SCORE = 4;
        $sql = "UPDATE  og_department
             SET score =score-$MINUS_SCORE  WHERE  depart_id = $assigned_to_departid";
        $sql2 = "INSERT INTO og_score_detail  ( `depart_id`, `minus`, `task_id`, `minus_time`, `type`) VALUES ($assigned_to_departid ,$MINUS_SCORE ,$id, NOW(),'reject');";
        DB::execute($sql);
        DB::execute($sql2);
        // 任务到期，改一下等的颜色为黄色，让科室继续完成,否则将完成时间设置为14天以后
        $dayDiff = (strtotime(date('Y-m-d', time())) - strtotime($due_date)) / 86400;

        if ($dayDiff > 0) {
            $nowDate = date("Y-m-d", time());
            // 再给7天的时间完成任务，黄灯，但是不扣分
            $due_date1 = date("Y-m-d H:i:s", strtotime("$nowDate   +8   day") - 1);

            $sql = "update `" . TABLE_PREFIX . "project_tasks` set due_date ='$due_date1',light_status=2
           where id = $id";
        } else {
            $sql = "update `" . TABLE_PREFIX . "project_tasks` set
            light_status=2 where id=$id";
        }

        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 主动督察不通过
     */
    function reject_adv_supervise()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $id = get_id();
        $due_date = $_POST['due_date'];
        $assigned_to_departid = $_POST['assigned_to_departid'];
        // 随机督察的情况
        $random_status = $_POST['random_status'];
        DB::beginWork();
        // 1状态设为不通过
        $sql = "update `" . TABLE_PREFIX . "project_tasks` set
        advanced_supervise=3,advanced_supervise_feedback='" . $_POST['feedback'] . "' where id=$id";
        DB::execute($sql);
        $MINUS_SCORE = 4;
        // 1给责任科室扣分
        $sql = "UPDATE  og_department
             SET score =score-$MINUS_SCORE  WHERE  depart_id = $assigned_to_departid";
        $sql2 = "INSERT INTO og_score_detail  ( `depart_id`, `minus`, `task_id`, `minus_time`, `type`) VALUES ($assigned_to_departid ,$MINUS_SCORE ,$id, NOW(),'reject');";
        DB::execute($sql);
        DB::execute($sql2);
        // 任务到未期，改一下等的颜色为灰色色，让科室继续完成,否则将完成时间设置为7天以后
        $dayDiff = (strtotime(date('Y-m-d', time())) - strtotime($due_date)) / 86400;

        if ($dayDiff > 0) {
            $nowDate = date("Y-m-d", time());
            // 再给7天的时间完成任务，黄灯，但是不扣分
            $due_date1 = date("Y-m-d H:i:s", strtotime("$nowDate   +8   day") - 1);

            $sql = "update `" . TABLE_PREFIX . "project_tasks` set due_date ='$due_date1',light_status=2
           where id = $id";
        } else {
            $sql = "update `" . TABLE_PREFIX . "project_tasks` set
            light_status=2 where id=$id";
        }
        DB::execute($sql);


        // 如果随机督察的结果是通过，认为效能办失职，扣效能办4分
        if ($random_status == 2) {
            $sql = "UPDATE  og_department
             SET score =score-$MINUS_SCORE  WHERE  depart_id = 1";
            $sql2 = "INSERT INTO og_score_detail  ( `depart_id`, `minus`, `task_id`, `minus_time`, `type`) VALUES (1 ,$MINUS_SCORE ,$id, NOW(),'adv_reject');";
            DB::execute($sql);
            DB::execute($sql2);
        }

        DB::commit();
        ajx_current("empty");
    }


} // TaskController


?>