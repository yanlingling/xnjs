<?php

require_javascript('og/modules/addMessageForm.js');
require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
require_javascript('og/kaoqinducha/kaoqinducha.js');
require_javascript('og/common.js');
require_javascript('editor/ueditor.config.js');
require_javascript('editor/ueditor.all.js');
require_javascript('editor/lang/zh-cn/zh-cn.js');
function HtmlEncode($fString)
{
    if($fString!="")
    {

        $fString = str_replace( '"', '&quot;',$fString);
        $fString = str_replace( '\'', '&#39;',$fString);

    }
    return $fString;
}
$genid = gen_id();
?>
<form id="submit-learning-content" style='height:100%;background-color:white;padding: 10px'
      class="internalForm"
      method="POST"  enctype="multipart/form-data"
    >
    <table width="80%">
        <tr>
            <td width="80px">文件名：</td>
            <td>
                <input name="kaoqinducha-name-input" id="kaoqinducha-name-input" value="<?php
                echo $content_info['name'];
                ?>"/>
            </td>
        </tr>
        <tr>
            <td colspan="2">内容：</td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="kaoqinducha-ue-editor" type="text/plain" style="width:900px;height:200px;"></div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <span id="kaoqinducha-submit" class='new-button' onclick="og.kaoqinducha.submit()">提交</span>
                &nbsp;&nbsp;
                <span id="add-kaoqinducha-tip" class="error-tip"></span>
            </td>
        </tr>
    </table>
</form>
<script>
   // eval('var kaoqinducha_content_info = <?php echo json_encode($content_info) ?>;');
    var kaoqinducha_content_info={
        content:  '<?php echo HtmlEncode($content_info['content']) ?>'
    };
    var content = '';
    if(kaoqinducha_content_info.content){
        content = kaoqinducha_content_info.content;
    }

    if ($('#kaoqinducha-ue-editor').length != 0) {
           UE.delEditor('kaoqinducha-ue-editor');
           UE.getEditor('kaoqinducha-ue-editor',{
               onready: function () {
                   this.setContent(content);
               },
               toolbars: [
                   [ 'undo', 'redo','bold', 'italic','insertimage','link','attachment', 'cleardoc']
               ]
           });
    }


</script>