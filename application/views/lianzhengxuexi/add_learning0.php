<?php

require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/main.js');
require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
require_javascript('og/learning/learning.js');
require_javascript('og/common.js');
function HtmlEncode($fString)
{
    if($fString!="")
    {

        $fString = str_replace( '"', '&quot;',$fString);
        $fString = str_replace( '\'', '&#39;',$fString);

    }
    return $fString;
}
echo HtmlEncode(json_encode($content_info)) ;

$genid = gen_id();
?>
<form id="submit-learning-content" style='height:100%;background-color:white;padding: 10px'
      class="internalForm"
      action="<?php
      $opt = "add";
      if(isset($content_info)){
          $type = $content_info['content_type'];
          $opt = 'save';
      }
      echo get_url('lianzhengxuexi', 'add_learning', array("type" => $type,"opt" => $opt))
      ?>"
      method="POST"  enctype="multipart/form-data"
    >
    <table width="80%">
        <tr>
            <td colspan="2">学习内容：<input name="learning-name-input" id="learning-name-input" value="<?php
                echo $content_info['name'];
                ?>"/></td>
        </tr>


        <tr>
            <td width="86px">到期时间：</td>
            <td>
                <div id="learning-due-date"></div>
            </td>
        </tr>
        <?php
        if ($type == 0) {
            ?>
            <tr>
                <td colspan="2">内容：</td>
            </tr>
            <tr>
                <td colspan="2">
                    <div id="learning-content"></div>
                </td>
            </tr>
        <?php
        } else {
            ?>
            <tr>
                <td colspan="2">视频名称：<input name="vedio-name-input" id="vedio-name-input"  value="<?php
                    echo $content_info['location'];
                    ?>"  /> <a href="uploadVedio.php" target="_blank">上传视频</a></td>

            </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="2">
                <span id="learning-submit" class='new-button' onclick="og.learning.submit('<?php
                echo $type;
                ?>'<?php
                if(isset($content_info['id'])){
                    echo ','.$content_info['id'];
                }
                ?>)">提交</span>
                &nbsp;&nbsp;
                <span id="add-learning-tip" class="error-tip"></span>
            </td>
        </tr>
    </table>
</form>
<script>
    eval('var learning_content_info = <?php echo HtmlEncode(json_encode($content_info)) ?>;');
    var due_date = '';
    var content = '';
    if(learning_content_info){
        due_date  = learning_content_info.due_date.split(' ')[0];
        content = learning_content_info.content;
    }

    new og.DateField({
        renderTo: 'learning-due-date',
        name: 'learning-date-picker',
        id: 'learning-date-picker',
        value: due_date,
        editable: false,
        readOnly: true
    });
    if ($('#learning-content').length != 0) {
        new Ext.form.HtmlEditor({
            renderTo: 'learning-content',
            id: 'learning-content',
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
        /*new Ext.form.TextField({
            renderTo: 'vedio-content',
            fieldLabel: '选择文件',
            name: "uploadLearningFile",
            id: "uploadLearningFile",
            inputType: 'file',
            // id:'importuser_value_text',
            cls: 'default',
            anchor: '70%'
        });*/
    }
</script>