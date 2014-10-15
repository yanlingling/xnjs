<?php

require_javascript('og/modules/addMessageForm.js');
require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
require_javascript('og/report/report.js');
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
            <td width="100px">简报名称：</td>
            <td>
                <input name="report-name-input" id="report-name-input" value="<?php
                echo $content_info['name'];
                ?>"/>
            </td>
        </tr>
        <tr>
            <td colspan="2">内容：</td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="report-ue-editor" type="text/plain" style="width:900px;height:200px;"></div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <span id="report-submit" class='new-button' onclick="og.report.submit(<?php echo $content_info['id'];?>)">提交</span>
                &nbsp;&nbsp;
                <span id="add-report-tip" class="error-tip"></span>
            </td>
        </tr>
    </table>
</form>
<script>
   // eval('var report_content_info = <?php echo json_encode($content_info) ?>;');
    var report_content_info={
        content:  '<?php echo HtmlEncode($content_info['content']) ?>'
    };
    var content = '';
    if(report_content_info.content){
        content = report_content_info.content;
    }

    if ($('#report-ue-editor').length != 0) {
           UE.delEditor('report-ue-editor');
           UE.getEditor('report-ue-editor',{
               onready: function () {
                   this.setContent(content);
               },
               toolbars: [
                   [ 'undo', 'redo','bold', 'italic','insertimage','link','attachment', 'cleardoc']
               ]
           });
    }



   eval('var reportOpt = <?php echo '"'.$reportOpt.'"';?>');
   if(reportOpt =='new'){
   }

</script>,