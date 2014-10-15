og.car = og.car || {};
og.car = {
    create: function () {
        var url = og.getUrl('carmanage', 'create_car_apply');
        og.openLink(url, {post: {}});
    },
    submitHandle: function (id) {
        var param = {};
        param.agree = og.common.getCheckboxValue('handle');
        param.car = Ext.getCmp('car-can-use-select').getValue();
        var result = this.validateHandle(param);
        if (result == ''){
            $('#car-error').addClass('hide');
        } else {
            $('#car-error').html(result);
            $('#car-error').removeClass('hide');
            return;
        }
        var url = og.getUrl('carmanage', 'submit_handle_apply', {id: id});
        og.openLink(url, {
            method: 'POST',
            post: param,
            callback: function (success, data) {
                if (success && !data.errorCode) {
                    Ext.MessageBox.alert("提示", "派车成功！");
                } else {
                    if (!data.errorMessage || data.errorMessage == '')
                        og.err(lang("error handle_car_apply"));
                }
            },
            scope: this
        });

    },

    validateHandle: function (param) {
        if (param.agree == '') {
            return '请选择同意或者不同意';
        } else if (param.agree == '1' && param.car == ''){
            return  '请选择车辆';
        }
        return '';
    },

    submit: function (id) {
        var param = {};
        param.carUser = $.trim($('#car-users').val());
        param.place = Ext.getCmp('car-place-select').getValue()
        param.placeDetail = $('#car-place-detail').val();
        param.reason = $.trim($('#car-detail').val());
        param.beginDate = $('#car-begin').val();
        param.endDate = $('#car-end').val();
        param.beginTime = Ext.getCmp('car-begin-time-select').getValue();
        param.endTime =  Ext.getCmp('car-end-time-select').getValue();
        if (this.validateInput(param) != '') {
            $('#car-error').html(this.validateInput(param));
            $('#car-error').removeClass('hide');
        } else {
            // 编辑
            if (id) {
                var url = og.getUrl('carmanage', 'edit_apply', {id: id});
                og.openLink(url, {
                    method: 'POST',
                    post: param,
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "修改成功！");
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error add_car_apply"));
                        }
                    },
                    scope: this
                });
            } else {
                var url = og.getUrl('carmanage', 'add_apply', {opt: 'add'});
                og.openLink(url, {
                    method: 'POST',
                    post: param,
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.getCmp('carmanage-panel').back();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error add_car"));
                        }
                    },
                    scope: this
                });
            }
            $('#car-error').addClass('hide');
        }
    },
    validateInput: function(param){
        if (param.carUser == '') {
            return '请填写用车人员';
        } else if (param.place == '') {
            return '请选择用车地点';
        }  else if (param.placeDetail == '') {
            return '请填写目的地';
        } else if (param.reason== '') {
            return '请填写用车原因';
        } else if (param.beginDate == '') {
            return '请选择开始时间';
        }else if (param.beginTime == '') {
            return '请选择开始时间';
        } else if (param.endDate == '') {
            return '请选择结束时间';
        }  else if (param.endTime == '') {
            return '请选择结束时间';
        } else if (new Date(param.beginDate) > new Date(param.endDate)) {
            return '结束时间要晚于开始时间';
        } else if (
            param.beginDate == param.endDate
            && +param.beginTime>+param.endTime){
            return '结束时间要晚于开始时间';
        }
        return '';
    },
    edit: function (id) {
        var url = og.getUrl('carmanage', 'create_car_apply', {opt: 'edit', id: id});
        og.openLink(url, {post: {}});
    },
    view: function (id) {
        var url = og.getUrl('carmanage', 'create_car_apply', {opt: 'view', id: id});
        og.openLink(url, {post: {}});
    },// 撤回申请
    undo: function (id) {
        Ext.MessageBox.confirm('', '您确认撤回该用车申请？', function (btn) {
            if (btn == 'yes') {
                var url = og.getUrl('carmanage', 'undo_apply', {id: id});
                og.openLink(url, {
                    method: 'POST',
                    post: {},
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "修改成功！");
                            Ext.getCmp('carmanage-panel').reload();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error undo_car_apply"));
                        }
                    },
                    scope: this
                });
            }
        });
    },
    returnCar: function (id) {

        Ext.MessageBox.confirm('', '您确认车辆已经使用完成？', function (btn) {
            if (btn == 'yes') {
                var url = og.getUrl('carmanage', 'return_car', {id: id});
                og.openLink(url, {
                    method: 'POST',
                    post: {},
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "操作成功！");
                            Ext.getCmp('carmanage-panel').reload();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error return_car_apply"));
                        }
                    },
                    scope: this
                });
            }
        });
    },

    handleApply: function (id, status) {
        var url = og.getUrl('carmanage', 'handle_car_apply', {id: id});
        og.openLink(url, {post: {}});
    }
}