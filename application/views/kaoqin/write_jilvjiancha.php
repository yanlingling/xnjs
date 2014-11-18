<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/kaoqinducha/kaoqinducha.js');
require_javascript('og/common.js');
require_javascript('og/treeCombo.js');
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
<div class="duty-title"><span id="cur-jilv-date"></span>&nbsp;&nbsp;机关人员工作纪律检查记录</div>
<div style="text-align: center;padding: 10px">
    <table border="1" class="duty-table" style="width: 880px">
        <tr>
            <td  class='verticalM bolder'>检查时间</td>
            <td colspan="2" class='verticalM'><input id="jilvjiancha-time" <?php if ($opt == 'view') {echo 'readonly';}?> placeholder="请输入检查时间"/></td>
            <td>检查人</td>
            <td colspan="5">
                <div ><input id="jilvjiancha-user" <?php if ($opt == 'view') {echo 'readonly';}?> placeholder="请输入检查人"/></div>
            </td>
        </tr>
        <tr>
            <td rowspan="5" class='verticalM bolder'>在     岗     情     况</td>
            <td rowspan="2" class='verticalM'>因事外出</td>
            <td>公务</td>
            <td colspan="6">
                <div id="work-status1" class="not-work-reason"></div>
            </td>
        </tr>
        <tr>
            <td>病事假、公休假</td>
            <td colspan="6">
                <div id="work-status2" class="not-work-reason"></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">擅自离岗（与考勤状态不符）</td>
            <td colspan="6">
                <div id="work-status3" class="not-work-reason"></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">上网炒股、网购、网聊、玩游戏</td>
            <td colspan="6">
                <div id="work-status4" class="not-work-reason"></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">从事与工作无关的其他活动</td>
            <td colspan="6">
                <div id="work-status5" class="not-work-reason"></div>
            </td>
        </tr>



        <tr id='depart-group' >
            <td rowspan="4" class='verticalM bolder'>环   境    卫     生</td>
            <td class='verticalM'>最佳科室</br><span class="gray">（选择1个）</span></td>
            <?php
            foreach ($departInfo as $item) {
                echo "<td><input type='radio' value='" . $item[depart_id] . "' name='most-clean-depart'>$item[depart_name]</td>";
            }
            ?>
        </tr>
        <tr id='' >
            <td class='verticalM'>最差科室</br><span class="gray">（选择1个）</span></td>
            <?php
            foreach ($departInfo as $item) {
                echo "<td><input type='radio' value='" . $item[depart_id] . "' name='least-clean-depart'>$item[depart_name]</td>";
            }
            ?>
        </tr>
        <tr id='floor-group' >
            <td class='verticalM'>最佳责任区</br><span class="gray">（选择1个）</span></td>
            <td><input type="radio" value="2" name="most-clean-floor">二楼</td>
            <td><input type="radio" value="3" name="most-clean-floor">三楼</td>
            <td><input type="radio" value="4" name="most-clean-floor">四楼</td>
            <td><input type="radio" value="6" name="most-clean-floor">三楼会议室楼</td>
            <td><input type="radio" value="5" name="most-clean-floor">五楼</td>
            <td>&nbsp;</td>
        </tr>

        <tr id='' >
            <td class='verticalM'>最差责任区</br><span class="gray">（选择1个）</span></td>
            <td><input type="radio" value="2" name="least-clean-floor">二楼</td>
            <td><input type="radio" value="3" name="least-clean-floor">三楼</td>
            <td><input type="radio" value="4" name="least-clean-floor">四楼</td>
            <td><input type="radio" value="6" name="least-clean-floor">三楼会议室楼</td>
            <td><input type="radio" value="5" name="least-clean-floor">五楼</td>
            <td>&nbsp;</td>
        </tr>

        <tr>
            <td class='verticalM bolder'>其               它</td>
            <td colspan="7" class='verticalM'>
                <textarea id="other-detail"></textarea>
            </td>
        </tr>
        <tr>
            <td colspan="8" class="<?php echo $opt=='view'?'hide':'';?>">
                <span class="new-button" onclick="og.jilvjiancha.saveClickHandler(<?php echo '\''.$opt.'\'';
                if($opt == 'edit'){
                    echo ','.$id;
                }?>)">提交</span>
                <span class="red" id="error-tip"></span>
            </td>
        </tr>
    </table>
</div>


<script type="text/javascript">
eval('var jilvUserInfo = <?php echo json_encode($userInfo);?>');
var jilvUserData = [];
eval('var jilvDetailInfo = <?php echo json_encode($jilv_info)?>');
eval('var jilvDetailOpt = "<?php echo $opt?>"');
if(jilvDetailInfo){
    var mostClean = jilvDetailInfo['most_clean_department'];
    $('input[name="most-clean-depart"][value='+mostClean+']').attr('checked',true);
    var leastClean = jilvDetailInfo['least_clean_department'];
    $('input[name="least-clean-depart"][value='+leastClean+']').attr('checked',true);
    var mostCleanFloor = jilvDetailInfo['most_clean_floor'];
    var leastCleanFloor = jilvDetailInfo['least_clean_floor'];
    $('input[name="most-clean-floor"][value='+mostCleanFloor+']').attr('checked',true);
    $('input[name="least-clean-floor"][value='+leastCleanFloor+']').attr('checked',true);
    $('#other-detail').html(jilvDetailInfo.other_content);
    $('#jilvjiancha-time').val(jilvDetailInfo.jiancha_time);
    $('#jilvjiancha-user').val(jilvDetailInfo.onDutyUser);

}

for (var i = 0; i < jilvUserInfo.length; i++) {
    var temp = [jilvUserInfo[i].id, jilvUserInfo[i].username];
    jilvUserData.push(temp);
}
//静态绑定数据

Ext.namespace('Ext.exampledata');

for (var i = 1; i <= 5; i++) {


    var thisData = $.extend(true, [], jilvUserData);
    var emptyStr = [];
    var selected = [];
    if (jilvDetailInfo) {
        var checked = jilvDetailInfo['work_status' + i].split(',');
        for (var k = 0; k < thisData.length; k++) {
            for (var j = 0; j < checked.length; j++) {
                if (thisData[k][0] == checked[j]) {
                    thisData[k][2] = 1;
                    emptyStr.push(thisData[k][1]);
                    selected.push(thisData[k][0]);
                }
            }
        }
    }

    var store = new Ext.data.SimpleStore
    ({
        fields: ["id", "name", 'check'],
        data: $.extend(true, [], thisData)
    });
    if(jilvDetailOpt !='view'){
        new Ext.form.ComboBox
        ({
            tpl: '<tpl for="."><div class="x-combo-list-item"><span><input class="work-status-' + i + '-users"  type="checkbox" {[values.check?"checked":""]}  value="{[values.id]}" /></span><span >{name}</span></div></tpl>',
            id: "work-status-" + i,
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
            renderTo: "work-status" + i,
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
                //this.fireEvent('select', this, record, index);
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
    }else{
        $('#work-status'+i).html(emptyStr.join('、'));
    }

}



<?php
/*    if(isset($jilv_info)){*/

?>


<?php
   /* }*/
?>
</script>

