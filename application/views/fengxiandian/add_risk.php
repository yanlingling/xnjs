<?php

require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/main.js');
require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
require_javascript('og/risk/risk.js');
require_javascript('og/common.js');

$genid = gen_id();
?>
<form id="submit-risk-content" style='height:100%;background-color:white;padding: 10px'
      class="internalForm"
      action="<?php
      $opt = "add";
      if (isset($content_info)) {
          $type = $content_info['content_type'];
          $opt = 'save';
      }
      echo get_url('fengxiandian', 'add_risk', array("type" => $type, "opt" => $opt))
      ?>"
      method="POST" enctype="multipart/form-data"
    >
    <table width="80%">
        <tr>
            <td colspan="2">问卷名称：<input name="risk-name-input" id="risk-name-input" value="<?php
                echo $content_info['name'];
                ?>"/></td>
        </tr>


        <tr>
            <td width="86px">到期时间：</td>
            <td>
                <div id="risk-due-date"></div>
            </td>
        </tr>
        <?php

        ?>

    </table>

    <div id="question-area">
        问卷题目：
        <input id="questionNum" name='questionNum' value="1" type="hidden">
        <div class="risk-question">
            <div>
                题目1：<input class="question-title" id="question-title-1"/>
            </div>
            <div>
                答案1：<input  class="answer1"  id="question-answer1-1"/>
                答案2：<input class="answer2"  id="question-answer2-1"/>
            </div>
        </div>
    </div>
    <div id="add-question" class="new-button" onclick="og.risk.addQuestionClickHandler()">+添加题目</div>


    <div>
        <span id="risk-submit" class='new-button' onclick="og.risk.submit('<?php
        echo $type;
        ?>')">提交</span>
        &nbsp;&nbsp;
        <span id="add-risk-tip" class="error-tip"></span>
    </div>
</form>
<script>
    eval('var risk_content_info = <?php echo json_encode($content_info) ?>;');
    var due_date = '';
    var content = '';
    if (risk_content_info) {
        due_date = risk_content_info.due_date.split(' ')[0];
        content = risk_content_info.content;
    }

    new og.DateField({
        renderTo: 'risk-due-date',
        name: 'risk-date-picker',
        id: 'risk-date-picker',
        value: due_date,
        editable: false,
        readOnly: true
    });
    if ($('#risk-content').length != 0) {
        new Ext.form.HtmlEditor({
            renderTo: 'risk-content',
            id: 'risk-content',
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
            value: content,
            fontFamilies: ["宋体", "隶书", "黑体"]
        })

    }
</script>