<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/new/taskList.js');
require_javascript("og/jquery.min.js");
require_javascript("og/ObjectPicker.js");
require_javascript("og/DateField.js");
require_javascript("og/Spinner.js");
require_javascript("og/SpinnerField.js");
require_javascript("og/DateTimeField.js");
$genid = gen_id();
?>

<div class="holiday-create-div">
    <form>
        <table width="100%" style="text-align: left">
            <tr>
                <td class="card1">
                    用车人员：
                </td>
                <td colspan="2"><input id="car-users"></td>
            </tr>

            <tr>
                <td class="card1">用车地点：</td>
                <td>
                    <div id="car-place"></div>
                </td>
            </tr>
            <tr>
                <td class="card1">目的地：</td>
                <td colspan="2">
                    <input id="car-place-detail">
                </td>
            </tr>
            <tr>
                <td class="card1">
                    用车原因：
                </td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="3">
                    <textarea id="car-detail"></textarea>
                </td>
            </tr>
            <tr>
                <td class="card1">
                    开始时间：
                </td>
                <td>
                    <div id="car-begin-date"></div>
                </td>
                <td>
                    <div id="car-begin-time"></div>
                </td>
                </td>
            </tr>
            <tr>
                <td class="card1">
                    结束时间：
                </td>
                <td>
                    <div id="car-end-date"></div>
                </td>
                <td>
                    <div id="car-end-time"></div>
                </td>
            </tr>
            <tr id="add-car-submit">
                <td colspan="3">
                    <div class="new-button" onclick="og.car.submit(<?php
                    if (isset($carApplyInfo)) {
                        echo $carApplyInfo['id'];
                    }
                    ?>)">提交
                    </div>

                    <span class="error hide" id="car-error"></span>
                </td>
            </tr>
        </table>
    </form>
</div>
<script>
    var store0 = new Ext.data.SimpleStore
    ({
        fields: ["id", "name"],
        data: [
            ['1', '县内'],
            ['2', '市内'],
            ['3', '市外']
        ]
    });

    var storeTime = new Ext.data.SimpleStore
    ({
        fields: ["id", "name"],
        data: [
            ['05', '5点'],
            ['05:30', '5点30'],
            ['06', '6点'],
            ['06:30', '6点30'],
            ['07', '7点'],
            ['07:30', '7点30'],
            ['08', '8点'],
            ['08:30', '8点30'],
            ['09', '9点'],
            ['09:30', '9点30'],
            ['10', '10点'],
            ['10:30', '10点30'],
            ['11', '11点'],
            ['11:30', '11点30'],
            ['12', '12点'],
            ['12:30', '12点30'],
            ['13', '13点'],
            ['13:30', '13点30'],
            ['14', '14点'],
            ['14:30', '14点30'],
            ['15', '15点'],
            ['15:30', '15点30'],
            ['16', '16点'],
            ['16:30', '16点30'],
            ['17', '17点'],
            ['17:30', '17点30'],
            ['18', '18点'],
            ['18:30', '18点30'],
            ['19', '19点']
        ]
    });
    new Ext.form.ComboBox({
        renderTo: 'car-place',
        name: 'car-place-select',
        id: 'car-place-select',
        value: '',
        store: store0,
        displayField: 'name',
        valueField: 'id',
        hiddenName: 'car-place',
        typeAhead: true,
        mode: 'local',
        triggerAction: 'all',
        selectOnFocus: true,
        width: 150,
        tabIndex: '150',
        emptyText: ( '请选择...'),
        valueNotFoundText: '',
        listeners: {
            expand: function () {
                this.list.setWidth(this.width);
            },
            click: function () {

            }
        },
        editable: false,
        readOnly: true
    });
    new og.DateField({
        renderTo: 'car-begin-date',
        name: 'car-begin',
        id: 'car-begin',
        value: '',
        editable: false,
        //onSelect: .dateClick,
        readOnly: true
    });
    new og.DateField({
        renderTo: 'car-end-date',
        name: 'car-end',
        id: 'car-end',
        value: '',
        editable: false,
        readOnly: true
    });
    new Ext.form.ComboBox({
        renderTo: 'car-begin-time',
        name: 'car-begin-time',
        id: 'car-begin-time-select',
        value: '',
        store: storeTime,
        displayField: 'name',
        valueField: 'id',
        hiddenName: 'car-begin-time',
        typeAhead: true,
        mode: 'local',
        triggerAction: 'all',
        selectOnFocus: true,
        width: 100,
        tabIndex: '150',
        emptyText: ( '请选择时间...'),
        valueNotFoundText: '',
        listeners: {
            expand: function () {
                this.list.setWidth(this.width);
            },
            click: function () {

            }
        },
        editable: false,
        readOnly: true
    });

    new Ext.form.ComboBox({
        renderTo: 'car-end-time',
        name: 'car-end-time',
        id: 'car-end-time-select',
        value: '',
        store: storeTime,
        displayField: 'name',
        valueField: 'id',
        hiddenName: 'car-end-time',
        typeAhead: true,
        mode: 'local',
        triggerAction: 'all',
        selectOnFocus: true,
        width: 100,
        tabIndex: '150',
        emptyText: ( '请选择时间...'),
        valueNotFoundText: '',
        listeners: {
            expand: function () {
                this.list.setWidth(this.width);
            },
            click: function () {
            }
        },
        editable: false,
        readOnly: true
    });

    eval('var carEditInfo = <?php echo json_encode($carApplyInfo)?>');
    eval('var carEditOpt = "<?php echo $opt;?>"');
    if (carEditInfo) {
        var beginDate = carEditInfo.begin_time.split(' ')[0];
        var beginTime = carEditInfo.begin_time.split(' ')[1].split(':')[0];
        var endDate = carEditInfo.end_time.split(' ')[0];
        var endTime = carEditInfo.end_time.split(' ')[1].split(':')[0];
        $('#car-users').val(carEditInfo.car_users);
        $('#car-place-detail').val(carEditInfo.place_detail);
        Ext.getCmp('car-place-select').setValue(carEditInfo.place);
        Ext.getCmp('car-end').setValue(beginDate);
        Ext.getCmp('car-begin').setValue(endDate);
        Ext.getCmp('car-begin-time-select').setValue(beginTime);
        Ext.getCmp('car-end-time-select').setValue(endTime);
        $('#car-detail').html(carEditInfo.reason);
        if (carEditOpt!= 'edit') {
            $('#add-car-submit').addClass('hide');
        }else{
            $('#add-car-submit').removeClass('hide');
        }
    }
</script>