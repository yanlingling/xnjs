<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class ZhibanzhangController extends ApplicationController
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

        $sql = "SELECT y.depart_id,y.depart_name,y.manager_id
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.depart_id = z.depart_id
            AND z.id =" . logged_user()->getId();

        $rows1 = DB::executeAll($sql);
        DB::commit();
        $depart_name = $rows1[0]['depart_name'];
        tpl_assign('departName', $depart_name);
        tpl_assign('userRole', logged_user()->getUserRole());
        //$_GET['date'] ||
        DB::beginWork();
        $sql = "SELECT x.id,cur_date,y.username,x.create_time,last_modify_time,x.isCommited
            FROM  `og_duty` as x ,og_users as y where x.user_id=y.id";
        /*        if (isset($_GET['date'])) {
                    $sql .= ' and date=' . $_GET['date'];
                }*/
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
        tpl_assign('dutyInfo', $rows);

        $sql = "SELECT *
            FROM  `og_duty` as x where x.cur_date= '" . date('Y-m-d', time()) . "'";
        $rows = DB::executeAll($sql);
        if (count($rows) == 0) {
            tpl_assign('duty_been_created', 0);
        } else {
            tpl_assign('duty_been_created', 1);
            tpl_assign('is_commit', $rows[0]['isCommited']);
            tpl_assign('duty_id', $rows[0]['id']);
            if ($rows[0]['user_id'] == logged_user()->getId()) {
                tpl_assign('is_on_duty', 1);
            } else {
                tpl_assign('is_on_duty', 0);
            }
        }
        $depart_tongji = $this::getMostClean('most_clean_department');
        tpl_assign('depart_tongji', array_sorts($depart_tongji, 'count', 'desc'));
        $floor_tongji = $this::getMostClean('most_clean_floor');
        tpl_assign('floor_tongji', array_sorts($floor_tongji, 'count', 'desc'));
        $chuqin_tongji = $this::getChuqin();
        tpl_assign('chuqin_tongji', array_sorts($chuqin_tongji, 'count4', 'desc'));
    }

    function  getChuqin()
    {
        // 计算卫生最佳科室
        $sql = "SELECT id,morning_absent1,morning_absent2,morning_absent3,morning_absent4,morning_absent5,
                          noon_absent1,noon_absent2,noon_absent3,noon_absent4,noon_absent5
            FROM  `og_duty`";

        $rows = DB::executeAll($sql);
        $res1 = array(0);
        $res2 = array(0);
        $res3 = array(0);
        $res4 = array(0);
        $res5 = array(0);
        foreach ($rows as $item) {
            // $reson1Users = array_merge(explode(',', $item['morning_absent1']),explode(',', $item['morning_absent1']));
            $reson1Users = explode(',', $item['morning_absent1'] . ',' . $item['noon_absent1']);
            foreach ($reson1Users as $item1 => $value1) {
                if (!isset($res1[$value1])) {
                    $res1[$value1] = 1;
                } else {
                    $res1[$value1] = $res1[$value1] + 1;
                }
            }
            $reson2Users = explode(',', $item['morning_absent2'] . ',' . $item['noon_absent2']);
            foreach ($reson2Users as $item1 => $value1) {
                if (!isset($res2[$value1])) {
                    $res2[$value1] = 1;
                } else {
                    $res2[$value1] = $res2[$value1] + 1;
                }
            }
            $reson3Users = explode(',', $item['morning_absent3'] . ',' . $item['noon_absent3']);
            foreach ($reson3Users as $item1 => $value1) {
                if (!isset($res3[$value1])) {
                    $res3[$value1] = 1;
                } else {
                    $res3[$value1] = $res3[$value1] + 1;
                }
            }
            $reson4Users = explode(',', $item['morning_absent4'] . ',' . $item['noon_absent4']);
            foreach ($reson4Users as $item1 => $value1) {
                if (!isset($res4[$value1])) {
                    $res4[$value1] = 1;
                } else {
                    $res4[$value1] = $res4[$value1] + 1;
                }
            }
            $reson5Users = explode(',', $item['morning_absent5'] . ',' . $item['noon_absent5']);
            foreach ($reson5Users as $item1 => $value1) {
                if (!isset($res5[$value1])) {
                    $res5[$value1] = 1;
                } else {
                    $res5[$value1] = $res5[$value1] + 1;
                }
            }
        }
        // 计算卫生最佳科室
        $sql = "SELECT id,username
            FROM  `og_users` where id!=1";
        $rows = DB::executeAll($sql);
        $i = 0;
        foreach ($rows as $item) {
            $item['count1'] = isset($res1[$item['id']]) ? $res1[$item['id']] : 0;
            $item['count2'] = isset($res2[$item['id']]) ? $res2[$item['id']] : 0;
            $item['count3'] = isset($res3[$item['id']]) ? $res3[$item['id']] : 0;
            $item['count4'] = isset($res4[$item['id']]) ? $res4[$item['id']] : 0;
            $item['count5'] = isset($res5[$item['id']]) ? $res5[$item['id']] : 0;
            $rows[$i++] = $item;
        }
        return $rows;
    }

    function getMostClean($type)
    {

        // 计算卫生最佳科室
        $sql = "SELECT id," . $type . "
            FROM  `og_duty`";

        $rows = DB::executeAll($sql);
        $res = array(0);
        foreach ($rows as $item) {
            $depart = explode(',', $item[$type]);
            foreach ($depart as $item1 => $value1) {
                if (!isset($res[$value1])) {
                    $res[$value1] = 1;
                } else {
                    $res[$value1] = $res[$value1] + 1;
                }
            }
        }
        // 计算卫生最佳科室
        if ($type == 'most_clean_department') {
            $sql = "SELECT depart_id,depart_name
            FROM  `og_department`";
            $rows = DB::executeAll($sql);
        } else {
            $rows = array(array('depart_id' => 2, 'depart_name' => '二层'),
                array('depart_id' => 3, 'depart_name' => '三层'),
                array('depart_id' => 4, 'depart_name' => '四层'),
                array('depart_id' => 5, 'depart_name' => '五层')
            );
        }

        $i = 0;

        foreach ($rows as $item) {
            $item['count'] = isset($res[$item['depart_id']]) ? $res[$item['depart_id']] : 0;
            $rows[$i++] = $item;
        }
        return $rows;
    }

    function  write_duty()
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
            $sql = "select * from og_duty where id=" . $_GET['id'];
            $rows = DB::executeAll($sql);
            $rows[0]['zuofeng_content'] = str_replace("\n", "&#13;&#10;", $rows[0]['zuofeng_content']);
            $rows[0]['advice_content'] = str_replace("\n", "&#13;&#10;", $rows[0]['advice_content']);
            $rows[0]['other_content'] = str_replace("\n", "&#13;&#10;", $rows[0]['other_content']);
            $rows[0]['safe_content'] = str_replace("\n", "&#13;&#10;", $rows[0]['safe_content']);
            $rows[0]['saving_content'] = str_replace("\n", "&#13;&#10;", $rows[0]['saving_content']);
            tpl_assign('duty_info', $rows[0]);
            tpl_assign('opt', $_GET['opt']);
            tpl_assign('id', $_GET['id']);
        }
    }

    function add_duty()
    {
        if (isset($_GET['opt']) && $_GET['opt'] == 'add') {

            $sql = "INSERT INTO `og_duty` (`morning_absent1`,`morning_absent2`,`morning_absent3`,`morning_absent4`,`morning_absent5`,
             `noon_absent1`,`noon_absent2`,`noon_absent3`,`noon_absent4`,`noon_absent5`," .
                " `most_clean_department`, `most_clean_floor`, `safe_content`," .
                " `saving_content`,`zuofeng_content`,`advice_content`,`other_content`, `cur_date`, `create_time`, `last_modify_time`,`user_id`,`isCommited`)
                VALUES ('" . $_POST['morningReason1'] . "', '" . $_POST['morningReason2'] .
                "', '" . $_POST['morningReason3'] . "', '" . $_POST['morningReason4'] . "','" . $_POST['morningReason5'] . "',
                   '" . $_POST['noonReason1'] . "', '" . $_POST['noonReason2'] . "','" . $_POST['noonReason3'] . "',
                   '" . $_POST['noonReason4'] . "','" . $_POST['noonReason5'] . "','" . $_POST['mostCleanDepart'] . "','" . $_POST['mostCleanFloor'] . "',
                   '" . $_POST['safeContent'] . "','" . $_POST['savingContent'] . "', '" . $_POST['zuofengContent'] .
                "','" . $_POST['adviceContent'] . "','" . $_POST['otherContent'] . "',curdate(),
                 now(), now()," . logged_user()->getId() . ",".$_POST['isCommit'].");";
            DB::beginWork();
            DB::executeAll($sql);
            DB::commit();
            ajx_current("empty");
        }
    }

    function edit_duty()
    {
        $sql = "update `og_duty` set
        morning_absent1='" . $_POST['morningReason1'] . "',
        morning_absent2='" . $_POST['morningReason2'] . "',
        morning_absent3='" . $_POST['morningReason3'] . "',
        morning_absent4='" . $_POST['morningReason4'] . "',
        morning_absent5='" . $_POST['morningReason5'] . "',
        noon_absent1='" . $_POST['noonReason1'] . "',
        noon_absent2='" . $_POST['noonReason2'] . "',
        noon_absent3='" . $_POST['noonReason3'] . "',
        noon_absent4='" . $_POST['noonReason4'] . "',
        noon_absent5='" . $_POST['noonReason5'] . "',
        most_clean_department='" . $_POST['mostCleanDepart'] . "',
        most_clean_floor='" . $_POST['mostCleanFloor'] . "',
        safe_content='" . $_POST['safeContent'] . "',
        saving_content ='" . $_POST['savingContent'] . "',
        zuofeng_content='" . $_POST['zuofengContent'] . "',
        other_content ='" . $_POST['otherContent'] . "',
        advice_content ='" . $_POST['adviceContent'] . "',
        isCommited = '" . $_POST['isCommit'] . "',
        last_modify_time = now()
        where id =" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }

} // TaskController


?>