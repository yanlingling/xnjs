<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class Newtask3Controller extends ApplicationController
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
    function content()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        // 指定科室的时候，查询指定科室的，否则查询当前用户科室的
        if (isset($_GET['depart_id'])) {
            $sql = "SELECT y.depart_id,y.depart_name,y.depart_task
          FROM  " . TABLE_PREFIX . "department AS y
          WHERE y.depart_id=" . $_GET['depart_id'];
        } else {
            $sql = "SELECT y.depart_id,y.depart_name,y.depart_task
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.manager_id = z.id
            AND z.id =" . logged_user()->getId();
        }

        $rows= DB::executeAll($sql);
        DB::commit();
      //  print_r($rows);
        tpl_assign('depart_info', $rows);

    }

    function add_depart_task()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        // 处理请求
        if (isset($_GET['opt']) && $_GET['opt'] == 'add') {

            $sql = "update og_department set depart_task = '" . $_POST['content'] . "'
            where depart_id=" . $_POST['depart'];
            //echo $sql;
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
        } else {
            DB::beginWork();
            $sql = "SELECT depart_id,depart_name FROM " . TABLE_PREFIX . "department";
            $rows = DB::executeAll($sql);
            DB::commit();
           // echo json_encode($rows);
            tpl_assign('depart_list', json_encode($rows));
        }

    }

} // TaskController


?>