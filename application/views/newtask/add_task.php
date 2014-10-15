<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/main.js');
require_javascript('og/tasks/addTask.js');
require_javascript('og/tasks/new/addTask.js');
require_javascript("og/ObjectPicker.js");
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
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