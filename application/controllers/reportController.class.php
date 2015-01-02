<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class ReportController extends ApplicationController
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
    function index()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        DB::beginWork();
        $year = '2015';
        if (isset($_GET['year'])) {
            $year = $_GET['year'];
        }
        tpl_assign('year', $year);
        // 查询待阅读文件信息
        $sql = "SELECT *
FROM  og_report AS z where year(z.create_time)=$year order by z.create_time ";
        $rows = DB::executeAll($sql);
        tpl_assign('toReadInfo', $rows);


        // 查询所有文件信息
/*        $sql = "SELECT z.id,z.name,z.create_time,sum(x.status=0) as not_read_count
FROM og_report AS z,og_report_reader as x where x.report_id=z.id group by z.id";
        $rows = DB::executeAll($sql);*/
        tpl_assign('allFileInfo', $rows)        ;

        $rows1 = DB::executeAll('select canCreateReport from og_users as x where x.id=' . logged_user()->getId());
        $canCreateReport= $rows1[0]['canCreateReport'];
        tpl_assign('canCreateReport', $canCreateReport);
    }

    function view_report () {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $reportId = $_GET['id'];
        DB::beginWork();
        $sql = "SELECT  *
FROM  og_report AS z
WHERE z.id =".$reportId;
        $rows = DB::executeAll($sql);
        tpl_assign('reportInfo', $rows[0]);
        tpl_assign('readId', $_GET['read_id']);
    }


    function add_report()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $id = time();
        DB::beginWork();
        // 新建处理请求
        if (isset($_GET['opt']) && $_GET['opt'] == 'add') {

            $sql = "INSERT INTO `og_report`
               (`id`,`name`, `content`, `create_time`)
               VALUES ($id,'" . $_POST['name'] . "','" .
                addslashes($_POST['content']) . "', now())";
            DB::beginWork();
            DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
            // 编辑
        } else if (isset($_GET['opt']) && $_GET['opt'] == 'save') {
            $id = $_POST['id'];
            $sql = "update `og_report` set `name` ='"
                . $_POST['name'] . "',`content` = '" . addslashes($_POST['content']) . "'  where id=$id";

            DB::beginWork();
            DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
            // 编辑
        } else if (isset($_GET['opt']) && $_GET['opt'] == 'edit') {
            $id = get_id();
            $sql = "select * from og_report where id=$id";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('content_info', $rows[0]);
        } else {
            tpl_assign('reportOpt', 'new');
        }
    }

    /**
     * 文件设置为已阅
     */

    function  read_report () {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $reportId = $_GET['id'];
        $reportName= $_GET['name'];
        $readId = $_POST['readId'];
        $comment = $_POST['comment'];
        DB::beginWork();
        $sql = "update og_report_reader set comment='".$comment."',status=1,handle_time=now() where id =".$readId;
        $rows = DB::executeAll($sql);
        if ($_POST['newReaders'] != '') {
            $users = explode(',',$_POST['newReaders']);
            for ($i=0;$i<count($users);$i++) {
                $sqlInsertReader = "INSERT INTO `og_report_reader`
               (`to_user_id`,`comment`, `status`, `from_user_id`,create_time,report_id)
               VALUES ($users[$i],'',0,".logged_user()->getId()." ,now(),".$reportId.")";
                DB::executeAll($sqlInsertReader);
            }
            // 查询电话发送短信
            $sql = 'select id ,phone from og_users where id in('.$_POST['newReaders'].')';
            $row = DB::executeAll($sql);
            for ($i=0;$i<count($row);$i++) {
                if ($row[$i]['phone'] != '') {
                    sendFileShortMessage($reportName, $row[$i]['phone']);
                }
            }
        }

        DB::commit();
        ajx_current("empty");
    }

    /**
     * 删除文件
     */

    function  del_report () {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $reportId = $_GET['id'];
        DB::beginWork();
        $sql = "delete from og_report where id=".$reportId;
        $rows = DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }

} // TaskController


?>