<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/main.js');
require_javascript('og/tasks/addTask.js');
require_javascript('og/tasks/new/addTask.js');
require_javascript("og/ObjectPicker.js");
require_javascript("og/jquery.min.js");
$genid = gen_id();
$item = $taskDetail;
?>

<form class="form-horizontal" role="form">
    <div class="form-group">
        <label class="col-sm-2 control-label">岗位职责:</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?php echo $item['title'] ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">责任人:</label>
        <div class="col-sm-10">
            <p class="form-control-static"><?php echo $item['username'] ?></p>

        </div>
    </div>
</form>


<div id="task-more-detail<?php echo $item['id'] ?>" class="ogAppendBox hide">
    <table width="100%" style="text-align: left" class="task-detail">
        <tr>
            <td>岗位职责：<?php echo $item['title'] ?></td>
            <td>责任人：<?php echo $item['username'] ?></td>
        </tr>
        <tr>
            <td colspan="2">目标任务：</td>

        </tr>
        <tr>
            <td colspan="2">
                <textarea readonly="readonly"><?php echo $item['text'] ?></textarea>
            </td>
        </tr>
        <tr>
            <td>当前状态：<?php echo transTaskStatus($item['light_status']) ?></td>
            <td>到期时间：<?php echo $item['due_date'] ?></td>
        </tr>
        <?php
        if ($item['light_status'] == 1) {
            echo "<tr><td colspan='2'>完成情况描述：</td></tr>";
            echo "<tr><td colspan='2'><textarea readonly='readonly'>" . $item['complete_detail'] . "</textarea></td></tr>";
            $j = explode(" ", $item['completed_on']);
            $item['completed_on'] = $j[0];
            echo "<tr><td>完成时间：" . $item['completed_on'] . "</td><td>督察情况:" . transTaskSupervise($item['supervise_status']) . "</td></tr>";
        }
        if ($item['supervise_status'] == 3 || $item['supervise_status'] == 2) {
            echo "<tr><td colspan='2'>随机督察意见：</td></tr>";
            echo "<tr><td colspan='2'><textarea readonly='readonly'>" . $item['supervise_feedback'] . "</textarea></td></tr>";
        }
        if ($item['advanced_supervise'] == 3 || $item['advanced_supervise'] == 2) {
            echo "<tr><td colspan='2'>主动督察意见：</td></tr>";
            echo "<tr><td colspan='2'><textarea readonly='readonly'>" . $item['advanced_supervise_feedback'] . "</textarea></td></tr>";
        }

        ?>

        <tr>
            <td colspan="2">
                <span class="small-button"
                      onclick="og.taskList.viewTaskDetailCancelClick(<?php echo $item['id'] ?>)">取消</span>
            </td>
        </tr>
    </table>
</div>










<span id='add-task-taskid' class="hide"></span>
<form id="submit-task-form" style='height:100%;background-color:white;padding: 10px' target="task-handle"
      class="internalForm" action="<?php echo get_url('newtask', 'add_task') ?>" method="post"
    >
    <table width="80%">
        <tr>
            <td colspan="4">岗位职责：<input name="task-name-input" id="task-name-input"/></td>


        </tr>

        <tr>
            <td colspan="4">目标任务：</td>
        </tr>
        <tr>
            <td colspan="4">
                <textarea name='task-detail-input' id="task-detail-input"></textarea>
            </td>
        </tr>
        <tr>
            <td style="width: 95px">到期时间：</td>
            <td>
                <div id="task-due-date"></div>
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>责任科室：</td>
            <td>
                <div id="task-due-depart"></div>
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="2">
                <span id="task-submit" class='new-button' onclick="og.addTask.submit()" >提交</span>
                &nbsp;&nbsp;
                 <span id="add-task-tip" class="error-tip"></span>
            </td>

        </tr>
    </table>
</form>
<script>
    og.addTask.initDate();
    eval('var taskDepartUsersStore =<?php echo $depart_list?>;');
    var taskContent = undefined;
    <?php if(isset($taskContent)){
        echo "eval('var taskContent =$taskContent;')";
    }?>;
    og.addTask.initDepartCombo('task-due-depart',taskDepartUsersStore);
    // 查看任务
    if (taskContent) {
        og.addTask.fillTaskField(taskContent);
    }
</script>