<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/new/taskList.js');
require_javascript("og/jquery.min.js");
require_javascript("og/ObjectPicker.js");
require_javascript("og/DateField.js");
$genid = gen_id();
?>

<div class="holiday-create-div">
    <form>
        <table width="100%" style="text-align: left">
            <tr>
                <td class="htd1">请假事由：</td>
                <td>
                    <div id="holiday-reason"></div>
                </td>
            </tr>
            <tr>
                <td class="htd1">
                    原因：
                </td>
                <td>&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea id="holiday-detail"></textarea>
                </td>
            </tr>
            <tr>
                <td class="htd1">
                    开始时间：
                </td>
                <td>
                    <div id="holiday-begin-date"></div>
                </td>
            </tr>
            <tr>
                <td class="htd1">
                    结束时间：
                </td>
                <td>
                    <div id="holiday-end-date"></div>
                </td>
            </tr>
			<tr class="info">
				<td colspan="2"> <div class="learn-comment" style="margin-top: 12px">审批记录</div></td>
			</tr>
			<tr class="info">
				<td colspan="2">
					<div id="approveInfo" style="color:#999;"></div>
				</td>
			</tr>
			<tr id="add-holiday-handle">
				<td>
					<input id="radio1" type="radio" style="border:0px;" onclick="agree();" value="同意" name="isAgree"/>
					<label style="width:65px;float:right;" for="radio1" >同意</label>
				</td>
				<td>
					<input id="radio2"type="radio" style="border:0px;" onclick="disagree();" checked="checked" value="不同意" name="isAgree"/>
					<label  style="width:285px;float:right;" for="radio2">不同意</label>
				</td>
			</tr>
			<tr id="approval_begin_tr">
				<td class="htd1">
					开始时间：
				</td>
				<td>
					 <div id="approval-begin-date"></div>
				</td>
			</tr>
			<tr id="approval_end_tr">
                <td class="htd1">
                    结束时间：
                </td>
                <td>
                    <div id="approval-end-date"></div>
                </td>
            </tr>
			<tr id="add-holiday-submit" >
                <td colspan="2">
					<br/>
                    <div class="new-button" onclick="og.holiday.submit(<?php
                    if(isset($holidayInfo)){
                        echo $holidayInfo['id'];
                    }
                    ?>)">提交</div>

                    <span class="error hide" id="holiday-error"></span>
					<input id="opt" type="hidden" value="" />
					<input id="isHandled" type="hidden" value="" />
					<input id="apply_status" type="hidden" value="" />	
					<input id="user_id" type="hidden" value="" />	
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
            ['1', '公差'],
            ['2', '病假'],
            ['3', '事假'],
            ['5', '年休假'],
            ['4', '其它']
        ]
    });

    new Ext.form.ComboBox({
        renderTo: 'holiday-reason',
        name: 'holiday-reason-select',
        id: 'holiday-reason-select',
        value: '',
        store: store0,
        displayField: 'name',
        valueField: 'id',
        hiddenName: 'holiday-reason',
        typeAhead: true,
        mode: 'local',
        triggerAction: 'all',
        selectOnFocus: true,
        width: 100,
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
        renderTo: 'holiday-begin-date',
        name: 'holiday-begin',
        id: 'holiday-begin',
        value: '',
        editable: false,
        //onSelect: .dateClick,
        readOnly: true
    });
    new og.DateField({
        renderTo: 'holiday-end-date',
        name: 'holiday-end',
        id: 'holiday-end',
        value: '',
        editable: false,
        readOnly: true
    });
	new og.DateField({
        renderTo: 'approval-begin-date',
        name: 'approval-begin',
        id: 'approval-begin',
        value: '',
        editable: false,
        //onSelect: .dateClick,
        readOnly: true
    });
	new og.DateField({
        renderTo: 'approval-end-date',
        name: 'approval-end',
        id: 'approval-end',
        value: '',
        editable: false,
        readOnly: true
    });
    eval('var holidayEditInfo = <?php echo json_encode($holidayInfo)?>');
    eval('var holidayApproveInfo = <?php echo json_encode($holidayApproveInfo)?>');
    eval('var holidayEditOpt = "<?php echo $opt;?>"');
    //console.log(holidayApproveInfo);
    if (holidayEditInfo) {
        Ext.getCmp('holiday-reason-select').setValue(holidayEditInfo.reason);
        Ext.getCmp('holiday-end').setValue(holidayEditInfo.apply_end_date);
		Ext.getCmp('holiday-begin').setValue(holidayEditInfo.apply_begin_date);
		Ext.getCmp('approval-end').setValue(holidayEditInfo.apply_end_date);
		Ext.getCmp('approval-begin').setValue(holidayEditInfo.apply_begin_date);
        $('#holiday-detail').html(holidayEditInfo.detail);
		$('#isHandled').val(holidayEditInfo.isHandled);
		$('#apply_status').val(holidayEditInfo.apply_status);
		$('#user_id').val(holidayEditInfo.user_id);
		
		$('#opt').val(holidayEditOpt);holidayApproveInfo==null
		$('#approveInfo').html(((!holidayApproveInfo || holidayApproveInfo.length ==0)?"没有记录": og.holiday.getApproveRecord(holidayApproveInfo)));
		//将','换成'' 一次只能去掉一个
		$('#approveInfo').html($('#approveInfo').html().replace(',',''));
		$('#approveInfo').html($('#approveInfo').html().replace(',',''));
        if (holidayEditOpt != 'edit') {
            $('#add-holiday-submit').addClass('hide');
        }else{
            $('#add-holiday-submit').removeClass('hide');
/*            $('#add-holiday-submit').onclick = (function(id){
                return function(){
                    console.log('new click');
                }
            })(holidayEditInfo.id);*/
        }
		//隐藏审批日期
		$('#approval_begin_tr').addClass('hide');
		$('#approval_end_tr').addClass('hide');
		
		if(holidayEditOpt != 'handle'){
			$('#add-holiday-handle').addClass('hide');
		}else{
			$('#holiday-begin-date img').remove();
			$('#holiday-end-date img').remove();
			$('#add-holiday-submit').removeClass('hide');
			$('.info').removeClass('hide');
		}
    }else{
		$('#add-holiday-handle').addClass('hide');
		$('#approval_begin_tr').addClass('hide');
		$('#approval_end_tr').addClass('hide');
		$('.info').addClass('hide');
	}
	//同意:显示审批日期
	function agree(){
		if(holidayEditInfo.apply_status == 8){
		}else{
			$('#approval_begin_tr').removeClass('hide');
			$('#approval_end_tr').removeClass('hide');
		}
			

		
	}
	//不同意:隐藏审批日期
	function disagree(){
		$('#approval_begin_tr').addClass('hide');
		$('#approval_end_tr').addClass('hide');
	}
</script>