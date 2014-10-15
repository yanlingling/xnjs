<?php

require_javascript('og/tasks/new/addTask.js');

require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
<form id="submit-depart-law-task" style='height:100%;background-color:white;padding: 10px'
      class="internalForm" action="<?php echo get_url('newtask2', 'add_law_task') ?>" method="post"
    >
    <table width="80%">


        <tr>
            <td>科室：</td>
            <td>
                <div id="depart-container2"></div>
            </td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td colspan="4">法定职责：</td>
        </tr>
        <tr>
            <td colspan="4">
                <div id="depart-law-task-container2"></div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <span id="law-task-submit" class='new-button mt10' onclick="og.addTask.lawTasksubmit()">提交</span>
                &nbsp;&nbsp;
                <span id="law-add-task-tip" class="error-tip"></span>
            </td>

        </tr>
    </table>
</form>
<script>

    eval('var taskDepartUsersStore =<?php echo $depart_list?>;');
    og.addTask.initDepartCombo('depart-container2',taskDepartUsersStore);
    new Ext.form.HtmlEditor({
        renderTo: 'depart-law-task-container2',
        id: 'depart-law-task-content2',
        width: 1000,
        height: 800,
        fieldLabel: '',
        enableAlignments: true,  //允许编辑器中的按钮居左，居中和居右显示
        enableColors: true,      //允许前景/高亮颜色按钮显示
        enableFont: true,       //允许增大、缩小字号按钮显示
        enableFontSize: true,   //Enable the increase/decrease font size buttons (defaults to true)
        enableFormat: true,     //Enable the bold, italic and underline buttons (defaults to true)
        enableLinks: true,      //Enable the create link button. Not available in Safari. (defaults to true)
        enableLists: true,      //Enable the bullet and numbered list buttons. Not available in Safari. (defaults to true)
        enableSourceEdit: true,  //Enable the switch to source edit button. Not available in Safari. (defaults to true)
        value: '',
        fontFamilies: ["宋体", "隶书", "黑体"]
    })
    /* */
</script>