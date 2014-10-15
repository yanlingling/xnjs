<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class WushuController extends ApplicationController
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
            $sql = "SELECT y.depart_id,y.depart_name
          FROM  " . TABLE_PREFIX . "department AS y
          WHERE y.depart_id=" . $_GET['depart_id'];
        } else {
            $sql = "SELECT y.depart_id,y.depart_name
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.manager_id = z.id
            AND z.id =" . logged_user()->getId();
        }

        $rows1 = DB::executeAll($sql);
        DB::commit();
        tpl_assign('departInfo', $rows1);
        $depart_id = $rows1[0]['depart_id'];
        $depart_name = $rows1[0]['depart_name'];
        // 查询督察岗位职责
        if ($depart_name == '效能办') {
            $sql = "SELECT x.id, x.title,x.text,z.username,x.due_date,x.light_status,x.supervise_status
            FROM " . TABLE_PREFIX . "project_tasks AS x, " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE x.assigned_to_departid = y.depart_id
            AND x.assigned_to_departid  = $depart_id
            AND z.id  = y.manager_id
            and x.supervise_status != 0
            order by x.light_status DESC";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('supervise_task_list', $rows);
        }

        // 查询岗位职责状态的汇总情况
        DB::beginWork();
        $sql = "SELECT y.depart_name AS name,  `light_status` , COUNT(  `light_status` ) AS light_count, y.score
            FROM  `og_project_tasks` AS x,  `og_department` AS y
            WHERE  `assigned_to_departid` =$depart_id AND y.depart_id =$depart_id
            GROUP BY light_status
            ORDER BY light_status ASC";
        $rows = DB::executeAll($sql);
        DB::commit();
        tpl_assign('task_overview_data', $rows);

        // 查询岗位职责列表
        DB::beginWork();

        $sql = "SELECT x.id, x.title,x.text,z.username,x.due_date,x.light_status,x.supervise_status,x.complete_detail,x.completed_on
            FROM " . TABLE_PREFIX . "project_tasks AS x, " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE x.assigned_to_departid = y.depart_id
            AND x.assigned_to_departid  = $depart_id
            AND z.id  = y.manager_id
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
            AND x.depart_id =$depart_id
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
        $sql = "SELECT y.depart_id,y.depart_name, y.score, MAX( x.light_status ) AS light_status
            FROM " . TABLE_PREFIX . "project_tasks AS x, "
            . TABLE_PREFIX . "department AS y
            WHERE x.`assigned_to_departid` = y.depart_id
            GROUP BY x.`assigned_to_departid`";

        /* $sql = "SELECT y.depart_id,y.depart_name, y.score, MAX( x.light_status ) AS light_status,count(id)
             FROM " . TABLE_PREFIX . "project_tasks AS x, "
             . TABLE_PREFIX . "department AS y, "
             . TABLE_PREFIX . "og_project_task_delay_apply AS z
             WHERE x.`assigned_to_departid` = y.depart_id and z.depart_id=x.id and z.status=0
             GROUP BY x.`assigned_to_departid`";*/
        // 副局长只能看到分管科室的
        if (logged_user()->getUserRole() == '副局长') {
            $sql = "SELECT y.depart_id,y.depart_name, y.score, MAX( x.light_status ) AS light_status
                FROM " . TABLE_PREFIX . "project_tasks AS x, " . TABLE_PREFIX . "department AS y, " . TABLE_PREFIX . "users AS z
                WHERE x.`assigned_to_departid` = y.depart_id and y.fujuzhang_id=z.id
                GROUP BY x.`assigned_to_departid`";
        }
        $rows = DB::executeAll($sql);
        DB::commit();

        $i = 0;
        foreach ($rows as $groupItem) {
            $dep_id = $groupItem['depart_id'];
            $sql2 = "SELECT count(*) as count
            FROM  " . TABLE_PREFIX . "project_task_delay_apply
            WHERE  `depart_id` =$dep_id
            AND STATUS =0";
            $rows2 = DB::executeAll($sql2);
            DB::commit();
            // print_r($rows2);
            $rows[$i]['apply_num'] = $rows2[0]['count'];
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
           '0', '" . $_POST['depart'] . "','" . $_POST['title'] . "','" . $_POST['detail'] . "','" . date("Y-m-d h:i:s", strtotime("$due_date +1   day") - 1) . "',
           '" . date('Y-m-d H:i:s', time()) . "','" . logged_user()->getId() . "',2)";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
        } else if (isset($_GET['opt']) && $_GET['opt'] == 'edit') {
            // 统一一下比较的时间
            $dayDiff = (strtotime(date('Y-m-d', time())) - strtotime($due_date)) / 86400;
            $lightStatus = this.getLightStatus($dayDiff);

            $sql = "update `" . TABLE_PREFIX . "project_tasks`
            set assigned_to_departid= " . $_POST['depart'] . ",title='" . $_POST['title']
                . "', light_status='" . $lightStatus . "', text='" . $_POST['detail'] . "',due_date='" . date("Y-m-d h:i:s", strtotime("$due_date +1   day") - 1) . "'
                where id= " . $_POST['id'];
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
        } else {
            DB::beginWork();
            $sql = "SELECT * FROM " . TABLE_PREFIX . "department";
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
        $id = get_id();
        //   $randNum = rand(1, 100);
        $randNum = rand(1, 100);
        $sql = "update " . TABLE_PREFIX . "project_tasks set light_status=1,completed_on='" . date('y-m-d h:i:s', time()) . "',complete_detail='" . $_POST['detail'] . "'";
        if ($randNum == 8) {
            $sql .= ',supervise_status=1';
        }
        $sql .= " where id=" . $id;
        $rows = DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
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
`status`,`user_id`,`depart_id` ) VALUES ( '$taskId','$reason' ,'$now',  '$hopeDay','0','" . logged_user()->getId() . "','$departId' ); ";
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
        status=1, handle_time='" . date('Y-m-d H:i:s', time()) . "' where id=$id";
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
        supervise_status=2 where id=$id";
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
        DB::beginWork();
        $sql = "update `" . TABLE_PREFIX . "project_tasks` set
        supervise_status=3 where id=$id";
        DB::execute($sql);

        // 任务还没到期，改一下等的颜色为灰，让科室继续完成,否则将完成时间设置为14天以后
        if (strtotime($due_date) < time()) {
            // 再给14天的时间完成任务，将到期时间设置为今天
            $due_date1 = date('Y-m-d', time());
            $sql = "update `" . TABLE_PREFIX . "project_tasks` set due_date ='$due_date1'
           where id = $id";
            DB::execute($sql);
        }
        $sql = "update `" . TABLE_PREFIX . "project_tasks` set
            light_status=2 where id=$id";
        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }
} // TaskController


?>