<?php

/**
 * Dashboard controller
 *
 * @author Ilija Studen <ilija.studen@gmail.com>, Marcos Saiz <marcos.saiz@fengoffice.com>
 */
class NewDashboardController extends ApplicationController {

	/**
	 * Construct controller and check if we have logged in user
	 *
	 * @param void
	 * @return null
	 */
	function __construct() {
		parent::__construct();
		prepare_company_website_controller($this, 'website');
		//$this->addHelper('calendar');
	} // __construct

	function init_overview() {
		require_javascript("og/OverviewManager.js");
		ajx_current("panel", "overview", null, null, true);
		ajx_replace(true);
	}
	
	/**
	 * Show dashboard index page
	 *
	 * @param void
	 * @return null
	 */
	function index() {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        // 查询当前的要处理的请假申请
        $sql = "select count(*) as count from og_holiday_apply as x where x.current_handler=" . logged_user()->getId().
            " and (apply_status = 2 or apply_status = 3 or apply_status = 4)";// 任务处于待审批的状态
        DB::beginWork();
        $rows = DB::executeAll($sql);
        tpl_assign('holidayApplyCount', $rows[0]['count']);
        tpl_assign('carApplyCount', 0);
        if (logged_user()->getUserRole() == '局长'){
            // 查询当前的要处理的请假申请
            $sql = "select count(*)  as count from og_project_task_delay_apply as x where x.status=0" ;
            DB::beginWork();
            $rows = DB::executeAll($sql);
            tpl_assign('taskDelayApplyCount', $rows[0]['count']);
        } else {
            tpl_assign('taskDelayApplyCount', 0);
        }

        // 查询最近一周到期的学习任务
         $sql = "SELECT count(*) as count
            FROM  `og_learning` AS y
            WHERE y.user_id =". logged_user()->getId()."
            and y.must_learn = 1 and status=2 and (DATEDIFF( due_date, NOW( ) ) <7 && DATEDIFF( due_date, NOW( ) ) >=0)
           ";
        DB::beginWork();
        $rows = DB::executeAll($sql);
        tpl_assign('learningCount', $rows[0]['count']);

        // 查询最近一周到期的风险点防控
        $sql = "SELECT COUNT( * ) as count , due_date
FROM  `og_risk_learning`
WHERE user_id =". logged_user()->getId()."
AND  status=2 and (DATEDIFF( due_date, NOW( ) ) <7 && DATEDIFF( due_date, NOW( ) ) >=0)";
        DB::beginWork();
        $rows = DB::executeAll($sql);
        tpl_assign('riskCount', $rows[0]['count']);

        // 查询待阅文件
        $sql = "SELECT COUNT( * ) as count
FROM  `og_file_reader`
WHERE to_user_id =". logged_user()->getId()."
AND  status=0";
        DB::beginWork();
        $rows = DB::executeAll($sql);
        tpl_assign('fileCount', $rows[0]['count']);
        // 查询最近要到期的任务
        // 如果是办公室的话，查询用车申请
        if (logged_user()->getUserRole() == '科长'){
            $sql = "SELECT y.depart_id,y.depart_name,y.manager_id
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.depart_id = z.depart_id
            AND z.id =" . logged_user()->getId();
            $rows1 = DB::executeAll($sql);
            $depart_id = $rows1[0]['depart_id'];

           $sql = "SELECT count(*) as count
            FROM og_project_tasks AS x, og_users AS z, og_department AS y
            WHERE x.assigned_to_departid = y.depart_id
            AND x.assigned_to_departid  = $depart_id
            AND z.id  = y.manager_id  and x.deleted = !1  and x.light_status=2 and (DATEDIFF( x.due_date, NOW( ) ) <7 && DATEDIFF( x.due_date, NOW( ) ) >=0) ";
            $rows = DB::executeAll($sql);
            tpl_assign('taskCount', $rows[0]['count']);

            if ($rows1[0]['depart_name'] == '办公室') {
                $sql = 'select count(*) as count from og_car_apply where status = 0';
                $row = DB::executeAll($sql);
                tpl_assign('carApplyCount', $row[0]['count']);
            }


        } else {
            tpl_assign('taskCount', 0);
        }


        $sql = "SELECT *
            FROM  `og_duty` as x where x.cur_date= '" . date('Y-m-d', time()) . "'";
        $rows = DB::executeAll($sql);
        if (count($rows) == 0) {
            tpl_assign('is_on_duty', 0);
        } else {
            if ($rows[0]['user_id'] == logged_user()->getId()) {
                tpl_assign('is_on_duty', 1);
            } else {
                tpl_assign('is_on_duty', 0);
            }
        }

	}


} // DashboardController

?>