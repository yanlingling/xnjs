<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class KaoqinController extends ApplicationController
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
        // $userRole = logged_user()->getUserRole();

        DB::beginWork();
        $sql = "SELECT z.id,z.name,z.create_time FROM og_kaoqinducha_file AS z ";
        if (isset($_POST['condition']) && $_POST['condition'] != '') {
            $sql .= ' and (z.name like "%' . $_POST['condition'] . '%")';
        }
        $sql .= ' order by z.create_time desc';
        $rows = DB::executeAll($sql);
        tpl_assign('kaoqinduchaFileInfo', $rows);

        $rows1 = DB::executeAll('select canManageKaoqinducha,canManageJilvjiancha from og_users as x where x.id=' . logged_user()->getId());
        $canManageKaoqinducha = $rows1[0]['canManageKaoqinducha'];
        tpl_assign('canManageKaoqinducha', $canManageKaoqinducha);
        tpl_assign('canManageJilvjiancha', $rows1[0]['canManageJilvjiancha']);
        if (isset($_POST['condition']) && $_POST['condition'] != '') {
            tpl_assign('condition', $_POST['condition']);
        }


        //纪律检查列表查询

        $sql = "SELECT x.id,jiancha_time,x.onDutyUser,y.username,x.create_time
            FROM  `og_jilvjiancha` as x ,og_users as y where x.user_id=y.id";
        if (isset($_POST['condition'])) {
            if (preg_match("/(\d+)-(\d+)-(\d+)/", $_POST['condition'])) {
                $sql .= ' and (cur_date="' . $_POST['condition'] . '")';
            } else {
                $sql .= ' and (y.username="' . $_POST['condition'] . '")';
            }
            tpl_assign('condition', $_POST['condition']);
        } else { // 没有查询条件只显示前30
            $sql .= ' order by cur_date desc limit 0,30';
        }

        // order by depart_id";

        $rows = DB::executeAll($sql);
        tpl_assign('jilvInfo', $rows);
        $tab = 'kaoqin';
        if (isset($_GET['tab'])) {
            $tab = $_GET['tab'];
        }
        tpl_assign('tab', $tab);
    }


    function view_kaoqinducha()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $fileId = $_GET['id'];
        DB::beginWork();
        $sql = "SELECT  *
FROM  og_kaoqinducha_file AS z
WHERE z.id =" . $fileId;
        $rows = DB::executeAll($sql);
        tpl_assign('fileInfo', $rows[0]);
    }

    function add_kaoqinducha()
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
            $sql = "INSERT INTO `og_kaoqinducha_file`
               (`id`,`name`, `content`, `create_time`)
               VALUES ($id,'" . $_POST['name'] . "','" .
                addslashes($_POST['content']) . "', now())";
            DB::beginWork();
            DB::executeAll($sql);

            DB::commit();
            ajx_current("empty");
            // 编辑
        } else {
            tpl_assign('fileOpt', 'new');
            tpl_assign('addType', $_GET['type']);
        }
    }

    /**
     * 删除文件
     */

    function  del_kaoqinducha()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $fileId = $_GET['id'];
        DB::beginWork();
        $sql = "delete from og_kaoqinducha_file where id=" . $fileId;
        $rows = DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }

    function write_jilvjiancha()
    {

        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        DB::beginWork();
        $sql = "SELECT id,username,depart_id
            FROM  `og_users` where depart_id!='0'
            order by depart_id";

        $rows = DB::executeAll($sql);
        // 查询科室，用于评选卫生最佳科室，局长室和效能办不用参加
        $sql2 = "SELECT *
FROM  `og_department` where depart_id!=1 and depart_id!=8 ";
        $rows2 = DB::executeAll($sql2);
        DB::commit();
        tpl_assign('userInfo', $rows);
        tpl_assign('departInfo', $rows2);
        if (isset($_GET['opt'])) {
            $sql = "select * from og_jilvjiancha where id=" . $_GET['id'];
            $rows = DB::executeAll($sql);
            $rows[0]['other_content'] = str_replace("\n", "&#13;&#10;", $rows[0]['other_content']);
            tpl_assign('jilv_info', $rows[0]);
            tpl_assign('opt', $_GET['opt']);
            tpl_assign('id', $_GET['id']);
        } else {

            tpl_assign('opt', 'add');
        }
    }
    function add_jilvjiancha()
    {
        if (isset($_GET['opt']) && $_GET['opt'] == 'add') {

            $sql = "INSERT INTO `og_jilvjiancha` (`work_status1`,`work_status2`,`work_status3`,`work_status4`,`work_status5`,".
                " `most_clean_department`,`least_clean_department`,  `most_clean_floor`,`least_clean_floor`," .
                " `other_content`, `jiancha_time`, `onDutyUser`,`create_time`, `last_modify_time`,`user_id`)
                VALUES ('" . $_POST['work_status1'] . "', '" . $_POST['work_status2'] .
                "', '" . $_POST['work_status3'] . "', '" . $_POST['work_status4'] . "','" . $_POST['work_status5'] . "',
                   '" . $_POST['mostCleanDepart'] . "','" . $_POST['leastCleanDepart'] . "','" . $_POST['mostCleanFloor'] . "','" . $_POST['leastCleanFloor'] . "',
                   '" . $_POST['otherContent'] . "','" . $_POST['jiancha_time'] . "','" . $_POST['jiancha_user'] . "',
                 now(), now()," . logged_user()->getId() . ");";
            DB::beginWork();
            DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
        }
    }

    function edit_jilvjiancha()
    {
        $sql = "update `og_jilvjiancha` set
        work_status1='" . $_POST['work_status1'] . "',
        work_status2='" . $_POST['work_status2'] . "',
        work_status3='" . $_POST['work_status3'] . "',
        work_status4='" . $_POST['work_status4'] . "',
        work_status5='" . $_POST['work_status5'] . "',
        most_clean_department='" . $_POST['mostCleanDepart'] . "',
        least_clean_department='" . $_POST['leastCleanDepart'] . "',
        most_clean_floor='" . $_POST['mostCleanFloor'] . "',
        least_clean_floor='" . $_POST['leastCleanFloor'] . "',
        other_content ='" . $_POST['otherContent'] . "',
        jiancha_time ='" . $_POST['jiancha_time'] . "',
        onDutyUser='" . $_POST['jiancha_user'] . "',
        last_modify_time = now()
        where id =" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }
    function  del_jilvjiancha()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $id = $_GET['id'];
        DB::beginWork();
        $sql = "delete from og_jilvjiancha where id=" . $id;
        $rows = DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }
} // TaskController
?>
