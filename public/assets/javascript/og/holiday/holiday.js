/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.holiday = og.holiday || {};
og.holiday = {
    create: function () {
        var url = og.getUrl('qingxiaojia', 'write_holiday_page');
        og.openLink(url, {post: {}});
    },
    edit: function (id) {
        var url = og.getUrl('qingxiaojia', 'write_holiday_page', {opt: 'edit', id: id});
        og.openLink(url, {post: {}});
    },
    view: function (id) {
        var url = og.getUrl('qingxiaojia', 'write_holiday_page', {opt: 'view', id: id});
        og.openLink(url, {post: {}});
    },
	handle: function (id) {
        var url = og.getUrl('qingxiaojia', 'write_holiday_page', {opt: 'handle', id: id});
        og.openLink(url, {post: {}});
    },
    submit: function (id) {
		
        var param = {};
        param.reason = Ext.getCmp('holiday-reason-select').getValue();
        param.detail = $('#holiday-detail').val();
        param.beginDate = $('#holiday-begin').val();
        param.endDate = $('#holiday-end').val();
		param.approvalBegin = $('#approval-begin').val();
		param.approvalEnd = $('#approval-end').val();
		param.isHandled = $('#isHandled').val();
		param.apply_status = $('#apply_status').val();
		param.user_id = $('#user_id').val();
		if($('#opt').val() == 'handle'){//审批
		//	if(!($("#radio1").is(':checked') || $("#radio2").is(':checked'))){
		//		$('#holiday-error').html('请选择同意或不同意.');
		//		$('#holiday-error').removeClass('hide');
		//		return;
		//	}

			if($("#radio1").is(':checked')){//同意休假申请
				og.holiday.agreeApply(id, param.apply_status, param.isHandled,param.beginDate,param.endDate,param.approvalBegin,param.approvalEnd,param.user_id);
			}
			if($("#radio2").is(':checked')){//不同意休假申请
				og.holiday.rejectApply(id, param.apply_status, param.isHandled);
			}	
			
			return;
		}
        if (this.validateInput(param) != '') {
            $('#holiday-error').html(this.validateInput(param));
            $('#holiday-error').removeClass('hide');
        } else {
            // 编辑
            if (id) {
                var url = og.getUrl('qingxiaojia', 'edit_apply', {id: id});
                og.openLink(url, {
                    method: 'POST',
                    post: param,
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "修改成功！");
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error add_holiday"));
                        }
                    },
                    scope: this
                });
            } else {
                var url = og.getUrl('qingxiaojia', 'add_apply', {opt: 'add'});
                og.openLink(url, {
                    method: 'POST',
                    post: param,
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.getCmp('qingxiaojia-panel').back();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error add_holiday"));
                        }
                    },
                    scope: this
                });
            }
            $('#holiday-error').addClass('hide');
        }
    },
    validateInput: function (param) {
        if (param.reason == '') {
            return '请选择请假事由'
        } else if (param.beginDate == '') {
            return '请选择开始时间'
        } else if (param.endDate == '') {
            return '请选择结束时间'
        } else if (new Date(param.beginDate) > new Date(param.endDate)) {
            return '结束时间要晚于开始时间';
        }
        return '';
    },

    dateClick: function (text, ins) {
    },
    // 同意休假申请
    agreeApply: function (id, status, isHandled,start,end,approveBegin,approveEnd,user_id) {
		//alert('id='+id+','+'status='+status+','+'isHandled='+isHandled+','+'start='+start+','+'end='+end+','+'approveBegin='+approveBegin+','+'approveEnd='+approveEnd+',');

	   Ext.MessageBox.confirm('', '您确认同意该休假申请吗？', function (btn) {
            if (btn == 'yes') {
                var url = og.getUrl('qingxiaojia', 'agree_apply', {id: id, status: status, isHandled: isHandled,user_id:user_id});
                og.openLink(url, {
                    method: 'POST',
                    post: {start:start,end:end,approveBegin:approveBegin,approveEnd:approveEnd},
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "修改成功！");
                            Ext.getCmp('qingxiaojia-panel').reload();
                            Ext.getCmp('overview-panel').reload();
							Ext.getCmp('qingxiaojia-panel').back();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error agree_holiday"));
                        }
                    },
                    scope: this
                });
            }
        });
    },

    // 不同意休假申请
    rejectApply: function (id, status, isHandled) {
        Ext.MessageBox.confirm('', '您确认不同意该休假申请吗？', function (btn) {
            if (btn == 'yes') {
                var url = og.getUrl('qingxiaojia', 'reject_apply', {id: id, status: status, isHandled: isHandled});
                og.openLink(url, {
                    method: 'POST',
                    post: {},
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "修改成功！");
                            Ext.getCmp('qingxiaojia-panel').reload();
							Ext.getCmp('qingxiaojia-panel').back();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error reject_holiday"));
                        }
                    },
                    scope: this
                });
            }
        });
    },

    // 撤回申请
    undo: function (id) {
        Ext.MessageBox.confirm('', '您确认撤回该休假申请吗？', function (btn) {
            if (btn == 'yes') {
                var url = og.getUrl('qingxiaojia', 'undo_apply', {id: id});
                og.openLink(url, {
                    method: 'POST',
                    post: {},
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "修改成功！");
                            Ext.getCmp('qingxiaojia-panel').reload();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error undo_holiday"));
                        }
                    },
                    scope: this
                });
            }
        });
    },

    getApproveRecord: function (records){
        var str = '';
        for(var i=0;i<records.length;i++) {
            var item = records[i];
            if (item.is_agree =='1') {
                str += item.username + '  同意时间'+item.approve_begin_time+'到' +item.approve_end_time + '</br>'
            } else {
                str += item.username + '审批不同意'
            }
        }
        return str;
    },

    // 销假
    cancelHoliday: function (id) {
        Ext.MessageBox.confirm('', '您确认提前销假吗？', function (btn) {
            if (btn == 'yes') {
                var url = og.getUrl('qingxiaojia', 'cancel_apply', {id: id});
                og.openLink(url, {
                    method: 'POST',
                    post: {},
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "修改成功！");
                            Ext.getCmp('qingxiaojia-panel').reload();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error undo_holiday"));
                        }
                    },
                    scope: this
                });
            }
        });
    }
};

