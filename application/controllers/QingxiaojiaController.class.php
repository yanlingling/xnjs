<?php

/**
 * Controller for handling task list and task related requests
 *
 * @version 1.0
 * @author Ilija Studen <ilija.studen@gmail.com>
 */
class QingxiaojiaController extends ApplicationController
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
		
		
       // $sql = "select * from og_holiday_apply as x where x.user_id=" . logged_user()->getId();
		
		$sql = "select hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,hd.reject_userid,hd.apply_begin_date,hd.apply_end_date,group_concat(u.title,'同意时间','<br/>',hda.approve_begin_time,'到',hda.approve_end_time,'<br/>') approve from (select * from og_holiday_apply as x where x.user_id=".logged_user()->getId().") hd left join og_holiday_approve as hda on hda.holiday_id=hd.id left join og_users u on hda.user_id=u.id group by hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,hd.reject_userid,hd.apply_begin_date,hd.apply_end_date
		 order by hd.create_time DESC";
		
        DB::beginWork();
        $rows = DB::executeAll($sql);
        tpl_assign('holidayInfo', $rows);
        tpl_assign('userRole', logged_user()->getUserRole());

        $rows1 = DB::executeAll('select canViewAllHolidayApply from og_users as x where x.id=' . logged_user()->getId());
        $canViewAllHolidayApply = $rows1[0]['canViewAllHolidayApply'];
        tpl_assign('canViewAllHolidayApply', $canViewAllHolidayApply);
        if ($canViewAllHolidayApply) {
            // 查所有的休假申请
           // $sql = "select x.id,user_id,reason,username,begin_date,end_date,apply_begin_date,apply_end_date,x.create_time,detail,apply_status from  og_users as y,og_holiday_apply as x where x.user_id=y.id order by x.create_time DESC";
           
		  $sql = "select 
			hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,
			hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,
			hd.reject_userid,hd.apply_begin_date,hd.apply_end_date,hd.username,
			group_concat(u.title,'同意时间','<br/>',hda.approve_begin_time,'到',hda.approve_end_time,'<br/>')
			approve from
			(select x.*,y.username from  og_users as y,og_holiday_apply as x where x.user_id=y.id) hd left join 
			og_holiday_approve as hda on hda.holiday_id=hd.id left join og_users u on 
			hda.user_id=u.id group by 
			hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,
			hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,
			hd.reject_userid,hd.apply_begin_date,hd.apply_end_date,hd.username order by hd.create_time DESC";
			
			$rows = DB::executeAll($sql);
            tpl_assign('allHolidayApply', $rows);

            $sql = "select x.id,user_id,reason,username,begin_date,end_date,x.create_time,".
                "detail,apply_status"
                ." from  og_users as y,og_holiday_apply as x where x.user_id=y.id and x.apply_status=1 order by x.create_time DESC";
            $rows = DB::executeAll($sql);
            $res = array();
            foreach ($rows as $item) {
                if (!isset($res[$item['user_id']])) {
                    $res[$item['user_id']] = array();
                    $res[$item['user_id']][$item['reason']] = get_work_days($item['begin_date'],$item['end_date']);
                    $res[$item['user_id']]['username'] = $item['username'];
                } else {
                    if (!isset($res[$item['user_id']])) {
                        $res[$item['user_id']][$item['reason']] =get_work_days($item['begin_date'],$item['end_date']);
                    } else {
                        $res[$item['user_id']][$item['reason']] += get_work_days($item['begin_date'],$item['end_date']);
                    }
                }
            }
            tpl_assign('tongjiData', $res);
        }else {
            $a =  array();
            tpl_assign('allHolidayApply', $a);
            tpl_assign('tongjiData', $a);
        }


        // 查询待处理的申请
        // apply_status 0为被用户撤回，5 6 7为已经拒绝
        if (logged_user()->getUserRole() != '科员') {
           /* $sql = "select * from og_users as y,og_holiday_apply as x
             where x.current_handler=" . logged_user()->getId() . " and x.user_id= y.id

             and x.apply_status!='0'
             and x.apply_status!='1'
             and x.apply_status!='5'
             and x.apply_status!='6'
             and x.apply_status!='7'
             ";*/
			 $sql = "SELECT * FROM `og_users` WHERE id=".logged_user()->getId();
            $rows = DB::executeAll($sql);
			 if(logged_user()->getUserRole() == '科长' && $rows[0]['depart_id'] == '2'){
				$sql = "select 
				hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,
				hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,
				hd.reject_userid,hd.apply_begin_date,hd.apply_end_date,hd.username,
				group_concat(u.title,'同意时间','<br/>',hda.approve_begin_time,'到',hda.approve_end_time,'<br/>')
				approve from
				(select x.*,y.username from og_users as y,og_holiday_apply as x
					where x.current_handler=".logged_user()->getId()." and x.user_id= y.id
					and x.apply_status!='0'
					and x.apply_status!='1'
					and x.apply_status!='5'
					and x.apply_status!='6'
					and x.apply_status!='7'
					and x.apply_status!='9'
					UNION ALL
					select x.*,y.username from og_users as y,og_holiday_apply as x
					 where x.user_id= y.id
					 and x.apply_status='8') hd left join 
				og_holiday_approve as hda on hda.holiday_id=hd.id left join og_users u on 
				hda.user_id=u.id group by 
				hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,
				hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,
				hd.reject_userid,hd.apply_begin_date,hd.apply_end_date,hd.username 	 order by hd.create_time DESC ;"  ;
			 }else{
				$sql = "select 
				hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,
				hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,
				hd.reject_userid,hd.apply_begin_date,hd.apply_end_date,hd.username,
				group_concat(u.title,'同意时间','<br/>',hda.approve_begin_time,'到',hda.approve_end_time,'<br/>')
				approve from
				(select x.*,y.username from og_users as y,og_holiday_apply as x
							 where x.current_handler=".logged_user()->getId()." and x.user_id= y.id
							 and x.apply_status!='0'
							 and x.apply_status!='1'
							 and x.apply_status!='5'
							 and x.apply_status!='6'
							 and x.apply_status!='7'
							 and x.apply_status!='9'
							 and x.apply_status!='8') hd left join
				og_holiday_approve as hda on hda.holiday_id=hd.id left join og_users u on 
				hda.user_id=u.id group by 
				hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,
				hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,
				hd.reject_userid,hd.apply_begin_date,hd.apply_end_date,hd.username 	 order by hd.create_time DESC;";
			 }
			
            $rows = DB::executeAll($sql);
            tpl_assign('toHandleApply', $rows);
        }
        // $userRole = logged_user()->getUserRole();
        DB::commit();
        $tab = 'my';
        if (isset($_GET['tab'])) {
            $tab = $_GET['tab'];
        }
        tpl_assign('tab', $tab);

    }

    function write_holiday_page()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        if (isset($_GET['opt']) && ($_GET['opt'] == 'edit' || $_GET['opt'] == 'view' || $_GET['opt'] == 'handle')) {
           $sql = " select * from og_holiday_apply where id=
            " . $_GET['id'];
            // 查询审批记录
            $sql2 = " select * from og_holiday_approve as x,og_users as y where holiday_id=
            " . $_GET['id']." and x.user_id = y.id";
			/* $sql = "select
				hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,
				hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,
				hd.reject_userid,hd.apply_begin_date,hd.apply_end_date,
				group_concat(u.username,' 同意时间','：',hda.approve_begin_time,'到',hda.approve_end_time,'<br/>')
				approve from
				(select * from og_holiday_apply where id=".$_GET['id'].") hd left join 
				og_holiday_approve as hda on hda.holiday_id=hd.id left join og_users u on 
				hda.user_id=u.id group by 
				hd.id,hd.reason,hd.detail,hd.begin_date,hd.end_date,hd.apply_status,
				hd.user_id,hd.create_time,hd.apply_time,hd.isHandled,hd.current_handler,
				hd.reject_userid,hd.apply_begin_date,hd.apply_end_date";
			*/
			
			
            DB::beginWork();
            $rows = DB::executeAll($sql);
            $record = DB::executeAll($sql2);
            DB::commit();
            $rows[0]['detail'] = str_replace("\n", "&#13;&#10;", $rows[0]['detail']);
            tpl_assign('holidayInfo', $rows[0]);
            tpl_assign('holidayApproveInfo', $record);
            tpl_assign('opt', $_GET['opt']);
        }
    }

    function add_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        DB::beginWork();
        $status = 2;
        $sql = "select * from og_users as x,og_department as y where x.depart_id=y.depart_id and x.id = " . logged_user()->getId();
        $row = DB::executeAll($sql);
        $manage_id = $row[0]['manager_id'];
        $temp = explode(',',$row[0]['fujuzhang_id']);
        $fujuzhang_id =$temp[0];
        $departName = $row[0]['depart_name'];
        $handlerId = $manage_id;
        $juzhang_id = 9;
        // 不管请假几天，第一级审批人是固定的，所以不用关注请了几天假
        // 科长直接由副局长审批
        if (logged_user()->getUserRole() == '科长') {
            // 效能办直接由局长批 没有分管领导
            if($departName == '效能办'){
                $status = 4;
                $handlerId = $juzhang_id;
            }else{
                $status = 3;
                $handlerId = $fujuzhang_id;
            }
        } else if (logged_user()->getUserRole() == '副局长' || logged_user()->getUserRole() == '局长') {
            $status = 4;
            // 这里把局长id写死了，有可能是坑。。。
            $handlerId = $juzhang_id;
        }
	
		//如果 病假或年假 的天数大于3天,status为8
		$dateLong = get_work_days($_POST['beginDate'],$_POST['endDate']);
		if($dateLong > 3){
			if($_POST['reason'] == '2' || $_POST['reason'] == '5'){
				$status = 8;
			}
		}
		
        $sql = "INSERT INTO og_holiday_apply " .
            "(reason,detail,apply_begin_date,apply_end_date,apply_status,user_id,create_time,current_handler)
            values('" . $_POST['reason'] . "','" . $_POST['detail'] . "','" . $_POST['beginDate']
            . "','" . $_POST['endDate'] . "'," . $status . "," . logged_user()->getId() . ",now()," . $handlerId . ")";

        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }


    function edit_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        //如果 病假或年假 的天数大于3天,status为8
        $dateLong = get_work_days($_POST['beginDate'],$_POST['endDate']);
        if($dateLong > 3){
            if($_POST['reason'] == '2' || $_POST['reason'] == '5'){
                $status = 8;
            }
        }
        $sql = "
        update og_holiday_apply set
         reason='" . $_POST['reason'] . "',detail='" . $_POST['detail'] . "',
         apply_begin_date='" . $_POST['beginDate'] . "',apply_end_date='" . $_POST['endDate'] . "'";
        if ($status == 8) {
            $sql.= ", apply_status=8 ";
        }
        $sql.=" where id=" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }


    function  reject_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
        $reject_status = 5;
        if (logged_user()->getUserRole() == '副局长') {
            $reject_status = 6;
        } else if (logged_user()->getUserRole() == '局长') {
            $reject_status = 7;
        }
		//状态为8,病假或年假大于3天.
		if($_GET['status'] == 8){
		//状态为9,办公室审核不通过
			$reject_status = 9;
		}
        $sql = "
         update og_holiday_apply set
         apply_status ='$reject_status',reject_userid='" . logged_user()->getId() . "',
         apply_time= now(),
         isHandled=1 where id=" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);

        //记录请假审批日期.
        $sql2 = "INSERT INTO `og_holiday_approve`(`user_id`, `holiday_id`,is_agree) VALUES ('".logged_user()->getId()."','".$_GET['id']."',0)";
        DB::executeAll($sql2);
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

        $sql = "update og_holiday_apply set
         apply_status ='0' where id=" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }
    function  cancel_apply()
    {
        if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }

        $sql = "update og_holiday_apply set
         	end_date =now() where id=" . $_GET['id'];
        DB::beginWork();
        DB::executeAll($sql);
        DB::commit();
        ajx_current("empty");
    }
    function agree_apply()
    {
		if (logged_user()->isGuest()) {
            flash_error(lang('no access permissions'));
            ajx_current("empty");
            return;
        }
		if($_GET['status'] == '8'){
			DB::beginWork();
			$status = 2;
			$sql = "select * from og_users as x,og_department as y where x.depart_id=y.depart_id and x.id=".$_GET['user_id'];
			$row = DB::executeAll($sql);
            $departName = $row[0]['depart_name'];
			// 不管请假几天，第一级审批人是固定的，所以不用关注请了几天假
			// 科长直接由副局长审批
			if ($row[0]['title'] == '科长') {
				// 效能办直接由局长批 没有分管领导
				if($departName == '效能办'){
					$status = 4;
				}else{
					$status = 3;
				}
			} else if ($row[0]['title'] == '副局长' || $row[0]['title'] == '局长') {
				$status = 4;
			}
			$sql = "UPDATE `og_holiday_apply` SET `apply_status`=".$status." WHERE id=".$_GET['id'];
			DB::executeAll($sql);
			DB::commit();
			
		}else{
			DB::beginWork();
			$sql = "select * from og_users as x,og_department as y,og_holiday_apply as z
			 where x.depart_id=y.depart_id
			 and x.id = z.user_id
			 and z.id=".$_GET['id'];
			$row = DB::executeAll($sql);
			$manage_id = $row[0]['manager_id'];
			// 药械一科有两个分管领导，由第一个负责
			$temp = explode(',',$row[0]['fujuzhang_id']);
			$fujuzhang_id =$temp[0] ;
			$userRole = $row[0]['title'];
			$status = 0;
			$juzhang_id = 9;
			$lastStatus = $_GET['status'];
			$handlerId = 1;
	/*        echo $_POST['end'];
			echo $_POST['start'];*/
			$dateLong = getDateLong($_POST['start'], $_POST['end']);
			if ($userRole == '科长') { //小于3天分管领导直接批，大于的话，需局长再批
				if ($dateLong <= 3) {
					$status = 1;
				} else {
					if ($lastStatus == 3) {
						$status = 4;
						$handlerId = $juzhang_id;
					} else if ($lastStatus == 4) { // 处理完成了
						$status = 1;
					}

				}
			} else if ($userRole == '科员') { //小于3天科长直接批，3天以内还需分管领导批，大于3天需局长再批
				if ($dateLong <= 1) {
					$status = 1;
				} else if ($dateLong <= 3) {
					// 等待科长审批
					// 把状态打成等待分管领导审批
					if ($lastStatus == 2) {
						$status = 3;
						$handlerId = $fujuzhang_id;
					} else if ($lastStatus == 3) { // 处理完成了
						$status = 1;
					}

				} else {
					if ($lastStatus == 2) {
						$status = 3;
						$handlerId = $fujuzhang_id;
					} else if ($lastStatus == 3) { // 副局长批完局长批
						$status = 4;
						$handlerId = $juzhang_id;
					} else if ($lastStatus == 4) { //处理完成了
						$status = 1;
					}
				}
			} else if ($userRole == '副局长' || $userRole == '局长' ) { // 直接跟局长请
				$status = 1;
			}
			
			$sql = "
			 update og_holiday_apply set
			 apply_status ='$status',
			 current_handler=$handlerId,
			 apply_time= now(),
			 isHandled=1 where id=" . $_GET['id'];
			DB::beginWork();
			DB::executeAll($sql);

			//记录请假审批日期.
			$sql2 = "INSERT INTO `og_holiday_approve`(`user_id`, `holiday_id`, `approve_begin_time`, `approve_end_time`) VALUES ('".logged_user()->getId()."','".$_GET['id']."','".$_POST['approveBegin']."','".$_POST['approveEnd']."')";
			DB::executeAll($sql2);
            if ($status == 1) {
                //更新最终审批日期
                $sql3 = "UPDATE og_holiday_apply SET begin_date='".$_POST['approveBegin']."',end_date='".$_POST['approveEnd']."' where id=".$_GET['id'];
                DB::executeAll($sql3);
            }


			DB::commit();
		}
		ajx_current("empty");
    }

} // TaskController


?>