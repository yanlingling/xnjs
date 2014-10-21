<?php
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
require_javascript('og/jquery.min.js');
require_javascript('og/cookie.js');

?>
<div id="task-list-main">
<div class="<?php echo logged_user()->getUserRole()=='局长'?'':'hide'?>" >
    <span class="new-button  "  id='add-task' onclick="og.taskList.addTaskClick()">新建岗位职责</span>
    <div class="clearFloat"></div>
</div>
<div id="" class="ogContentPanel" style="<?php echo logged_user()->getUserRole()=='局长'?'':'margin-top:20px;'?>background-color:white;background-color:#F0F0F0;height:100%;width:100%;">

    <div id="tasksTabContent"
         style="background-color:white;padding:7px;padding-top:0px;overflow-y:scroll;position:relative;" >
        <div class="table-header">
            <table width='100%'>
                <tr>
                    <td style="width: 100px">科室名称</td>
                    <td style="width: 100px">效能状态</td>
                    <td style="width: 100px">效能考核</td>
                    <td style="width: 200px" class="<?php echo logged_user()->getUserRole()=='局长'?'':'hide'?>">操作</td>

                </tr>
            </table>
        </div>

        <?php
        $i = 0;
        $hasApplyDepart = array();
        foreach ($group_task_list as $item) {
        echo '<div id="group-task-list-' . $item['depart_id'] . '"><table width=100%>';
        $i++;
        if ($i % 2 == 0) {
            echo '<tr>';
        } else {
            echo '<tr class="dashaltrow">';
        }?>
        <td style="width: 100px">
            <a onclick="og.taskList.goToDepartTask(<?php echo $item['depart_id'] ?>)">
                <?php echo $item['depart_name'] ?>
            </a>
        </td>
        <td style="width: 100px"><?php echo getTaskLightStatus($item['light_status']) ?></td>
        <td style="width: 100px"><?php echo $item['score'] ?></td>
        <td class="<?php echo logged_user()->getUserRole()=='局长'?'':'hide'?>"  style="width: 200px">
            <?php
            echo getTaskOptContent($item['depart_id'],$item['apply_num'],$item['comment_num']);
            // 有未处理的延期申请
            if($item['apply_num']>0){
                array_push($hasApplyDepart, $item['depart_name']);
            }
            ?>
        </td>

        </tr></table></div>
    <?php
    }
    ?>
    <?php


    function getTaskOptContent($departId,$num,$numComment)
    {

        return "<a onclick='og.taskList.goToDepartApply($departId)'>"
        ."<span class='bolder'>$num</span>个未处理的延期申请</a>&nbsp;&nbsp;&nbsp;&nbsp;".
            "<a onclick='og.taskList.goToComment($departId)'>"
        ."<span class='bolder'>$numComment</span>个待评价任务</a>&nbsp;&nbsp;&nbsp;&nbsp;";

    }


    function getTaskLightStatus($status)
    {
        $str = '';
        switch ($status) {
            case 1:
            case 5:
                $str = '<span class="ico-task-light-green" title="已完成"></span>';
                break;
            case 2:
                $str = '<span class="ico-task-light-gray"  title="进行中"></span>';
                break;
            case 3:
                $str = '<span class="ico-task-light-yellow"  title="已过期"></span>';
                break;
            case 4:
                $str = '<span class="ico-task-light-red"  title="过期超过7天"></span>';
                break;
        }

        return $str;
    }

    ?>

</div>


</div>
</div>


<?php
// 有要提醒的任务
$strApply = '';
if (count($hasApplyDepart) > 0) {
    $strApply = '<span style="font-weight: bold;color: red">以下科室有延期申请未处理：</span></br>';
    foreach ($hasApplyDepart as $task) {
        $strApply .= $task . '</br>';
    }
}
?>
<span class="hide" id="strApplyHF"><?php echo $strApply; ?></span>
<script>
    var strApply = $('#strApplyHF').html();
    var formatDate = getFomatDate();
    if (strApply != ''

        // 只给科长提示，副局长不提示
        && og.loggedUser.userRole == '局长'
        // 今天还没提醒过任务
        && og.loggedUser.hadTipDelayApply != true
        ) {
        Ext.MessageBox.alert('提示', '<?php echo $strApply; ?>');
		og.loggedUser.hadTipDelayApply = true;
        //$.cookie('apply_tip', formatDate); // 设置cookie
    }

    function getFomatDate() {
        var myDate = new Date();
        var formatDate = myDate.getFullYear() + '-' + (myDate.getMonth() + 1) + '-' + myDate.getDate();
        return formatDate;
    }
</script>