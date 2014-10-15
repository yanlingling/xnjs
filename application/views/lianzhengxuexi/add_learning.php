<?php

require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/main.js');
require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
require_javascript('og/learning/learning.js');
require_javascript('og/common.js');
require_javascript('editor/ueditor.config.js');
require_javascript('editor/ueditor.all.min.js');
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
        <tr>
            <td width="86px">必学</td>
            <td>
               <input id='is-must-learn' type="checkbox" <?php
                echo $content_info['must_learn'] == 1?'checked=checked':'';
                ?>/>
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
                    <div id="ue-editor" type="text/plain" style="width:1024px;height:500px;"></div>
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
   // eval('var learning_content_info = <?php echo json_encode($content_info) ?>;');
    var learning_content_info={
        due_date: '<?php echo $content_info['due_date'] ?>',
        content:  '<?php echo HtmlEncode($content_info['content']) ?>'
    };
    var due_date = '';
    var content = '';
    if(learning_content_info.due_date){
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
    if ($('#ue-editor').length != 0) {
           UE.delEditor('ue-editor');
           UE.getEditor('ue-editor',{
               onready: function () {
                   this.setContent(content);
               },
               toolbars: [
                   [
                       'undo', //撤销
                       'redo', //重做
                       'bold', //加粗
                       'indent', //首行缩进
                       'italic', //斜体
                       'underline', //下划线
                       'strikethrough', //删除线
                       'subscript', //下标
                       'superscript', //上标
                       'formatmatch', //格式刷
                       'selectall', //全选
                       'print', //打印

                       'horizontal', //分隔线
                       'time', //时间
                       'date', //日期
                       'unlink', //取消链接
                       'insertrow', //前插入行
                       'insertcol', //前插入列
                       'mergeright', //右合并单元格
                       'mergedown', //下合并单元格
                       'deleterow', //删除行
                       'deletecol', //删除列
                       'splittorows', //拆分成行
                       'splittocols', //拆分成列
                       'splittocells', //完全拆分单元格
                       'deletecaption', //删除表格标题
                       'inserttitle', //插入标题
                       'mergecells', //合并多个单元格
                       'deletetable', //删除表格

                       'insertparagraphbeforetable', //"表格前插入行"
                       'fontfamily', //字体
                       'fontsize', //字号
                       'paragraph', //段落格式
                       'simpleupload', //单图上传
                       'spechars', //特殊字符
                       'edittable', //表格属性
                       'edittd', //单元格属性
                       'insertimage', //多图上传
                       'insertvideo', //视频
                       'link', //超链接
                       'attachment', //附件
                       'justifyleft', //居左对齐
                       'justifyright', //居右对齐
                       'justifycenter', //居中对齐
                       'justifyjustify', //两端对齐
                       'forecolor', //字体颜色
                       'backcolor', //背景色
                       'insertorderedlist', //有序列表
                       'insertunorderedlist', //无序列表
                       'directionalityltr', //从左向右输入
                       'directionalityrtl', //从右向左输入
                       'rowspacingtop', //段前距
                       'rowspacingbottom', //段后距
                       'pagebreak', //分页
                       'imagenone', //默认
                       'imageleft', //左浮动
                       'imageright', //右浮动
                       'imagecenter', //居中
                       'wordimage', //图片转存
                       'lineheight', //行间距
                       'edittip ', //编辑提示
                       'customstyle', //自定义标题
                       'autotypeset', //自动排版
                       'touppercase', //字母大写
                       'tolowercase', //字母小写
                       'background', //背景
                       'inserttable', //插入表格
                       'cleardoc',  //清空文档
                       'preview' //预览
                   ]
               ]
           });
    }
</script>