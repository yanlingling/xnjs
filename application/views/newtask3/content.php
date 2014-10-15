<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/new/taskList.js');
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
<span class="new-button mt10" <?php
echo ($depart_info[0]['depart_name'] == '效能办' ? '' : 'style="display:none"');
?> id='add-law-task' onclick="og.taskList.addDepartTask()">新建岗位职责</span>
<div>
    <?php
    //print_r($depart_info);
    echo $depart_info[0]['depart_task'];
    ?>
</div>