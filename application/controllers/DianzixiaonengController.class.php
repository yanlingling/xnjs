<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class DianzixiaonengController extends ApplicationController
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
        // 药械2科id
        $this->YAOXIE2KE_ID = 5;
        // 流通监管1科id
        $this->LIUTONG1KE_ID = 6;
        // 流通监管2科id
        $this->LIUTONG2KE_ID = 4;
        $sql = "SELECT y.depart_id,y.depart_name,y.manager_id
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.depart_id = z.depart_id
            AND z.id =" . logged_user()->getId();

        $rows1 = DB::executeAll($sql);
        DB::commit();
        $this->depart_id = $rows1[0]['depart_id'];
        $this->depart_name = $rows1[0]['depart_name'];
        $this->SHOULI_TIAOJIANSHENHE = 1;
        $this->SHOULI_XIANCHANGZHIDAO = 2;
        $this->SHOULI_XIANCHANGZHIDAO_TONGZHI = 3;

        $this->YANSHOU_CHOUBEIQI = 4;
        $this->YANSHOU_ZILIAOSHENHE = 5;
        $this->YANSHOU_ZILIAOLIUZHUAN = 6;
        $this->YANSHOU_XIANCHANGJIANCHA = 7;
        $this->FAZHENG_GONGSHIFAZHENG = 9;
    } // __construct

    /**
     * 获取任务列表
     */
    function index()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        // $userRole = logged_user()->getUserRole();
        tpl_assign('isYaoxie2', false);
        if ($this->depart_id == $this->YAOXIE2KE_ID) {
            tpl_assign('isYaoxie2', true);
        }
        $tab = 'xuke';
        if (isset($_GET['tab'])) {
            $tab = $_GET['tab'];
        }
        tpl_assign('tab', $tab);
        DB::beginWork();
        $sql = 'select * from og_dianzixiaoneng';
        $rows = DB::executeAll($sql);
        tpl_assign('allxukeList', $rows);
        $baseSql = 'select x.id,x.`apply_name`,x.`apply_time`,x.`apply_type`,
y.sub_process,y.create_time,y.dead_time,
y.complete_time,y.result, y.id as task_id,y.light_status
from og_dianzixiaoneng as x,og_dianzixiaoneng_task as y
where x.id=y.apply_id and x.process!=0 and y.result=3'; // 0代表已经被拒绝了得申请，3代码申请还没被处理
        if ($this->depart_name == '药械二科') {
            $sql = $baseSql . ' and (y.sub_process=1 or y.sub_process=4 or y.sub_process=21 or y.sub_process=23)';
            $rows = DB::executeAll($sql);
            tpl_assign('xukeshouliList', $rows);
            // 验收
            $sql = $baseSql . ' and (y.sub_process=5 or y.sub_process=6)';
            $rows = DB::executeAll($sql);
            tpl_assign('yanshouList', $rows);
            //发证
            $sql = $baseSql . ' and (y.sub_process=9 or y.sub_process=8 or y.sub_process=27)';
            $rows = DB::executeAll($sql);
            tpl_assign('fazhengList', $rows);
        } else {
            $sql = $baseSql . ' and (y.sub_process=2 or y.sub_process=3)';
            if ($this->depart_name == '流通监管一科') {
                $sql .= ' and x.apply_area =0';
            } else if ($this->depart_name == '流通监管二科') {
                $sql .= ' and x.apply_area =1';
            }
            $rows = DB::executeAll($sql);
            tpl_assign('xukeshouliList', $rows);
            // 验收阶段
            $sql = $baseSql . ' and (y.sub_process=7 or y.sub_process=25 )';
            if ($this->depart_name == '流通监管一科') {
                $sql .= ' and x.apply_area =0';
            } else if ($this->depart_name == '流通监管二科') {
                $sql .= ' and x.apply_area =1';
            }
            $rows = DB::executeAll($sql);
            tpl_assign('yanshouList', $rows);
        }

    }

    function del_xuke()
    {

        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        DB::beginWork();
        $id = $_GET['id'];
        $sql = 'delete from og_dianzixiaoneng where id=' . $id;
        DB::executeAll($sql);
        $sql = 'delete from og_dianzixiaoneng_task where apply_id=' . $id;
        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * to test
     * @throws Exception
     */
    function view_xuke()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        DB::beginWork();
        $id = $_GET['id'];
        $sql = 'select * from og_dianzixiaoneng where id=' . $id;
        $rows = DB::executeAll($sql);
        tpl_assign('xukeInfo', $rows [0]);
        $sql = 'select * from og_dianzixiaoneng_task where apply_id=' . $id . ' order by sub_process asc;';
        $rows = DB::executeAll($sql);
        tpl_assign('handleInfo', $rows);
    }

    //totest
    function handle_task()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        tpl_assign('taskid', $_GET['taskid']);
        ajx_current("empty");
    }

    //to test
    function handle_task_result()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $taskid = $_POST['taskid'];
        $xukeid = $_POST['xukeid'];
        $sub_process = $_POST['sub_process'];
        $type = $_POST['type'];
        $res = $_POST['result'];
        $detail = addslashes($_POST['detail']);
        $sql = "update og_dianzixiaoneng_task
                set
                result='$res',
                result_detail='$detail',
                complete_time=now(),
                light_status=1
                where id=$taskid";
        DB::beginWork();
        $rows = DB::executeAll($sql);
        // 被拒绝
        if ($res == 0) {
            $sql = "update og_dianzixiaoneng set process=0  where id=$xukeid";
            DB::executeAll($sql);
        } else {
            if ($type == '0') {
                $this->update_xuke_status($sub_process, $xukeid);
                $next = $this->getNextSubProcess($sub_process);
            } else {
                //药品企业连锁
                $this->update_liansuo_xuke_status($sub_process, $xukeid);
                $next = $this->getLiansuoNextSubProcess($sub_process);
            }
            // 生成下一个任务
            if ($next != 0) {
                if ($type == '0') {
                    $dead_time = $this->getDeadTime($next);
                } else {
                    //药品企业连锁
                    $dead_time = $this->getLiansuoDeadTime($next);
                }
                $sql = "INSERT INTO `" . TABLE_PREFIX . "dianzixiaoneng_task` (
           `sub_process`,  `apply_id`, `create_time`, `dead_time`) VALUES (" . $next . "," . $xukeid . ",now(),'" . $dead_time . "');";
                DB::executeAll($sql);
            }
        }
        DB::beginWork();
        DB::commit();
        ajx_current("empty");
    }

    function  update_liansuo_xuke_status($sub_process, $xukeid)
    {
        $sql='';
        if ($sub_process == 23) {
            $sql = "update og_dianzixiaoneng set process=3  where id=$xukeid";
        } else if ($sub_process == 25) {
            $sql = "update og_dianzixiaoneng set process=4  where id=$xukeid";
        } else if ($sub_process == 27) {
            $sql = "update og_dianzixiaoneng set process=6  where id=$xukeid";
        }
        if ($sql!=''){
            DB::executeAll($sql);
        }
    }

    function  update_xuke_status($sub_process, $xukeid)
    {
        $sql='';
        if ($sub_process == $this->SHOULI_XIANCHANGZHIDAO_TONGZHI) {
            $sql = "update og_dianzixiaoneng set process=2  where id=$xukeid";
        } else if ($sub_process == $this->YANSHOU_CHOUBEIQI) {
            $sql = "update og_dianzixiaoneng set process=3  where id=$xukeid";
        } else if ($sub_process == $this->YANSHOU_XIANCHANGJIANCHA) {
            // 进入公示发证
            $sql = "update og_dianzixiaoneng set process=4  where id=$xukeid";
        } else if ($sub_process == $this->FAZHENG_GONGSHIFAZHENG) {
            // 进入公示发证
            $sql = "update og_dianzixiaoneng set process=6  where id=$xukeid";
        }
        if ($sql!=''){
            DB::executeAll($sql);
        }
    }
    function  add_xuke()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $apply_date = $_POST['date'];
        // 处理请求
        if (isset($_GET['opt']) && $_GET['opt'] == 'add') {
            $id = time();
            $sql = "INSERT INTO `" . TABLE_PREFIX . "dianzixiaoneng` (id,
           `apply_name`,`apply_area`,  `apply_time`, `apply_type`, `apply_detail`,
           `create_time`,process) VALUES (" . $id . ",
           '" . $_POST['name'] . "', '" . $_POST['area'] . "','" . date("Y-m-d H:i:s", strtotime("$apply_date +1   day") - 1) . "',
           '" . $_POST['type'] . "','" . addslashes($_POST['detail']) . "',now(),1);";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            if ($_POST['type'] == 0) {

                // 创建相应的待办事项 条件审核
                $dead_time = $this->getDeadTime(1);
                $sql = "INSERT INTO `" . TABLE_PREFIX . "dianzixiaoneng_task` (
           `sub_process`,  `apply_id`, `create_time`, `dead_time`) VALUES (
            1," . $id . ",now(),'" . $dead_time . "');";
                $rows = DB::executeAll($sql);
            } else {

                // 创建相应的待办事项 条件审核
                $dead_time = $this->getLiansuoDeadTime(21);
                $sql = "INSERT INTO `" . TABLE_PREFIX . "dianzixiaoneng_task` (
           `sub_process`,  `apply_id`, `create_time`, `dead_time`) VALUES (
            21," . $id . ",now(),'" . $dead_time . "');";
                $rows = DB::executeAll($sql);
            }
            DB::commit();
            ajx_current("empty");
        } else if (isset($_GET['opt']) && $_GET['opt'] == 'edit') {
            /*            // 统一一下比较的时间
                        $dayDiff = (strtotime(date('Y-m-d', time())) - strtotime($due_date)) / 86400;
                        $lightStatus = $this->getLightStatus($dayDiff);

                        $sql = "update `" . TABLE_PREFIX . "project_tasks`
                        set assigned_to_departid= " . $_POST['depart'] . ",title='" . $_POST['title']
                            . "', light_status='" . $lightStatus . "', text='" . $_POST['detail'] . "',due_date='" . date("Y-m-d H:i:s", strtotime("$due_date +1   day") - 1) . "'
                            where id= " . $_POST['id'];
                        DB::beginWork();
                        $rows = DB::executeAll($sql);
                        DB::commit();
                        ajx_current("empty");*/
        } else {
            DB::beginWork();
            $sql = "SELECT depart_id,depart_name FROM " . TABLE_PREFIX . "department";
            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('depart_list', json_encode($rows));
            /*            if ($_GET['opt'] == 'view') {
                            $id = $_POST['taskId'];
                            DB::beginWork();
                            $sql = "SELECT x.id, x.title,x.text,x.due_date,y.depart_name FROM " . TABLE_PREFIX . "project_tasks as x," . TABLE_PREFIX . "department as y
                        where x.id=" . $id . " and x.assigned_to_departid = y.depart_id";
                            $rows = DB::executeAll($sql);
                            DB::commit();

                            $rows[0]['text'] = str_replace("\n", "&#13;&#10;", $rows[0]['text']);
                            tpl_assign('taskContent', json_encode($rows[0]));
                            tpl_assign('opt', 'view');

                        }*/
        }
    }

    /**根据不同的子任务类型计算deadline时间
     * key为子任务的type，value为最长处理天数
     * @param $type
     * @return bool|string
     */
    function getDeadTime($type)
    {
        $map = array(
            1 => 4,
            2 => 9,
            3 => 5,
            4 => 30,
            5 => 4,
            6 => 2,
            7 => 7,
            8 => 3,
            9 => 15,
        );
        $today = date('Y-m-d', time());
        if ($type == 4) {
            return date("Y-m-d H:i:s", strtotime("$today +3   month") - 1);
        } else {
            return date("Y-m-d H:i:s", strtotime("$today +$map[$type]   day") - 1);
        }
    }

    function getNextSubProcess($pro)
    {
        $map = array(
            1 => 2,
            2 => 3,
            3 => 4,
            4 => 5,
            5 => 6,
            6 => 7,
            7 => 8,
            8 => 9,
            9 => 0,
        );
        return $map[$pro];
    }

    /**
     * 获取连锁类型的申请的下一个任务
     * @param $pro
     * @return mixed
     */
    function getLiansuoNextSubProcess($pro)
    {
        $map = array(
            21 => 23,
            23 => 25,
            25 => 27,
            27 => 0,
        );
        return $map[$pro];
    }

    function getLiansuoDeadTime($type)
    {
        $map = array(
            21 => 4,
            23 => 5,
            25 => 7,
            27 => 5
        );
        $today = date('Y-m-d', time());
        return date("Y-m-d H:i:s", strtotime("$today +$map[$type]   day") - 1);
    }
    function dianzixiaoneng_of_juzhang()
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
            $sql = "SELECT y.depart_id,y.depart_name, y.score, MAX( x.light_status ) AS light_status
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
            if (logged_user()->getUserRole() == '局长') {
                $sql3 = "SELECT count(*) as count
            FROM  " . TABLE_PREFIX . "project_tasks as y
            WHERE  `assigned_to_departid` =$dep_id
            and y.deleted!=1
            AND light_status =1 and comment_status_juzhang=0 and comment_status_fujuzhang!=0
            ";
            }
            $rows3 = DB::executeAll($sql3);
            $rows[$i]['comment_num'] = $rows3[0]['count'];
            $i++;
        }
        tpl_assign('group_task_list', $rows);
    }
    /**
     * 处理任务延期的申请
     */
    function delay_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $taskId = $_POST['taskid'];
        $reason = $_POST['detail'];
        $hopeDay = $_POST['day'];
        $departId = $this->depart_id;
        $now = date('Y-m-d H:i:s', time());
        DB::beginWork();
        $sql = "INSERT INTO  `" . TABLE_PREFIX . "dianzixiaoneng_task_delay_apply` ( `task_id` ,`reason` , `create_time` , `hope_day` ,
`status`,`user_id`,`depart_id` ) VALUES ( '$taskId','$reason' ,now(),  '$hopeDay','0','" . logged_user()->getId() . "','$departId' ); ";
        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }

} // TaskController


?>