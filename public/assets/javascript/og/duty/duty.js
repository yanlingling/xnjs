/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.duty = og.duty || {};
og.duty = {
    writeDuty: function (dutyHasBeenCreate, id) {
        if (dutyHasBeenCreate) {
            var url = og.getUrl('zhibanzhang', 'write_duty', {opt: 'edit', id: id});
            og.openLink(url, {post: {}});
        } else {
            var url = og.getUrl('zhibanzhang', 'write_duty');
            og.openLink(url, {post: {}});
        }

    },

    viewDuty: function (id) {
        var url = og.getUrl('zhibanzhang', 'write_duty', {opt: 'view', id: id});
        og.openLink(url, {post: {}});
    },

    saveDutyClickHandler: function (type,isCommit,id) {
        var params = {};
        var opt = type || 'add';

        params.morningReason1 = Ext.getCmp('morning-reason-1').getValue().join(',');
        params.morningReason2 = Ext.getCmp('morning-reason-2').getValue().join(',');
        params.morningReason3 = Ext.getCmp('morning-reason-3').getValue().join(',');
        params.morningReason4 = Ext.getCmp('morning-reason-4').getValue().join(',');
        params.morningReason5 = Ext.getCmp('morning-reason-5').getValue().join(',');

        params.noonReason1 = Ext.getCmp('noon-reason-1').getValue().join(',');
        params.noonReason2 = Ext.getCmp('noon-reason-2').getValue().join(',');
        params.noonReason3 = Ext.getCmp('noon-reason-3').getValue().join(',');
        params.noonReason4 = Ext.getCmp('noon-reason-4').getValue().join(',');
        params.noonReason5 = Ext.getCmp('noon-reason-5').getValue().join(',');

        params.mostCleanDepart = og.common.getCheckboxValue('most-clean-depart');
        params.mostCleanFloor = og.common.getCheckboxValue('most-clean-floor');
        if (params.mostCleanDepart.split(',').length != 2) {
            this.showError('请选择两个卫生最佳科室');
            return;
        }
        if (params.mostCleanFloor.split(',').length != 2) {
            this.showError('请选择两个卫生最佳责任区');
            return;
        }
        params.safeContent = $('#safe-detail').val();
        params.savingContent = $('#saving-detail').val();
        params.zuofengContent = $('#zuofeng-detail').val();
        params.adviceContent = $('#advice-detail').val();
        params.otherContent = $('#other-detail').val();
        params.isCommit = isCommit;
        var me = this;
        if (isCommit) {
            Ext.MessageBox.confirm('', '您确认值班长日志已经填写完成了吗？提交以后将不能修改', function (btn) {
                    if (btn == 'yes') {
                        me.doOpration(opt,id,params);
                    }
            });
        } else {
            this.doOpration(opt,id,params);
        }
    },


    doOpration: function (opt,id,params){
        if (opt == 'add') {
            var url = og.getUrl('zhibanzhang', 'add_duty', {opt: opt});
            og.openLink(url, {
                method: 'POST',
                post: params,
                callback: function (success, data) {
                    if (success && !data.errorCode) {
                        Ext.getCmp('zhibanzhang-panel').back();
                    } else {
                        if (!data.errorMessage || data.errorMessage == '')
                            og.err(lang("error add_duty"));
                    }
                },
                scope: this
            });
        } else {
            var url = og.getUrl('zhibanzhang', 'edit_duty', {opt: opt,id:id});
            og.openLink(url, {
                method: 'POST',
                post: params,
                callback: function (success, data) {
                    if (success && !data.errorCode) {
                        Ext.MessageBox.alert("提示", "操作成功！");
                        $('#error-tip').hide();

                    } else {
                        if (!data.errorMessage || data.errorMessage == '')
                            og.err(lang("error add_duty"));
                    }
                },
                scope: this
            });
        }
    },

    onselectyear: function () {
        var year = og.common.getCheckboxValue('task-year-selector');
        var url = og.getUrl('zhibanzhang', 'index',
            {
                userid: $('#user-id').val(),
                year: year
            });
        og.openLink(url, {});
    },

    initYear: function (year) {
        $('input[name="task-year-selector"][value=' + year + ']').attr('checked', true);
        return;
    },

    showError: function (tip) {
        $('#error-tip').html(tip);
        $('#error-tip').removeClass('hide');
    },
    onSearch: function () {
        $('#duty-search-input').val('');
        $('#duty-search-input').removeClass('gray');

    },
    leaveSearch: function () {
        if ($('#duty-search-input').val() == '') {
            $('#duty-search-input').val('输入日期(如2014-01-01)或值班人进行查询');
            $('#duty-search-input').addClass('gray');
        }
    },
    beginSearch: function () {
        var year = og.common.getCheckboxValue('task-year-selector');
        var url = og.getUrl('zhibanzhang', 'index', {year: year});
        og.openLink(url,
            {
                post: {
                    condition: $.trim($('#duty-search-input').val())
                }
            });
    },
    departClick: function () {
        og.duty.resetDepartRadioStatus();
    },

    resetDepartRadioStatus: function () {
        if (og.common.getCheckboxValue('most-clean-depart').split(',').length == 2) {
            $('input[name=most-clean-depart]').each(function(){
                if(!$(this).is(':checked')){
                    $(this).attr('disabled', 'disable');
                }else{
                    $(this).removeAttr('disabled');
                }
            });
        } else {
            $('input[name=most-clean-depart]').removeAttr("disabled");;
        }
    },
    floorClick: function () {
        og.duty.resetFloorRadioStatus();
    },
    resetFloorRadioStatus: function(){
        if (og.common.getCheckboxValue('most-clean-floor').split(',').length == 2) {
            $('input[name=most-clean-floor]').each(function(){
                if(!$(this).is(':checked')){
                    $(this).attr('disabled', 'disable');
                }else{
                    $(this).removeAttr('disabled');
                }
            });
        } else {
            $('input[name=most-clean-floor]').removeAttr("disabled");;
        }
    }
}

