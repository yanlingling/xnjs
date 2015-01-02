<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class LianzhengxuexiController extends ApplicationController
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
        $uid = isset($_GET['userid']) ? $_GET['userid'] : logged_user()->getId();
        if ($uid == logged_user()->getId()) {
            tpl_assign('selfView', true);
        } else {
            tpl_assign('selfView', false);
        }
        tpl_assign('role', logged_user()->getUserRole());
        tpl_assign('uid', $uid);

        $year = '2015';
        if (isset($_GET['year'])) {
            $year = $_GET['year'];
        }

        tpl_assign('year', $year);
        // 查询用户当前学习情况汇总
        DB::beginWork();

        $sql = "SELECT `status` , COUNT( `status` ) AS light_count, z.score, canCreateLearning, y.content_id
                FROM `og_learning` AS y, og_users AS z
                WHERE z.id =$uid
                AND y.user_id = z.id and y.must_learn = 1 and year(y.due_date) = $year
                GROUP BY STATUS
                ORDER BY STATUS ASC ";
        $rows = DB::executeAll($sql);
        tpl_assign('learning_overview_data', json_encode($rows));

        $sql = "SELECT z.score, canCreateLearning,depart_id
                FROM og_users AS z
                WHERE z.id =$uid";
        $rows = DB::executeAll($sql);
        tpl_assign('canCreateLearning', $rows[0]['canCreateLearning']);
        tpl_assign('personScore', $rows[0]['score']);
        tpl_assign('depart_id', $rows[0]['depart_id']);
        $canCreateLearning = $rows[0]['canCreateLearning'];
        /**
         * 查询当前userid的必学学习情况
         */
        $sql = "SELECT x.id as contentId,y.id as learningId,z.id as userId,name,status,supervise_status,y.due_date,
            y.complete_on as complete_on,z.depart_id as depart_id,time_long,x.create_time
            FROM  `og_learning_content` AS x,  `og_learning` AS y, og_users AS z
            WHERE z.id =" . $uid . "
            AND y.user_id =z.id
            AND x.id = y.content_id
            and y.must_learn = 1 and year(y.due_date) = $year
            order by y.status DESC,y.due_date ASC";

        $rows = DB::executeAll($sql);
       // DB::commit();
        tpl_assign('learning_list', $rows);


        /**
         * 查询当前userid的选学学习情况
         */
        $sql = "SELECT x.id as contentId,y.id as learningId,z.id as userId,name,status,supervise_status,y.due_date,
            y.complete_on as complete_on,z.depart_id as depart_id,time_long,x.create_time
            FROM  `og_learning_content` AS x,  `og_learning` AS y, og_users AS z
            WHERE z.id =" . $uid . "
            AND y.user_id =z.id
            AND x.id = y.content_id
            and y.must_learn = 0 and year(y.due_date) = $year
            order by y.status DESC,y.due_date ASC";

        $rows = DB::executeAll($sql);
        // DB::commit();
        tpl_assign('optional_learning_list', $rows);


        /**
         * 查询学习内容
         */
        if ($canCreateLearning) {

            $sql = "SELECT x.id as contentId,y.id as learningId,z.id as userId,name,status,supervise_status,y.due_date,
            y.complete_on as complete_on,z.depart_id as depart_id,z.username as user_name
            FROM  `og_learning_content` AS x,  `og_learning` AS y, og_users AS z
            WHERE y.user_id =z.id
            AND x.id = y.content_id
            and y.supervise_status!=0 and year(y.due_date) = $year
            order by y.supervise_status DESC,y.due_date ASC";

            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('supervise_list', $rows);
            // 查学习内容数据，是学习内容本身
            $sql = "SELECT *
            FROM  `og_learning_content` AS x where year(x.due_date) = $year

            order by x.due_date ASC";

            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('learning_content_list', $rows);
        }

    }

    /**
     * 局长的首页
     */
    function index_of_juzhang()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        // 查询岗位职责列表
        DB::beginWork();
        // 局长查询科室的汇总
        $sql = "SELECT y.depart_id,y.depart_name, round(avg(x.score)) as score,MAX( z.status ) AS status
            FROM " . TABLE_PREFIX . "users AS x, "
            . TABLE_PREFIX . "department AS y,og_learning as z
            WHERE x.`depart_id` = y.depart_id and z.user_id=x.id and z.must_learn=1
            GROUP BY y.depart_id";

        /* $sql = "SELECT y.depart_id,y.depart_name, y.score, MAX( x.light_status ) AS light_status,count(id)
             FROM " . TABLE_PREFIX . "project_tasks AS x, "
             . TABLE_PREFIX . "department AS y, "
             . TABLE_PREFIX . "og_project_task_delay_apply AS z
             WHERE x.`assigned_to_departid` = y.depart_id and z.depart_id=x.id and z.status=0
             GROUP BY x.`assigned_to_departid`";*/
        // 副局长只能看到分管科室的
        if (logged_user()->getUserRole() == '副局长') {
            $sql = "SELECT y.depart_id,y.depart_name,  round(avg(x.score)) as score,MAX( z.status ) AS status
            FROM " . TABLE_PREFIX . "users AS x, "
                . TABLE_PREFIX . "department AS y,og_learning as z
            WHERE x.`depart_id` = y.depart_id and z.user_id=x.id and y.fujuzhang_id=" . logged_user()->getId() . " and z.must_learn=1
            GROUP BY y.depart_id";

        }
        $rows = DB::executeAll($sql);
        DB::commit();

        tpl_assign('group_learning_list', $rows);
    }

    /**
     * 按科室查看的页面
     */
    function index_of_depart()
    {
        //  查询当前部门名称
        DB::beginWork();
        // 指定科室的时候，查询指定科室的，否则查询当前用户科室的
        if (isset($_GET['depart_id'])) {
            $sql = "SELECT y.depart_id,y.depart_name
          FROM  " . TABLE_PREFIX . "department AS y
          WHERE y.depart_id=" . $_GET['depart_id'];
        } else {
            $sql = "SELECT y.depart_id,y.depart_name
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.depart_id = z.depart_id
            AND z.id =" . logged_user()->getId();
        }

        $rows1 = DB::executeAll($sql);
        DB::commit();
        tpl_assign('departInfo', $rows1);
        $depart_id = $rows1[0]['depart_id'];
        $depart_name = $rows1[0]['depart_name'];
        $sql = "SELECT x.id as id,x.username as name,x.score as score,MAX( z.status ) AS status
            FROM " . TABLE_PREFIX . "users AS x, og_learning as z
            WHERE x.`depart_id` = " . $depart_id . " and z.user_id=x.id and z.must_learn=1
            GROUP BY x.id";
        $rows = DB::executeAll($sql);
        DB::commit();
        tpl_assign('personInfo', $rows);
    }

    /**
     * 添加学习内容
     */
    function add_learning()
    {


        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $due_date = $_POST['dueDate'];
        $id = time();
        // 新建处理请求
        if (isset($_GET['opt']) && $_GET['opt'] == 'add') {

            if ($_GET['type'] == 0) {
                $sql = "INSERT INTO `og_learning_content`
               (`id`,`name`, `content_type`, `content`, `location`, `due_date`,`create_time`,`must_learn`)
               VALUES ($id,'" . $_POST['name'] . "', 0, '" .
                    addslashes($_POST['content'])  . "', '', '" .
                    date("Y-m-d H:i:s", strtotime("$due_date +1   day") - 1) . "',now(),".$_POST['mustLearn'].")";
            } else {
                // $due_date = $_POST['learning-date-picker'];

                if (!file_exists("upload/" . $_POST["vedio-name-input"])) {
                    ajx_error(20, "file not exit");
                    return;
                }

                $sql = "INSERT INTO `og_learning_content`
               (`id`,`name`, `content_type`, `content`, `location`, `due_date`,`create_time`,`must_learn`)
                 VALUES ($id,'" . $_POST['name'] . "', 1, '', '" . $_POST["vedio"] . "', '"
                    . date("Y-m-d H:i:s", strtotime("$due_date +1   day") - 1) . "',now(),".$_POST['mustLearn'].")";
            }
            DB::beginWork();
            $rows = DB::executeAll($sql);
            $sql2 = 'select * from og_users';
            $users = DB::executeAll($sql2);
            // 给每个用户分发学习任务
            foreach ($users as $item) {
                $sql3 = "insert into og_learning (user_id,content_id,status,due_date,`must_learn`) values("
                    . $item['id'] . ",$id,2,'" . date("Y-m-d H:i:s", strtotime("$due_date +1   day") - 1) . "',".$_POST['mustLearn'].")";
                DB::executeAll($sql3);
            }
            DB::commit();
            ajx_current("empty");
            // 编辑
        } else if (isset($_GET['opt']) && $_GET['opt'] == 'save') {
            $id=$_POST['id'];
            $due_date = date("Y-m-d H:i:s", strtotime("$due_date +1   day") - 1);
            if ($_GET['type'] == 0) {
                $sql = "update `og_learning_content` set `name` ='"
                    .$_POST['name']."',`content` = '".addslashes($_POST['content'])."',due_date='"
                    . $due_date . "',must_learn="
                    . $_POST['mustLearn'] . "   where id=$id";
            } else {
                // $due_date = $_POST['learning-date-picker'];

                if (!file_exists("upload/" . $_POST["vedio-name-input"])) {
                    ajx_error(20, "file not exit");
                    return;
                }

                $sql = "update `og_learning_content` set `name` ='"
                    .$_POST['name']."' ,`location` = '".$_POST['vedio']."',due_date='"
                    . $due_date . "',must_learn="
                    . $_POST['mustLearn'] . "    where id=$id";
            }
            $sql2 = "update `og_learning` set due_date= '$due_date',must_learn=" . $_POST['mustLearn'] . " where content_id=$id";
            DB::beginWork();
            DB::executeAll($sql);
            DB::executeAll($sql2);
            DB::commit();
            ajx_current("empty");
            // 编辑
        } else if (isset($_GET['opt']) && $_GET['opt'] == 'edit') {
            $id = get_id();
            $sql = "select * from og_learning_content where id=$id";
            DB::beginWork();
            $rows = DB::executeAll($sql);
            DB::commit();
            tpl_assign('content_info', $rows[0]);
        } else {
            // echo $_GET['type'];
            $content_type = 1;
            if ($_GET['type'] == 'text') {
                $content_type = 0;
            }
            tpl_assign('type', $content_type);
        }
    }

    function to_learning()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        tpl_assign('opt', $_GET['opt']);

        DB::beginWork();

        $sql = "SELECT  *
            FROM  `og_learning_content` AS x
            WHERE x.id =" . $_GET['contentId'];

        $rows = DB::executeAll($sql);
        // 查询相关的学习体会
        $sql2 = "SELECT  x.comment,x.create_time,y.username
            FROM  `og_learning_comment` AS x,`og_users` AS y
            WHERE x.content_id =" . $_GET['contentId']." and x.user_id = y.id";

        $rows2 = DB::executeAll($sql2);
        DB::commit();
        tpl_assign('contentInfo', $rows[0]);
        tpl_assign('commentList', $rows2);
        tpl_assign('learnId', $_GET['learnId']);
    }

    function complete_learning()
    {
        $id = $_GET['id'];
        DB::beginWork();
        $sql = "UPDATE  `og_learning` SET  `status` =  '1',complete_on =now(),  `time_long`=".$_POST['timeLong']."";
        $randNum = rand(8, 8);
        //$randNum = rand(1, 70);
        // 需要督察
        if ($randNum == 8) {
            $sql .= ",supervise_status = 1 ";
        }
        $sql .= "WHERE  `og_learning`.`id` =$id;";
        $rows = DB::executeAll($sql);
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
        $sql = "update `" . TABLE_PREFIX . "learning` set
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
        $user_id = $_POST['user_id'];
        DB::beginWork();
        $sql = "update `" . TABLE_PREFIX . "learning` set
       supervise_status=3 where id=$id";
        //echo $sql;
        DB::execute($sql);
        $MINUS_SCORE = 4;
        $sql = "UPDATE  og_users
             SET score =score-$MINUS_SCORE  WHERE  id = $user_id";
        $sql2 = "INSERT INTO og_person_score_detail  ( `user_id`, `minus`, `learning_id`, `minus_time`, `type`) VALUES ($user_id ,$MINUS_SCORE ,$id, NOW(),'reject');";
        DB::execute($sql);
        DB::execute($sql2);

        $dayDiff = (strtotime(date('Y-m-d', time())) - strtotime($due_date)) / 86400;
        // 已经到期的任务，
        if ($dayDiff > 0) {
            $nowDate = date("Y-m-d", time());
            // 再给7天的时间完成任务，黄灯，但是不扣分
            $due_date1 = date("Y-m-d H:i:s", strtotime("$nowDate   +7   day") - 1);

            $sql = "update `" . TABLE_PREFIX . "learning` set due_date ='$due_date1',status=2
           where id = $id";
        } else {
            $sql = "update `" . TABLE_PREFIX . "learning` set
            status=2 where id=$id";
        }

        DB::execute($sql);
        $sql2 = " INSERT INTO og_user_score_detail  ( `user_id`, `minus`, `content_id`, `minus_time`, `type`, `content_type`)
			    VALUES (".logged_user()->getId()." , $MINUS_SCORE,$id, NOW(),'reject','1')";
        DB::execute($sql2);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 删除学习内容
     */
    function delete_learning_content()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $id = get_id();
        DB::beginWork();
        // 删除学习内容
        $sql = "delete FROM `og_learning_content` WHERE id=$id";
        // 删除关联的学习任务
        $sql2 = "delete FROM `og_learning` WHERE content_id=$id";
        DB::execute($sql);
        DB::execute($sql2);
        DB::commit();
        ajx_current("empty");
    }

    function publish_comment(){
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        ajx_current("empty");
        $id = get_id();
        DB::beginWork();
        $sql = " INSERT INTO og_learning_comment  ( `content_id`, `user_id`, `comment`, `create_time`)
			    VALUES (".$id." , ".logged_user()->getId().",'".addslashes($_POST['content'])."', NOW())";
        DB::execute($sql);
        DB::commit();
        ajx_current("empty");
    }


} // TaskController


?>