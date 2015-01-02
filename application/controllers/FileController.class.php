<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class FileController extends ApplicationController
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
        $sql = "SELECT DISTINCT y.id as id, x.username AS from_user,z.id as file_id,z.name as file_name, y.create_time AS file_create_time, z.create_time
FROM  `og_users` AS x, og_file_reader AS y, og_file AS z
WHERE to_user_id =".logged_user()->getId()."
AND y.from_user_id = x.id AND z.id = y.file_id and z.type=1 and year(z.create_time)=$year
AND STATUS =0";
        if (isset($_POST['condition'])) {
            $sql .= ' and (z.name like "%' . $_POST['condition'] . '%")';
        }
        $sql.=' order by y.create_time desc';
        $rows = DB::executeAll($sql);
        tpl_assign('toReadInfo', $rows);


        // 查询所有文件信息
        $sql = "SELECT z.id,z.name,z.create_time,sum(x.status=0) as not_read_count
FROM og_file AS z,og_file_reader as x where x.file_id=z.id  and z.type=1 and year(z.create_time)=$year";
        if (isset($_POST['condition']) && $_POST['condition']!='') {
            $sql .= ' and (z.name like "%' . $_POST['condition'] . '%")';
        }
        $sql.= ' group by z.id';
        $sql.=' order by z.create_time desc';
        $rows = DB::executeAll($sql);
        tpl_assign('allFileInfo', $rows);

       // 查询已阅文件
        $sql = "SELECT DISTINCT z.id as file_id, y.id as id, x.username AS from_user,z.name as file_name, y.create_time AS file_create_time, z.create_time,y.handle_time
FROM  `og_users` AS x, og_file_reader AS y, og_file AS z
WHERE to_user_id =".logged_user()->getId()."
AND y.from_user_id = x.id AND z.id = y.file_id and year(z.create_time)=$year
AND STATUS =1 and z.type=1 and y.is_rehandler!=1";
        if (isset($_POST['condition']) && $_POST['condition']!='') {
            $sql .= ' and (z.name like "%' . $_POST['condition'] . '%")';
        }
        $sql.=' order by y.create_time desc';
        $rows = DB::executeAll($sql);
        tpl_assign('hasReadInfo', $rows);

        // 查询局发文件
        $sql = "SELECT z.id,z.name,z.create_time FROM og_file AS z where  z.type=2 and year(z.create_time)=$year";
        if (isset($_POST['condition']) && $_POST['condition']!='') {
            $sql .= ' and (z.name like "%' . $_POST['condition'] . '%")';
        }
        $sql.=' order by z.create_time desc';
        $rows = DB::executeAll($sql);
        tpl_assign('jufaFileInfo', $rows);

        $rows1 = DB::executeAll('select can_manage_file from og_users as x where x.id=' . logged_user()->getId());
        $canManageFile = $rows1[0]['can_manage_file'];
        tpl_assign('canManageFile', $canManageFile);
        if (isset($_POST['condition']) && $_POST['condition']!='') {
            tpl_assign('condition', $_POST['condition']);
        }
        tpl_assign('currentTabId', isset($_POST['currentTabId']) ?  $_POST['currentTabId']: 'to-read-tab');
    }

    function view_file () {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $fileId = $_GET['id'];
        DB::beginWork();
        $sql = "SELECT  *
FROM  og_file AS z
WHERE z.id =".$fileId;
        $rows = DB::executeAll($sql);
        tpl_assign('fileInfo', $rows[0]);
        tpl_assign('readId', $_GET['read_id']);
        // 查询已阅信息
        $sql2 = "select * from og_file_reader as x ,og_users as y where file_id=".$fileId
            ." and x.to_user_id=y.id  and status = 1 order by handle_time desc";
        $rows2 = DB::executeAll($sql2);
        tpl_assign('readInfo', $rows2);

        // 查询传阅信息
        $sql3 = "SELECT y.username AS from_user, z.username AS to_user, x.create_time
FROM  `og_file_reader` AS x, og_users AS y, og_users AS z
WHERE x.from_user_id = y.id
AND to_user_id = z.id
AND file_id =".$fileId;
        $rows3 = DB::executeAll($sql3);
        tpl_assign('passInfo', $rows3);

        tpl_assign('opt', $_GET['opt']);
        tpl_assign('type', $_GET['type']);
        $sql = "SELECT id, x.depart_id, x.order AS user_order, username, depart_name, manager_id, fujuzhang_id, (
manager_id = id
) AS is_kezhang, (
manager_id =0
) AS is_juzhang
FROM og_users AS x, og_department AS y
WHERE x.depart_id = y.depart_id
ORDER BY is_juzhang DESC , is_kezhang DESC , x.depart_id DESC , user_order DESC";
        $rows = DB::executeAll($sql);
        tpl_assign('userInfo', $rows);

    }


    function add_file()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $id = time();
        DB::beginWork();
        $sql = "SELECT id, x.depart_id, x.order AS user_order, username, depart_name, manager_id, fujuzhang_id, (
manager_id = id
) AS is_kezhang, (
manager_id =0
) AS is_juzhang
FROM og_users AS x, og_department AS y
WHERE x.depart_id = y.depart_id
ORDER BY is_juzhang DESC , is_kezhang DESC , x.depart_id DESC , user_order DESC";
        $rows = DB::executeAll($sql);
        tpl_assign('userInfo', $rows);
        // 新建处理请求
        if (isset($_GET['opt']) && $_GET['opt'] == 'add') {
            $sql = "INSERT INTO `og_file`
               (`id`,`name`, `content`, `create_time`, type)
               VALUES ($id,'" . $_POST['name'] . "','" .
                addslashes($_POST['content']) . "', now(),".$_POST['type'].")";
            DB::beginWork();
            DB::executeAll($sql);
            if ($_POST['type'] == 1) {
                $users = explode(',',$_POST['readers']);
                for ($i=0;$i<count($users);$i++) {
                    $sqlInsertReader = "INSERT INTO `og_file_reader`
               (`to_user_id`,`comment`, `status`, `from_user_id`,create_time,file_id)
               VALUES ($users[$i],'',0,".logged_user()->getId()." ,now(),".$id.")";
                    DB::executeAll($sqlInsertReader);
                }
            }
            if ($_POST['needSendMessage'] == 'true') {
                // 查询电话发送短信
                $sql = 'select id ,phone from og_users where id in('.$_POST['readers'].')';
                $row = DB::executeAll($sql);
                for ($i=0;$i<count($row);$i++) {
                    if ($row[$i]['phone'] != '') {
                        sendFileShortMessage($_POST['name'], $row[$i]['phone']);
                    }
                }
            }

            DB::commit();
            ajx_current("empty");
            // 编辑
        } else if (isset($_GET['opt']) && $_GET['opt'] == 'save') {
            $id = $_POST['id'];
            $sql = "update `og_file` set `name` ='"
                . $_POST['name'] . "',`content` = '" . addslashes($_POST['content']) . "'  where id=$id";

            DB::beginWork();
            DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
            // 编辑
        } else if (isset($_GET['opt']) && $_GET['opt'] == 'edit') {
            $id = get_id();
            $sql = "select * from og_file where id=$id";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('content_info', $rows[0]);
        } else {
            tpl_assign('fileOpt', 'new');
            tpl_assign('addType', $_GET['type']);
        }
    }

    /**
     * 文件设置为已阅
     */

    function  read_file () {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $fileId = $_GET['id'];
        $fileName= $_GET['name'];
        $readId = $_POST['readId'];
        $comment = $_POST['comment'];
        DB::beginWork();
        // 第一次处理，是别人传阅的
        if ($readId) {
            $sql = "update og_file_reader set comment='".$comment."',status=1,handle_time=now() where id =".$readId;
        } else { // 再处理，主动的.相当于自己给自己传阅了一次
            $sql = "INSERT INTO `og_file_reader`
               (`to_user_id`,`comment`, `status`, `from_user_id`,create_time,file_id,handle_time, is_rehandler)
               VALUES (".logged_user()->getId().",'$comment',1,".logged_user()->getId()." ,now(),".$fileId.",now(),1)";
        }
        $rows = DB::executeAll($sql);
        if ($_POST['newReaders'] != '') {
            $users = explode(',',$_POST['newReaders']);
            for ($i=0;$i<count($users);$i++) {
                $sqlInsertReader = "INSERT INTO `og_file_reader`
               (`to_user_id`,`comment`, `status`, `from_user_id`,create_time,file_id)
               VALUES ($users[$i],'',0,".logged_user()->getId()." ,now(),".$fileId.")";
                DB::executeAll($sqlInsertReader);
            }
            // 查询电话发送短信
            $sql = 'select id ,phone from og_users where id in('.$_POST['newReaders'].')';
            $row = DB::executeAll($sql);
            if ($_POST['needSendMessage'] == 'true') {

                for ($i=0;$i<count($row);$i++) {
                    if ($row[$i]['phone'] != '') {
                        sendFileShortMessage($fileName, $row[$i]['phone']);
                    }
                }
            }
        }

        DB::commit();
        ajx_current("empty");
    }

    /**
     * 删除文件
     */

    function  del_file () {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $fileId = $_GET['id'];
        DB::beginWork();
        $sql = "delete from og_file where id=".$fileId;
        $rows = DB::executeAll($sql);
        $sql = "delete from og_file_reader where file_id=".$fileId;
        $rows = DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }

} // TaskController



?>