<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/new/taskList.js');
require_javascript("og/jquery.min.js");
require_javascript("og/ObjectPicker.js");
require_javascript("og/DateField.js");
require_javascript("og/Spinner.js");
require_javascript("og/common.js");
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
                <td colspan="2"><input id="car-users" ></td>
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
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr>
                <td class="card1" >
                    <input type="radio" id="agree" name="handle" value="1">同意
                </td>
                <td >
                    <div id="car-can-use" class="hide"></div>
                    <div id="no-car-can-use" class="hide red">当前没有可用的车辆</div>
                </td>
                <td class="" >
                    <input type="radio" id="disagree" name="handle" value="0">不同意
                </td>
            </tr>
            <tr>
                <td colspan="3">&nbsp;</td>
            </tr>
            <tr id="add-car-submit">
                <td colspan="3">
                    <div class="new-button" onclick="og.car.submitHandle(<?php
                        echo $carApplyInfo['id'];
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
            ['01', '1点'],
            ['02', '2点'],
            ['03', '3点'],
            ['04', '4点'],
            ['05', '5点'],
            ['06', '6点'],
            ['07', '7点'],
            ['08', '8点'],
            ['09', '9点'],
            ['10', '10点'],
            ['11', '11点'],
            ['12', '12点'],
            ['13', '13点'],
            ['14', '14点'],
            ['15', '15点'],
            ['16', '16点'],
            ['17', '17点'],
            ['18', '18点'],
            ['19', '19点'],
            ['20', '20点'],
            ['21', '21点'],
            ['22', '22点'],
            ['23', '23点'],
            ['24', '24点']
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

    eval('var carInfo = <?php echo json_encode($carInfo)?>');
    var data= [];
    if (carInfo){
        for (var i =0;i<carInfo.length;i++) {
            data[data.length] = [carInfo[i].id, carInfo[i].car_number]
        }
        var storeCar = new Ext.data.SimpleStore
        ({
            fields: ["id", "name"],
            data: data
        });
        new Ext.form.ComboBox({
            renderTo: 'car-can-use',
            name: 'car-can-use',
            id: 'car-can-use-select',
            value: '',
            store: storeCar,
            displayField: 'name',
            valueField: 'id',
            hiddenName: 'car-begin-time',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            selectOnFocus: true,
            width: 100,
            tabIndex: '150',
            emptyText: ( '请选择车辆...'),
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


    } else {
        $('#agree').attr('disable',true)
    }

    $('#agree').click(function(){
        if ($(this).is(':checked')) {
            eval('var carInfo = <?php echo json_encode($carInfo)?>');
            if (carInfo.length == 1){
               $('#no-car-can-use').removeClass('hide');
            } else {
                $('#car-can-use').removeClass('hide');
            }
        } else {
            $('#car-can-use').addClass('hide');
        }
    });
    $('#disagree').click(function(){
        if ($(this).is(':checked')) {
            $('#car-can-use').addClass('hide');
        } else {
            $('#car-can-use').removeClass('hide');
        }
    });
</script>