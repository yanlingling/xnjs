<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class CarmanageController extends ApplicationController
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
        $sql = "SELECT y.depart_id,y.depart_name,y.manager_id
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.depart_id = z.depart_id
            AND z.id =" . logged_user()->getId();

        $row = DB::executeAll($sql);
        $depart_id = $row[0]['depart_id'];
        $sql = "select * from og_car as y ,og_car_apply as x,og_department as z
                where x.depart_id=" . $depart_id . " and z.depart_id= x.depart_id and
        x.car_id = y.id  and year(x.create_time)=$year";

        $rows = DB::executeAll($sql);

        tpl_assign('carInfo', $rows);
        tpl_assign('userRole', logged_user()->getUserRole());

        $rows1 = DB::executeAll('select can_manage_car from og_users as x where x.id=' . logged_user()->getId());
        $canManageCar = $rows1[0]['can_manage_car'];
        tpl_assign('canManageCar', $canManageCar);
        if ($canManageCar || logged_user()->getUserRole() == '局长') {
            // 查所有的申请
            $sql = "select * from  og_department as y,og_car as z, og_car_apply as x
                where x.depart_id  =y.depart_id and z.id = x.car_id  and year(x.create_time)=$year order by x.create_time DESC";
            $rows = DB::executeAll($sql);
            tpl_assign('allCarApply', $rows);
            tpl_assign('userRole', logged_user()->getUserRole());
            if ($canManageCar) {
                $sql = "select * from  og_department as y,og_car_apply as x where x.depart_id  =y.depart_id  and x.status = 0 and year(x.create_time)=$year order by x.create_time DESC";
                $rows = DB::executeAll($sql);
                tpl_assign('toHandleApply', $rows);
            }

            // 科室用车数据统计
            $car_data = $this::getCarData($year);
            tpl_assign('carData', $car_data);

        } else {
            $a = array();
            tpl_assign('carData', array());
            tpl_assign('allCarApply', $a);
        }

        // $userRole = logged_user()->getUserRole();
        DB::commit();

    }

    function  getCarData($year) {
        $sql = "SELECT x.depart_id,depart_name, COUNT( * ) as count
FROM og_car_apply AS x, og_department AS y
WHERE x.depart_id = y.depart_id and x.status = 1 and year(x.create_time)=$year
GROUP BY depart_name";
        $row = DB::executeAll($sql);
        $i = 0;
        foreach ($row as $item) {
            $sql0 = "SELECT place
FROM  og_car_apply
WHERE  `depart_id` =" . $item['depart_id'] ." and status = 1";
            $row0 = DB::executeAll($sql0);
         $row[$i]['1'] = 0;
         $row[$i]['2'] = 0;
         $row[$i]['3'] = 0;
        foreach ($row0 as $item0) {
            $row[$i][$item0['place']] = $row[$i][$item0['place']] + 1;
        }
        $i++;
        }
        return $row;
    }

    function  create_car_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        if (isset($_GET['opt']) && ($_GET['opt'] == 'edit' || $_GET['opt'] == 'view')) {
            $sql = "
               select * from og_car_apply where id=
            " . $_GET['id'];
            DB::beginWork();
            $rows = DB::executeAll($sql);
            $rows[0]['reason'] = str_replace("\n", "&#13;&#10;", $rows[0]['reason']);
            DB::commit();
            tpl_assign('carApplyInfo', $rows[0]);
            tpl_assign('opt', $_GET['opt']);
        }
    }

    function handle_car_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        DB::beginWork();
        $sql = "select * from og_car_apply where id=
            " . $_GET['id'];
        $rows = DB::executeAll($sql);
        $rows[0]['reason'] = str_replace("\n", "&#13;&#10;", $rows[0]['reason']);
        $beginTime = $rows[0]['begin_time'];
        $endTime = $rows[0]['end_time'];
        DB::commit();
        tpl_assign('carApplyInfo', $rows[0]);
        // 查询空闲的车
        $sql = "select * from og_car where id!=0";
        $rows = DB::executeAll($sql);
        $new_rows = array(0);
        $i = 0;
        foreach ($rows as $item) {
            $car_id = $item['id'];
            // 这个时间段的车已经被分配出去，并且没有还回。
            $sql0 = "select * from og_car_apply where car_id="
                . $car_id . " and
                 ((begin_time>='" . $beginTime . "' and begin_time<='" . $endTime . "')
                 or
                 (end_time>='" . $beginTime . "' and end_time<='" . $endTime . "')
                 or
                 (begin_time<='" . $beginTime . "' and end_time>='" . $endTime . "')
                ) and car_returned!=1 and status=1";
            $rows0 = DB::executeAll($sql0);
            if (count($rows0) == 0) {
                $new_rows[$i++] = $item;
            }
        }
        tpl_assign('carInfo', $new_rows);
    }

    function add_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        DB::beginWork();
        $sql = "SELECT y.depart_id,y.depart_name,y.manager_id
            FROM " . TABLE_PREFIX . "users AS z, " . TABLE_PREFIX . "department AS y
            WHERE y.depart_id = z.depart_id
            AND z.id =" . logged_user()->getId();

        $row = DB::executeAll($sql);
        $depart_id = $row[0]['depart_id'];
        $sql = "insert into og_car_apply (car_users,depart_id,place,place_detail,reason,begin_time,end_time,create_time,car_returned) "
            . "values('" . $_POST['carUser'] . "',$depart_id," . $_POST['place'] . ",'" . $_POST['placeDetail'] . "','" . $_POST['reason'] .
            "','" . $_POST['beginDate'] . " " . $_POST['beginTime'] . "','" . $_POST['endDate'] . " " . $_POST['endTime'] . "',now(),0)";
        DB::executeAll($sql);
        DB::commit();
        $sql = 'select id ,phone from og_users where can_manage_car=1';
        $row1 = DB::executeAll($sql);
        sendApplyCarMessage($row[0]['depart_name'],
            $_POST['beginDate'] . " " . $_POST['beginTime'],
            $_POST['endDate'] . " " . $_POST['endTime'],
            $row1[0]['phone']);

        ajx_current("empty");
    }

    function edit_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $sql = "update og_car_apply set
         car_users='" . $_POST['carUser'] . "',place='" . $_POST['place'] . "',
         place_detail='" . $_POST['placeDetail'] . "',reason='" . $_POST['reason'] . "',
         begin_time='" . $_POST['beginDate'] . " " . $_POST['beginTime'] . "',end_time='" . $_POST['endDate'] . " " . $_POST['endTime'] . "'
         where id=" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }

    /**
     * 处理的提交
     */
    function submit_handle_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        // 同意
        $status = 1;
        if ($_POST['agree'] == '0') {
            $status = 2;
            $_POST['car'] = 0;
        }
        $sql = "update og_car_apply set
         status=" . $status . ",
         car_id=" . $_POST['car'] . "
         where id=" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);
        // 同意的话，查找司机的号码，如果有，发送短信
        if ($status == 1) {
            $sql = 'select * from og_car where id=' . $_POST['car'];
            $row = DB::executeAll($sql);
            if ($row[0]['driver_phone'] != '' && $row[0]['driver_phone'] != '无') {
                $sql1 = "select * from  og_department as y,og_car_apply as x " .
                    "where x.depart_id  =y.depart_id and x.id=" . $_GET['id'];
                $row1 = DB::executeAll($sql1);
                sendShortMessageToDriver($row1[0]['depart_name'], $row1[0]['begin_time'], $row1[0]['end_time'], $row[0]['car_number'], $row[0]['driver_phone']);
            }

            // 查找科长的电话，给科长发送批准通过的短信
            $depart_id = $row1[0]['depart_id'];
            $driver_name = $row[0]['driver_name'];
            $sql = 'select x.phone from og_users as x,og_department as y where y.depart_id=' . $depart_id .
                ' and x.id=y.manager_id ';
            $row3 = DB::executeAll($sql);
            sendShortMessageToKezhang($row1[0]['begin_time'],
                $row1[0]['end_time'], $row3[0]['phone'],
                $row[0]['car_number'], $row[0]['driver_name'],
                $row[0]['driver_phone']
            );
        }
        DB::commit();
        ajx_current("empty");
    }

    function  undo_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $sql = "update og_car_apply set
         status =4 where id=" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }

    function return_car()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $sql = "update og_car_apply set
         car_returned =1 where id=" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }
} // TaskController
?>