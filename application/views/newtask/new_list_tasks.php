<?php
require_javascript('og/bootstrap.min.js');
require_javascript('og/jquery.min.js');
require_javascript('og/cookie.js');
require_javascript("og/CSVCombo.js");
require_javascript("og/DateField.js");
require_javascript('og/tasks/main.js');
require_javascript('og/tasks/addTask.js');
require_javascript('og/tasks/drawing.js');
require_javascript('og/tasks/TasksTopToolbar.js');
require_javascript('og/tasks/TasksBottomToolbar.js');
require_javascript('og/tasks/print.js');
require_javascript('og/tasks/new/taskList.js');
require_javascript('og/tasks/delayApply.js');
$departName = '';
foreach ($departInfo as $item) {
    $departName = $item['depart_name'];
    $departId = $item['depart_id'];
}
?>
<div id="task-list-main">
<span id="depart_id" class="hide"><?php echo $departId ?></span>
<span id="depart_name" class="hide"><?php echo $departName ?></span>
<span id="task_sub_tab" class="hide"><?php echo $tab ?></span>

<div>
    <div class="sub-tab">
        <span id='task-sub-link'
              class="<?php echo $tab == 'task' ? 'sub-tab-content' : ''; ?>"><?php //echo $departName; ?>岗位职责</span>
        <span id='apply-sub-link' class="<?php echo $tab == 'apply' ? 'sub-tab-content' : ''; ?>">延期申请</span>
        <span id='supervise-sub-link' class="<?php
        //只有效能办，并且是科长查看的时候，才有监督岗位职责，局长和副局长也能进来
        echo isset($supervise_task_list) && $userRole == '科长' ? '' : 'hide';
        ?>">督察岗位职责</span>
        <span id='comment-sub-link' class="<?php
        //只有效能办,副局长，局长对完成的任务评价
        echo $has_comment_auth ? '' : 'hide';
        echo $tab == 'comment' ? 'sub-tab-content' : '';
        ?>">待评价岗位职责</span>
    </div>

    <div class="clearFloat"></div>
</div>
<div id="" class="ogContentPanel" style="background-color:white;background-color:#F0F0F0;height:100%;width:100%;"
     id="taskContentPanel">


<div id="tasksTabContent" class="<?php echo $tab == 'task' ? '' : 'hide'; ?>"
     style="background-color:white;padding:7px;padding-top:0px;overflow-y:scroll;position:relative;">
    <div class="task-bulletin">
        <table width="100%" border="0">
            <tr >
                <td rowspan="2"
                    style="vertical-align: middle;font-weight: bold;color:#000">
                    <?php
                    echo $departName;
                    ?>
                </td>
                <td>红灯岗位职责</td>
                <td>黄灯岗位职责</td>

                <td>进行中岗位职责</td>
                <td>已完成岗位职责</td>
                <td>科室得分</td>
            </tr>
            <tr>
                <td id="light-count-4" class="num">0</td>
                <td id="light-count-3" class="num">0</td>
                <td id="light-count-2" class="num">0</td>
                <td id="light-count-1" class="num">0</td>
                <td id="depart-score" class="num"></td>
            </tr>

        </table>
    </div>
    <span class="new-button" id='add-task' onclick="og.taskList.addTaskClick()">新建岗位职责</span>
    <span class="new-button" id='view-all-task' onclick="og.taskList.viewAllTaskClick()">查看全局任务</span>

    <div class="table-header">
        <table width='100%'>
            <tr>
                <td class="d1">岗位职责</td>
                <td class="d2">目标任务</td>
                <td class="d3">责任人</td>
                <td class="d8">截止时间</td>
                <td class="d4">效能状态</td>
                <td class="d5">督察状态</td>
                <td class="d6">督察结论</td>
                <td class="d7">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    $lateTask = array();
    foreach ($task_list as $item) {

    $j = explode(" ", $item['due_date']);
    // 判断是否能申请延期的时候 要用原始的时间 ，精确到秒
    $raw_time = $item['due_date'];
    $item['due_date'] = $j[0];


    echo '<div id="task-list-' . $item['id'] . '"><table width=100%>';
    $i++;
    if ($i % 2 == 0) {
        if ($item['supervise_status'] == 1 || $item['advanced_supervise'] == 1) {
            echo '<tr  style="background: yellow">';
        } else {
            echo '<tr>';
        }
    } else {
        if ($item['supervise_status'] == 1 || $item['advanced_supervise'] == 1) {
            echo '<tr style="background: yellow" class="dashaltrow">';
        } else {
            echo '<tr class="dashaltrow">';
        }
    }
    ?>
    <td class='d1' title=" <?php echo $item['title'] ?>">
        <?php
        echo mb_substr($item['title'], 0, 10, "UTF-8");
        if (daysFromNow($item['created_on']) > -3) {
            echo "<span class='new-icon'></span>";
        }
        ?>
    </td>
    <td class='d2' title=" <?php echo $item['text'] ?>">
        <?php echo mb_substr($item['text'], 0, 18, "UTF-8"); ?>
    </td>
    <td class='d3'><?php echo $item['username'] ?></td>
    <td class='d8'><?php  echo $item['due_date'];
        $dateDiff = (strtotime($j[0]) - strtotime(date('y-m-d', time()))) / 86400;
        // 最近7天要到期的任务，还没完成 都要提醒
        if ($dateDiff >= 0 && $dateDiff <= 6 && $item['light_status'] != 1) {
            array_push($lateTask, $item['title']);
        }
        ?></td>
    <td class='d4'><?php echo getTaskLightStatus($item['light_status']) ?></td>
    <td class='d5'><?php echo getSuperviseStatus($item['supervise_status'], $item['advanced_supervise']) ?></td>
    <td class='d6'><?php echo getSuperviseResult($item['supervise_status'], $item['advanced_supervise']) ?></td>
    <td class='d7'><?php echo getTaskOptContent($item['light_status'], $item['id'], $raw_time,
            $logged_user_depart, $isSelf, $item['supervise_status'], $item['advanced_supervise'],
            $item['assigned_to_departid'],$item['text']) ?></td>
    </tr></table></div>
<!--查看岗位职责出现的-->


<!-- 转交任务Modal
 -->
<div class="modal" id="transTaskModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">任务转交</h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="form-group">
                        <label >执法任务：</label>
                        <label id="trans-task-name"></label>
                    </div>
                    <div class="form-group">
                        <label >转交理由：</label>
                        <textarea id="trans-reason" class="form-control" ></textarea>
                        <input type="hidden" id="transout-task-id" value="" />
                    </div>
                    <div class="form-group">
                        <label>科室</label>
                        <select class="form-control" id="trans-depart" >
                            <option value="">请选择科室</option>
                            <?php
                            foreach ($depart_list as $item) {
                                echo '<option value="'.$item['depart_id'].'">'.$item['depart_name'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group" >
                        <div class="col-sm-4">
                            <span class="bg-danger" id="tans-error"> </span>
                            <span class="bg-success" id="tans-info">   </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="trans-task-confirm" onclick="og.taskList.tansTaskOK()">转交</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>



<?php
}
if ($i == 0) {
    ?>
    <div class="no-data">当前暂无相关数据</div>
<?php
}
?>
<?php


function getSuperviseStatus($status, $advanced_supervise)
{
    switch ($status) {
        case 0:
            if ($advanced_supervise != 0) {
                return getAdvancedSuperviseStatus($advanced_supervise);
            } else {
                return '-';
            }
            return getAdvancedSuperviseStatus($advanced_supervise);
            break;
        case 1:
            return '<span >随机督察中</span>';
            break;
        // 随机督察完成，还要检查主动督察的状态
        case 2:
        case 3:
            if ($advanced_supervise != 0) {
                return getAdvancedSuperviseStatus($advanced_supervise);
            } else {
                return '随机督察完成';
            }
            break;
    }
}

function getAdvancedSuperviseStatus($advanced_supervise)
{
    switch ($advanced_supervise) {
        case 0:
            return '-';
            break;
        case 1:
            return '<span >主动督察中</span>';
            break;
        case 2:
        case 3:
            return '主动督察完成';
            break;
    }
}


/**判断是否有删除的权限
 * @param $departName
 * @return bool
 */
function hasDeleteAuth($departName)
{
    if (logged_user()->getUserRole() == '局长' || $departName == '效能办') {
        return true;
    } else {
        return false;
    }
}

function getTaskOptContent($status, $taskId, $dueDate, $departName, $isSelf, $superviseStatus, $advSupervise, $depart_id,$taskName)
{
    $userRole = logged_user()->getUserRole();
    $str = "<a onclick='og.taskList.viewTaskDetailClick($taskId)'>查看</a>";
    if (hasDeleteAuth($departName)) {
        $str .= "&nbsp;&nbsp;<a onclick='og.taskList.deleteClick($taskId)'>删除</a>";
    }

    switch ($status) {
        // 已完成
        case 1:
            if ($departName == '效能办' || $userRole == '局长') {
                // 不需要督察或者督察完成的,可以主动督察
                if ($superviseStatus == 0 || $superviseStatus == 2) {
                    if ($advSupervise == 0) {
                        $str .= "&nbsp;&nbsp;<a onclick='og.taskList.beginAdvancedSupervise($taskId)'>开始督察</a>";
                    } else if ($advSupervise == 1) {
                        $str .= "&nbsp;&nbsp;<a onclick='og.taskList.passAdvancedSupervise($taskId)'>通过</a>";
                        $str .= "&nbsp;&nbsp;<a onclick=\"og.taskList.rejectAdvancedSupervise";
                        $str .= "($taskId,'$dueDate',$depart_id,$status)\">不通过</a>";
                    }
                }
            }
            break;
        case 2:
        case 3:
        case 4:
            // 完成的任务不能编辑
            // 只有效能办和局长可以编辑
            if ($userRole != '科长' && $userRole != '科员' && $userRole != '副局长') {
                $str .= "&nbsp;&nbsp;<a onclick='og.taskList.viewTaskClick($taskId)'>编辑</a>";
            } else {
                if ($departName == '效能办') {
                    $str .= "&nbsp;&nbsp;<a onclick='og.taskList.viewTaskClick($taskId)'>编辑</a>";
                }
                if ($isSelf == 'self') {
                    $str .= "&nbsp;&nbsp;<a onclick='og.taskList.completeClick($taskId,$superviseStatus,$advSupervise)'>完成</a>";
                    // 过期的任务不能再申请延期
                    if (strtotime($dueDate) > time()) {
                        $str .= "&nbsp;&nbsp;<a onclick='og.taskList.drawDelayApply($taskId)'>申请延期</a>";
						if($userRole == '科长'){
							$str .= "&nbsp;&nbsp;<a onclick=og.taskList.deliverClick($taskId,'$taskName')>转交</a>";
						}
                    }
					
                }

            }

            break;
    }
    return $str;

}

function getSuperviseResult($status, $advStatus)
{
    switch ($status) {
        case 0:
            if ($advStatus != 0) {
                return getAdvSuperviseResult($advStatus);
            } else {
                return '-';
            }
            break;
        case 1:
            return '-';
            break;
        case 2:
            if ($advStatus != 0) {
                return getAdvSuperviseResult($advStatus);
            } else {
                return '通过';
            }
            break;
        case 3:
            return '<span >未通过</span>';
            break;
    }
}

function getAdvSuperviseResult($status)
{
    switch ($status) {
        case 0:
        case 1:
            return '-';
            break;
        case 2:
            return '通过';
            break;
        case 3:
            return '<span >未通过</span>';
            break;
    }
}

function getTaskLightStatus($status)
{
    $str = '';
    switch ($status) {
        case 1:
            $str = '<span class="ico-task-light-green" title="已完成"></span>';
			break;
		case 0:
            $str = '<span title="已转交">已转交</span>';
            break;
        case 2:
            $str = '<span class="ico-task-light-gray" title="进行中"></span>';
            break;
        case 3:
            $str = '<span class="ico-task-light-yellow" title="已过期"></span>';
            break;
        case 4:
            $str = '<span class="ico-task-light-red" title="过期超过7天"></span>';
            break;
    }

    return $str;
}

?>

</div>

<!--  我的申请面板 begin-->

<div id="applyTabContent" class="<?php echo $tab == 'apply' ? '' : 'hide'; ?>"
     style="background-color:white;padding:7px;padding-top:0px;overflow-y:scroll;position:relative;">
    <div class="table-header">
        <table width='100%'>
            <tr>
                <td class="yanqid1">岗位职责</td>
                <td class="yanqid2">申请延期天数</td>
                <td class="yanqid3">申请创建时间</td>
                <td class="yanqid8">创建人</td>
                <td class="yanqid4">批准天数</td>
                <td class="yanqid5">状态</td>
                <td class="yanqid6">审批时间</td>
                <td class="yanqid7">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    foreach ($apply_list as $item) {
    echo '<div id="delay-apply-' . $item['id'] . '"><table width=100%>';
    $i++;
    if ($i % 2 == 0) {
        echo '<tr>';
    } else {
        echo '<tr class="dashaltrow">';
    }?>
    <td class='yanqid1'
        title="<?php echo $item['title'] ?>"><?php echo mb_substr($item['title'], 0, 12, "UTF-8"); ?></td>
    <td class='yanqid2'><?php echo $item['hope_day'] ?></td>
    <td style='display:none'><?php echo $item['reason'] ?></td>
    <td class='yanqid3'><?php echo $item['create_time'] ?></td>
    <td class='yanqid8'><?php echo $item['username'] ?></td>
    <td class='yanqid4'><?php echo $item['agree_day'] ?></td>
    <td class='yanqid5'><?php echo get_apply_status($item['status']) ?></td>
    <td class='yanqid6'><?php echo gethandletime($item['handle_time']) ?></td>
    <td class='yanqid7'><?php echo getoptcontent($item['status'], $item['id'], $item['task_id'], $isSelf) ?></td>
    </tr></table></div>
<div class="ogAppendBox hide" id="apply-detail<?php echo $item['id'] ?>">
    <table width="500px">
        <tr>
            <td width="100px">岗位职责：</td>
            <td style="text-align: left"><?php echo $item['title'] ?></td>
        </tr>
        <tr>
            <td>延期原因：</td>
            <td><textarea id='apply-reason-<?php echo $item['id'] ?>'><?php echo $item['reason'] ?></textarea>
            </td>
        </tr>
        <tr>
            <td>申请延期：</td>
            <td style="text-align: left">
                <input class='w60' id='apply-day-<?php echo $item['id'] ?>'
                       value="<?php echo $item['hope_day'] ?>"/>&nbsp;天
            </td>
        </tr>
        <tr style="text-align: left" class="<?php echo $userRole == '局长' ? '' : 'hide' ?>">
            <td>批准延期：
            </td>
            <td>
                <input class='w30' id='agree-day-<?php echo $item['id'] ?>'/>&nbsp;天
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="<?php echo $userRole == '局长' ? '' : 'hide' ?>">
                    <span class="small-button"
                          onclick="og.taskList.agreeDelayApply(<?php echo $item['id'] ?>,<?php echo $item['task_id'] ?>)">
                    同意
                    </span>
                    &nbsp;&nbsp;
                    <span class="small-button"
                          onclick="og.taskList.disagreeDelayApply(<?php echo $item['id'] ?>,<?php echo $item['task_id'] ?>)">
                        不同意
                    </span>
                </span>
               <span class="<?php
               // 局长或者不是待审批的状态 都能修改
               if ($userRole == '局长' || $item['status'] != 0) {
                   echo 'hide';
               } else {
                   echo '';
               }
               ?>">
                   <span class="small-button"
                         onclick="ogTasks.saveDelayApplyDetail(<?php echo $item['id'] ?>)">保存</span>
                   <span class="small-button"
                         onclick="ogTasks.viewDelayApplyDetail(<?php echo $item['id'] ?>)">关闭</span>

               </span>
                <span class="error-tip" id="edit-apply-tip"></span>
            </td>

        </tr>

    </table>
</div>
<?php
}
if ($i == 0) {
    ?>
    <div class="no-data">当前暂无相关数据</div>
<?php
}
?>

</div>
<?php
function gethandletime($time)
{
    if ($time == '0000-00-00 00:00:00') {
        return '-';
    } else {
        return $time;
    }
}

function get_apply_status($status)
{
    switch ($status) {
        case 0:
            return '待审批';
            break;
        case 1:
            return '审批通过';
            break;
        case 2:
            return '审批不通过';
            break;
        case 3:
            return '已撤回';
            break;
    }
}

function getoptcontent($status, $id, $task_id, $isSelf)
{
    $userRole = logged_user()->getUserRole();
    $str = '';
//$str = '<a onclick="og.dialog.draw()">查看</a>';
// 只有局长有处理延期申请的权限
    if ($userRole == '局长') {
        if ($status == 0) {
            $str .= '&nbsp;&nbsp;<a onclick="ogTasks.viewDelayApplyDetail(' . $id . ',' . $task_id . ')">处理申请</a>';
        }
        return $str;
    } else {
        $str = '<a onclick="ogTasks.viewDelayApplyDetail(' . $id . ',' . $status . ')">查看</a>';
// 还未处理的请求可以撤回
        if ($status == 0 && $isSelf == 'self') {
            $str .= '&nbsp;&nbsp;<a onclick="ogTasks.cancelDelayApply(' . $id . ')">撤回</a>';
        }
        return $str;
    }
}

?>
<!--  我的申请面板end -->


<!--  督察岗位职责面板 begin-->

<div id="superviseTabContent"  class="<?php echo $tab == 'supervise' ? '' : 'hide'; ?>"
     style="background-color:white;padding:7px;padding-top:0px;overflow-y:scroll;position:relative;">
    <div class="table-header">
        <table width='100%'>
            <tr>
                <td class="d1">岗位职责</td>
                <td class="d2">目标任务</td>
                <td class="d3">责任人</td>
                <td class="d8">截止时间</td>
                <td class="d4">效能状态</td>
                <td class="d5">督察状态</td>
                <td class="d6">督察结论</td>
                <td class="d7">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    foreach ($supervise_task_list as $item) {
    echo '<div id="supervise-task-list-' . $item['id'] . '"><table width=100%>';
    $i++;

    if ($i % 2 == 0) {
        echo '<tr>';
    } else {
        echo '<tr class="dashaltrow">';
    }
    ?>
    <td class='d1'><?php echo mb_substr($item['title'], 0, 12, "UTF-8"); ?></td>
    <td class='d2'><?php echo mb_substr($item['text'], 0, 18, "UTF-8"); ?></td>
    <td class='d3'><?php echo $item['username'] ?></td>
    <td class='d4'><?php $j = explode(" ", $item['due_date']);
        echo $j[0] ?></td>
    <td class='d8'><?php echo getTaskLightStatus($item['light_status']) ?></td>
    <td class='d5'><?php echo getSuperviseStatus($item['supervise_status'], $item['advanced_supervise']) ?></td>
    <td class='d6'><?php echo getSuperviseResult($item['supervise_status'], $item['advanced_supervise']) ?></td>
    <td class='d7'><?php echo getSuperviseOptContent($item['supervise_status'], $item['id'], $item['due_date'], $item['assigned_to_departid'], $item['advanced_supervise']) ?></td>
    </tr></table></div>
<?php
}
if ($i == 0) {
    ?>
    <div class="no-data">当前暂无相关数据</div>
<?php
}
?>

</div>
<?php
function getSuperviseOptContent($status, $task_id, $due_date, $depart_id, $adv_supervise)
{
    switch ($status) {
        // 不需要督察 或者 通过的时候  要看是否有主动督察
        case 0:
        case 2:
            if($adv_supervise == 1){
                return "&nbsp;&nbsp;<a onclick='og.taskList.passAdvancedSupervise($task_id)'>通过</a>".
                "&nbsp;&nbsp;<a onclick=\"og.taskList.rejectAdvancedSupervise".
                "($task_id,'$due_date',$depart_id,$status)\">不通过</a>";
            }else{
                return '-';
            }
            break;
        case 3:
            return '-';
            break;
        case 1:
            $date1 = explode(" ", $due_date);
            $date1 = $date1[0];
            return "<a onclick='og.taskList.passSupervise($task_id)'>通过</a>" .
            "&nbsp;<a  onclick=og.taskList.rejectSupervise($task_id,'$date1',$depart_id)>不通过</a>";
            break;
    }
}

?>
<!--  督察岗位职责面板end -->


<!--  评价岗位职责面板 begin-->

<div id="commentTabContent" class="<?php echo $tab == 'comment' ? '' : 'hide'; ?>"
     style="background-color:white;padding:7px;padding-top:0px;overflow-y:scroll;position:relative;">
    <div class="table-header">
        <table width='100%'>
            <tr>
                <td class="d1">岗位职责</td>
                <td class="d2">目标任务</td>
                <td class="d3">责任人</td>
                <td class="d8">截止时间</td>
                <td class="d8">完成时间</td>
                <td class="d4">效能状态</td>
                <td class="d7">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    foreach ($comment_task_list as $item) {
    echo '<div id="supervise-task-list-' . $item['id'] . '"><table width=100%>';
    $i++;

    if ($i % 2 == 0) {
        echo '<tr>';
    } else {
        echo '<tr class="dashaltrow">';
    }
    ?>
    <td class='d1'><?php echo mb_substr($item['title'], 0, 12, "UTF-8"); ?></td>
    <td class='d2'><?php echo mb_substr($item['text'], 0, 18, "UTF-8"); ?></td>
    <td class='d3'><?php echo $item['username'] ?></td>
    <td class='d4'><?php $j = explode(" ", $item['due_date']);
        echo $j[0] ?></td>
    <td class='d4'><?php  $j = explode(" ", $item['completed_on']);
        echo $j[0]; ?></td>
    <td class='d8'><?php echo getTaskLightStatus($item['light_status']) ?></td>
    <td class='d7'><?php echo getCommentOptContent($item['id'],$item['text']) ?></td>
    </tr></table></div>
<?php
}
if ($i == 0) {
    ?>
    <div class="no-data">当前暂无相关数据</div>
<?php
}
?>

</div>
<!-- 评价任务Modal
-->
<div class="modal" id="commentTaskModal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" >任务评价</h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    <div class="form-group">
                        <label >执法任务：</label>
                        <label id="comment-task-name"></label>
                    </div>
                    <div class="form-group">
                        <label >评价：</label>
                        <label class="radio-inline">
                            <input type="radio" name="comment-status"  value="1"> 好
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="comment-status"  value="2"> 中
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="comment-status"  value="3"> 差
                        </label>
                    </div>
                    <div>
                        <textarea id="comment-text" class="form-control"  roww="4"></textarea>
                        <input type="hidden" id="comment-task-id" value="" />
                    </div>
                    <div class="form-group" >
                        <div class="col-sm-4">
                            <span class="bg-danger" id="comment-error"> </span>
                            <span class="bg-success" id="comment-info">   </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="comment-task-confirm" onclick="og.taskList.commentTaskOK()">评价</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

<!--  评价岗位职责面板end -->


</div>


<?php
function getCommentOptContent($task_id,$text)
{
    $str = "<a onclick='og.taskList.viewTaskDetailClick($task_id)'>查看</a>";
    return $str.="  <a onclick=og.taskList.commentClick($task_id,'$text')>评价</a>" ;
}

?>
</div>
<script>
    function renderBulletin() {
        eval('var departOverviewData=<?php echo $task_overview_data?>');
        var redLightCount = 0;
        var yellowLightCount = 0;
        var baseScore = departOverviewData[0].score || 100;
        for (var i = 0, item; item = departOverviewData[i++];) {
            var status = item['light_status'];
            if (status == '4') {
                redLightCount = +item['light_count'];
            }
            if (status == '3') {
                yellowLightCount = +item['light_count'];
            }
            $('#light-count-' + status).html(item['light_count']);
        }
        $('#depart-score').html(baseScore);
    }
    renderBulletin();
    // 用户点击过tab的切换，按用户点击的来
    if (typeof og.taskSubTab != 'undefined') {
        showSubTab($('#' + og.taskSubTab));
    }

    function showSubTab(ele) {
        $('.sub-tab span').removeClass('sub-tab-content');
        ele.addClass('sub-tab-content');
        $('#tasksTabContent').addClass('hide');
        $('#applyTabContent').addClass('hide');
        $('#superviseTabContent').addClass('hide');
        $('#commentTabContent').addClass('hide');
        if (ele.html() == '延期申请') {
            $('#applyTabContent').removeClass('hide');
        } else if (ele.html() == '督察岗位职责') {
            $('#superviseTabContent').removeClass('hide');
        }else if (ele.html() == '待评价岗位职责') {
            $('#commentTabContent').removeClass('hide');
        }
        else {
            $('#tasksTabContent').removeClass('hide');
        }
    }
    $('.sub-tab span').click(function () {
        var ele = $(this);
        og.taskSubTab = ele.attr('id');
        showSubTab(ele);
    });
    //ogTasks.showApplyPanel()
</script>
<?php
// 有要提醒的任务
$strLateTask = '';
if (count($lateTask) > 0) {
    $strLateTask = '<span style="font-weight: bold;color: red">以下岗位职责即将到期，请您尽快完成。</span></br>';
    foreach ($lateTask as $task) {
        $strLateTask .= $task . '</br>';
    }
}
?>
<span class="hide" id="strLateTaskHF"><?php echo $strLateTask; ?></span>
<script>
    var strLateTask = $('#strLateTaskHF').html();
    var formatDate = getFomatDate();
    if (strLateTask != ''
        // 只在岗位职责一个子tab进行提示
        && $('#task-sub-link').hasClass('sub-tab-content')
        // 只给科长提示，局长不提示
        && og.loggedUser.userRole == '科长'
        && og.loggedUser.hadTipTask != true
    // 今天还没提醒过任务
    // && $.cookie('task_tip') != formatDate
        ) {
        Ext.MessageBox.alert('提示', '<?php echo $strLateTask; ?>');
        // 每次不刷新页面的话 提示只出一次
        og.loggedUser.hadTipTask = true;
        $.cookie('task_tip', formatDate); // 设置cookie
    }
    //只有效能办和局长可以新建岗位职责
    if (og.loggedUser.userRole == '局长' || $('#depart_name').html() == '效能办') {
        $('#add-task').show();
    } else {
        $('#add-task').hide();
    }
    //只有效能办和局长可以新建岗位职责
    if ($('#depart_name').html() == '效能办') {
        $('#view-all-task').show();
    } else {
        $('#view-all-task').hide();
    }
    function getFomatDate() {
        var myDate = new Date();
        var formatDate = myDate.getFullYear() + '-' + (myDate.getMonth() + 1) + '-' + myDate.getDate();
        return formatDate;
    }
</script>