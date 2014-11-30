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
        if ($this-> depart_id == $this->YAOXIE2KE_ID) {
            tpl_assign('isYaoxie2', true);
        }
        $tab = 'xuke';
        if (isset($_GET['tab'])) {
            $tab = $_GET['tab'];
        }
        tpl_assign('tab', $tab);

    }
    function  add_xuke() {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $apply_date = $_POST['date'];
        // 处理请求
        if (isset($_GET['opt']) && $_GET['opt'] == 'add') {

            $sql = "INSERT INTO `" . TABLE_PREFIX . "dianzixiaoneng` (
           `apply_name`,`apply_area`,  `apply_time`, `apply_type`, `apply_detail`,
           `create_time`,process) VALUES (
           '" . $_POST['name'] . "', '" . $_POST['area'] . "','" . date("Y-m-d H:i:s", strtotime("$apply_date +1   day") - 1)  . "',
           '" . $_POST['type'] . "','" . $_POST['detail'] . "',now(),1);";
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


} // TaskController


?>