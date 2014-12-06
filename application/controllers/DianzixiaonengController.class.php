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
        $baseSql = 'select x.id,x.`apply_name`,x.`apply_time`,
y.sub_process,y.create_time,y.dead_time,
y.complete_time,y.result, y.id as task_id
from og_dianzixiaoneng as x,og_dianzixiaoneng_task as y
where x.id=y.apply_id';
        if ($this->depart_name == '药械二科') {
            $sql = $baseSql.' and y.sub_process=1';
            DB::beginWork();
            $rows = DB::executeAll($sql);
            tpl_assign('xukeshouliList', $rows);
            // 验收
            $sql = $baseSql.' and (y.sub_process=5 or y.sub_process=6)';
            DB::beginWork();
            $rows = DB::executeAll($sql);
            tpl_assign('yanshouList', $rows);
            //发证
            $sql = $baseSql.' and (y.sub_process=7 or y.sub_process=8)';
            DB::beginWork();
            $rows = DB::executeAll($sql);
            tpl_assign('fazhengList', $rows);
        } else {
            $sql = $baseSql.' and (y.sub_process=2 or y.sub_process=3)';
            if ($this->depart_name == '流通监管一科') {
               $sql.=' and x.apply_area =0';
            } else if ($this->depart_name == '流通监管二科'){
                $sql.=' and x.apply_area =1';
            }
            DB::beginWork();
            $rows = DB::executeAll($sql);
            tpl_assign('xukeshouliList', $rows);
            // 验收阶段
            $sql = $baseSql.' and (y.sub_process=9)';
            if ($this->depart_name == '流通监管一科') {
                $sql.=' and x.apply_area =0';
            } else if ($this->depart_name == '流通监管二科'){
                $sql.=' and x.apply_area =1';
            }
            DB::beginWork();
            $rows = DB::executeAll($sql);
            tpl_assign('yanshouList', $rows);
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
           '" . $_POST['type'] . "','" . $_POST['detail'] . "',now(),1);";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            $today = date('Y-m-d', time());
            // 创建相应的待办事项 条件审核
            $dead_time = $this->getDeadTime(1);
            $sql = "INSERT INTO `" . TABLE_PREFIX . "dianzixiaoneng_task` (
           `sub_process`,  `apply_id`, `create_time`, `dead_time`) VALUES (
            1," . $id . ",now(),'" . $dead_time . "');";
            $rows = DB::executeAll($sql);
            // 创建相应的待办事项 现场指导
            $dead_time = $this->getDeadTime(2);
            $sql = "INSERT INTO `" . TABLE_PREFIX . "dianzixiaoneng_task` (
           `sub_process`,  `apply_id`, `create_time`, `dead_time`) VALUES (
            2," . $id . ",now(),'" . $dead_time . "');";
            $rows = DB::executeAll($sql);
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
            7 => 3,
            8 => 15,
            9=>7
        );
        $today = date('Y-m-d', time());
        return date("Y-m-d H:i:s", strtotime("$today +$map[$type]   day") - 1);
    }

} // TaskController


?>