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
    if ($fString != "") {

        $fString = str_replace('"', '&quot;', $fString);
        $fString = str_replace('\'', '&#39;', $fString);

    }
    return $fString;
}

$genid = gen_id();
?>
<div class="learn-title">
    <span>
        <?php
        echo $fileInfo['name'];
        ?>
    </span>
</div>
<div style="padding: 5px">
    <span>
        <?php
        echo $fileInfo['content'];
        ?>
    </span>
</div>

<div id="view-comment-area" class="<?php echo $type == 2 ? 'hide':''?>">
    <div class="learn-comment">已阅意见</div>
    <table width="100%" style="
    border: 1px solid #D1F4D5;
">
        <?php
        $i = 0;
        foreach ($readInfo as $item) {
            $i++;
            ?>
            <tr>
                <td class="topicTabletd1"><?php echo $item['username']; ?></td>
                <td class="topicTabletd2">
                    <div class="firstLine">
                        <span class="line_image"></span>
                        <span>阅于&nbsp;&nbsp;<?php echo $item['handle_time']; ?></span>
                    </div>
                    <div class="postContent">
                        <?php echo $item['comment'] == '' ? '已阅' : $item['comment']; ?>
                    </div>
                </td>
            </tr>

        <?php
        }

        if ($i == 0) {
            ?>
            <div class="no-data">暂无已阅意见</div>
        <?php
        }
        ?>

    </table>
    <div class="learn-comment" style="margin-top: 12px">传阅记录</div>
    <ul style="padding: 0px 10px;
color: gray;
font-size: 14px;">
        <?php
        $i = 0;
        foreach ($passInfo as $item) {
            $i++;
            ?>
            <li>
                <?php echo $item['from_user']; ?>于
               <span class="gray"><?php echo $item['create_time']; ?></span>  交由<span class="gray"><?php echo $item['to_user']; ?></span>阅读
            </li>

        <?php
        }

        if ($i == 0) {
            ?>
            <div class="no-data">暂无已阅意见</div>
        <?php
        }
        ?>

    </ul>
</div>
<div id="create-comment-area" class="<?php echo $opt == 'view' ? 'hide' : '' ?>">
    <div>
        <textarea id="file-comment" placeholder="请输入阅读意见" style="width: 800px;height: 200px"></textarea>
    </div>
    交由
    <div id="continue-reader"></div>
    继续阅读
    </br>
    <p style="display: none">
        <input type="checkbox" id="needSendMessage" >发送短信通知阅读人
        </br>
    </p>
    <div class="new-button"
         onclick="og.file.hasRead(<?php echo $fileInfo['id'] . ",'". $fileInfo['name']."'" .($readId?"," . $readId:'')  ?>)">
        已阅
    </div>
    <span class="red" id="comment-error"></span>
</div>
<script>

    // 阅读人初始化
    eval('var dutyUserInfo = <?php echo json_encode($userInfo);?>');
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
    new Ext.form.ComboBox
    ({
        tpl: '<tpl for="."><div class="x-combo-list-item"><span><input  type="checkbox" {[values.check?"checked":""]}  value="{[values.id]}" /></span><span >{name}</span></div></tpl>',
        id: "continueReader",
        emptyText: '',
        editable: false,//默认为true，false为禁止手写和联想功能
        store: store,
        mode: 'local',//指定数据加载方式，如果直接从客户端加载则为local，如果从服务器断加载 则为remote.默认值为：remote
        typeAhead: true,
        triggerAction: 'all',
        valueField: 'name',
        displayField: 'name',
        selectOnFocus: true,
        disable: true,
        renderTo: "continue-reader",
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
</script>




