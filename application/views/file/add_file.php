<?php

require_javascript('og/modules/addMessageForm.js');
require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
require_javascript('og/file/file.js');
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
                <input name="file-name-input" id="file-name-input" value="<?php
                echo $content_info['name'];
                ?>"/>
            </td>
        </tr>
        <tr class="<?php echo $addType == 'jufa'? 'hide':''; ?>">
            <td>阅读人：</td>
            <td><div id="file-reader"></div></td>
        </tr>
        <tr>
            <td>
            </td>
            <td  style="display: none">
                <input type="checkbox" id="needSendMessage">发送短信通知阅读人
            </td>
        </tr>
        <tr>
            <td colspan="2">内容：</td>
        </tr>
        <tr>
            <td colspan="2">
                <div id="file-ue-editor" type="text/plain" style="width:900px;height:200px;"></div>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <span id="file-submit" class='new-button' onclick="og.file.submit('<?php echo $addType; ?>')">提交</span>
                &nbsp;&nbsp;
                <span id="add-file-tip" class="error-tip"></span>
            </td>
        </tr>
    </table>
</form>
<script>
   // eval('var file_content_info = <?php echo json_encode($content_info) ?>;');
    var file_content_info={
        content:  '<?php echo HtmlEncode($content_info['content']) ?>'
    };
    var content = '';
    if(file_content_info.content){
        content = file_content_info.content;
    }

    if ($('#file-ue-editor').length != 0) {
           UE.delEditor('file-ue-editor');
           UE.getEditor('file-ue-editor',{
               onready: function () {
                   this.setContent(content);
               },
               toolbars: [
                   [ 'undo', 'redo','bold', 'italic','insertimage','link','attachment', 'cleardoc']
               ]
           });
    }



    // 阅读人初始化
   eval('var dutyUserInfo = <?php echo json_encode($userInfo);?>');
   eval('var fileOpt = <?php echo '"'.$fileOpt.'"';?>');
   var dutyUserData = [];
   for (var i = 0; i < dutyUserInfo.length; i++) {
       var temp = [dutyUserInfo[i].id, dutyUserInfo[i].username];
       dutyUserData.push(temp);
   }
   var store = new Ext.data.SimpleStore
   ({
       fields: ["id", "name", 'check'],
       data: $.extend(true, [], dutyUserData)
   });
   var selected = [];
   var emptyStr = [];
   if(fileOpt =='new'){
       new Ext.form.ComboBox
       ({
           tpl: '<tpl for="."><div class="x-combo-list-item"><span><input  type="checkbox" {[values.check?"checked":""]}  value="{[values.id]}" /></span><span >{name}</span></div></tpl>',
           id: "fileReader",
           emptyText:'',
           editable: false,//默认为true，false为禁止手写和联想功能
           store: store,
           mode: 'local',//指定数据加载方式，如果直接从客户端加载则为local，如果从服务器断加载 则为remote.默认值为：remote
           typeAhead: true,
           triggerAction: 'all',
           valueField: 'name',
           displayField: 'name',
           selectOnFocus: true,
           disable: true,
           renderTo: "file-reader",
           width: 400,
           listWidth: 400,
           frame: true,
           resizable: true,
           selected: selected,
           onSelect: function (record, index) {

               record.set('check', !record.get('check'));
               var value = $('#' + this.id).val();
               // 选中
               if (record.get('check')) {
                   this.selected.push(record.get('id'));
                   if (value == '') {
                       $('#' + this.id).val(record.data.name);
                   } else {
                       $('#' + this.id).val(value + '、' + record.data.name);
                   }
               } else {
                   this.selected.splice($.inArray(record.get('id'), this.selected), 1);
                   value = value.replace(record.data.name + '、', '');
                   value = value.replace(record.data.name, '');
                   if (value.charAt(value.length - 1) == '、') {
                       value = value.substring(0, value.length - 1);
                   }
                   $('#' + this.id).val(value);
               }
           },
           getValue: function () {

               return this.selected;
           },
           listeners: {
               'render': function () {
                   $('#' + this.id).val(emptyStr.join('、'));
               }
           }
       });
   }

</script>