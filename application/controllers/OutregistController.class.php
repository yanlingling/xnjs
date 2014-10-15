<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class OutregistController extends ApplicationController
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

        $sql = "SELECT id, x.depart_id, x.order as user_order,username, depart_name, manager_id, fujuzhang_id, (
manager_id = id
) AS is_kezhang, kaoqin_status
FROM og_users AS x, og_department AS y
WHERE x.depart_id = y.depart_id
ORDER BY x.depart_id DESC, is_kezhang  DESC, user_order  DESC  ";
        DB::beginWork();
        $rows = DB::executeAll($sql);
        // 设置可操作的权限
        $i=0;
        foreach ($rows as $item) {
            if($item['id'] == logged_user()->getId()){
                $rows[$i]['isSelf'] = 'y';
            }
            if ($rows[$i]['isSelf'] == 'y') {
                $rows[$i]['optable'] = 'y';
            } else {
                /*            echo '--'.logged_user()->getUserRole() ;
            echo '...'.in_array(logged_user()->getId(),explode(',',$item['fujuzhang_id']));
            print_r(explode(',',$item['fujuzhang_id']));*/
                if (logged_user()->getUserRole() == '科长' &&$item['id'] != logged_user()->getId()&& $item['manager_id'] == logged_user()->getId()){
                    $rows[$i]['optable'] = 'y';
                }
                // 分管局长可能是两个人
                else if(logged_user()->getUserRole() == '副局长'
                    && $item['id'] != logged_user()->getId()
                    && in_array(logged_user()->getId(),explode(',',$item['fujuzhang_id'])) ){
                    $rows[$i]['optable'] = 'y';
                }
                // 局长可以设置任何人的状态
                else if(logged_user()->getUserRole() == '局长'){
                    $rows[$i]['optable'] = 'y';
                }
            }

            $i++;
        }
        tpl_assign('allUserInfo', $rows);
        tpl_assign('userRole', logged_user()->getUserRole());
        DB::commit();
    }
    function change_status(){
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $sql = "update og_users set kaoqin_status=".$_GET['status']." where id=".$_GET['id'];
        $sql2 = "insert into og_kaoqin_status_log
        (user_id,opt_user_id,from_status,to_status,opt_time)
         values(".$_GET['id'].",".logged_user()->getId().",".$_GET['formerStatus'].",".$_GET['status'].",now())";
        DB::beginWork();
        DB::executeAll($sql);
        DB::executeAll($sql2);
        DB::commit();
        ajx_current("empty");
    }

    function get_newstatus(){

        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $sql = "select id,kaoqin_status from og_users";
        DB::beginWork();
        $row=DB::executeAll($sql);
        $i=0;
        foreach ($row as $item) {
            if($item['id'] == logged_user()->getId()){
                $row[$i]['isSelf'] = 'y';
            }
            $i++;

        }
        DB::commit();
        ajx_current("empty");
        ajx_extra_data($row);
    }
    function view_data(){
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        DB::beginWork();
        $sql = "SELECT user_id, username, to_status, COUNT( to_status ) AS opt_count
FROM  `og_kaoqin_status_log` AS x, og_users AS y
WHERE x.user_id = y.id AND to_status !=8
GROUP BY user_id, to_status";
        $rows = DB::executeAll($sql);
        $res1 = array(0);
        $i = 0;
        $last_id = 0;
        foreach ($rows as $item) {
            // 一个新用户
/*            echo '---'.$last_id;
            echo '---'.$item['user_id'];
            print_r($item);
            echo '</br>';*/
            if ($item['user_id'] != $last_id){
                $i++;
                $res1[$i]['id'] = $item['user_id'];
                $res1[$i]['name'] = $item['username'];
                $res1[$i][$item['to_status']] = $item['opt_count'];
                $last_id = $item['user_id'];
            }else {
                $res1[$i][$item['to_status']] = $item['opt_count'];
            }
        }
        tpl_assign('userData', $res1);
    }


} // TaskController


?>