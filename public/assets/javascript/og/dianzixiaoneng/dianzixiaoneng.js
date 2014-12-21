/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.dianzixiaoneng= og.dianzixiaoneng|| {};
og.dianzixiaoneng= {
    add: function () {
        var url = og.getUrl('dianzixiaoneng', 'add_xuke');
        og.openLink(url, {post: {}});
    },
    view: function (id) {
        var url = og.getUrl('dianzixiaoneng', 'view_xuke',{id:id});
        og.openLink(url, {post: {}});
    },
    del: function (id) {
        Ext.MessageBox.confirm('', '您确认删除该许可申请吗？', function (btn) {
            if (btn == 'yes') {
                og.openLink(og.getUrl('dianzixiaoneng', 'del_xuke', {id: id}), {
                    method: 'POST',
                    callback: function (success, data) {
                        if (!success || data.errorCode) {

                        } else {
                            Ext.getCmp('dianzixiaoneng-panel').reload();
                        }
                    },
                    scope: this
                });
            }
        });
    },
    finishTask: function (taskid,xukeid,sub_process,type) {
        var parameters = {
            taskid: taskid,
            xukeid: xukeid,
            sub_process:sub_process,
            type:type,
            result: 1,
            detail:''
        }
        Ext.MessageBox.confirm('', '您确认已经完成该项任务了吗？', function (btn) {
            if (btn == 'yes') {
                og.openLink(og.getUrl('dianzixiaoneng', 'handle_task_result', {id: taskid}), {
                    method: 'POST',
                    post: parameters,
                    callback: function (success, data) {
                        if (!success || data.errorCode) {

                        } else {
                            Ext.getCmp('dianzixiaoneng-panel').reload();
                        }
                    },
                    scope: this
                });
            }
        });
    },
    handleTask: function (taskid,xukeid,sub_process,type) {
        $('#xuke-task-id').val(taskid);
        $('#xuke-id').val(xukeid);
        $('#xuke-type').val(type);
        $('#sub-process').val(sub_process);
        $('#xuke-status-detail').val('');
        $('input[name=xuke-status]').attr("checked",false);
        $('#xukeHandleModal').modal();
    },
    handleTaskStatusOK: function () {
        var parameters = {
            taskid: $('#xuke-task-id').val(),
            xukeid: $('#xuke-id').val(),
            type: $('#xuke-type').val(),
            sub_process: $('#sub-process').val(),
            result: og.common.getCheckboxValue('xuke-status'),
            detail:$.trim($('#xuke-status-detail').val())
        };
        if(!parameters.result){
            $('#comment-error').html('请选择处理结果');
            return;
        }
        $('#comment-error').html('');
        $('#xukeHandleModal').modal('hide');
        //return;
        var url = og.getUrl('dianzixiaoneng', 'handle_task_result', {id: parameters.taskId});
        og.openLink(url, {
            method: 'POST',
            post: parameters,
            callback: function (success, data) {
                if (success && !data.errorCode) {
                    Ext.MessageBox.alert("提示", "处理成功");
                    Ext.getCmp('dianzixiaoneng-panel').reload();
                } else {
                    if (!data.errorMessage || data.errorMessage == '')
                        og.err(lang("error xuke handle"));
                }
            },
            scope: this
        });
    },
    showDelayApply: function (taskid) {
        $('#xuke-task-id').val(taskid);
        $('#xuke-delay-apply-detail').val('');
        $('#xuke-delay-apply-day').val('');
        $('#xukeDelayApplyModal').modal();
    },
    viewDelayApplyDetail: function (id, taskid,detail,applyDay) {
        $('#xuke-task-id').val(taskid);
        $('#xuke-delay-apply-detail').val(detail);
        $('#xuke-delay-apply-day').val(applyDay);
        $('#xuke-apply-submit').hide();
        $('#xukeDelayApplyModal').modal();
    },
    handleDelayApply: function (id, taskid,detail,applyDay) {
        $('#xuke-handle-task-id').val(taskid);
        $('#xuke-handle-apply-id').val(id);
        $('#xuke-handle-delay-apply-detail').val(detail);
        $('#xuke-handle-delay-apply-day').html(applyDay);
        $('#xukeHandleDelayApplyModal').modal();
    },


    /**
     * 跳转到科室的任务
     * @param depart_id
     */
    goToDepartTask: function (depart_id) {
        var url = og.getUrl('dianzixiaoneng', 'index', {depart_id: depart_id, tab: 'xuke'});
        og.openLink(url, {});
    },

    /**
     * 跳转到科室的延期申请
     * @param depart_id
     */
    goToDepartApply: function (depart_id) {
        var url = og.getUrl('dianzixiaoneng', 'index', {depart_id: depart_id, tab: 'delay'});
        og.openLink(url, {});
    },

    delayApplyOk: function () {
        var parameters = {
            taskid: $('#xuke-task-id').val(),
            day: $.trim($('#xuke-delay-apply-day').val()),
            detail:$.trim($('#xuke-delay-apply-detail').val())
        };
        if(!parameters.day){
            $('#xuke-apply-error').html('请填写申请延期天数');
            return;
        }
        if(!$.isNumeric(parameters.day)){
            $('#xuke-apply-error').html('请填写正确延期天数');
            return;
        }
        $('#xuke-apply-error').html('');
        $('#xukeDelayApplyModal').modal('hide');
        //return;
        var url = og.getUrl('dianzixiaoneng', 'delay_apply', {id: parameters.taskid});
        og.openLink(url, {
            method: 'POST',
            post: parameters,
            callback: function (success, data) {
                if (success && !data.errorCode) {
                    Ext.MessageBox.alert("提示", "创建成功");
                    Ext.getCmp('dianzixiaoneng-panel').reload();
                } else {
                    if (!data.errorMessage || data.errorMessage == '')
                        og.err(lang("error xuke handle"));
                }
            },
            scope: this
        });
    },

    /**
     * 同意延期申请
     * @param id
     * @param task_id
     */
    agreeDelayApply: function () {
        var id = $('#xuke-handle-apply-id').val();
        var task_id = $('#xuke-handle-task-id').val();
        var parameters = {
            agreeDay: $.trim($('#xuke-handle-delay-apply-agree-day').val()),
            taskId: task_id
        };
        if (!$.isNumeric(parameters.agreeDay)) {
            $('#xuke-handle-apply-error').html('同意延期的天数必须是数字');
            return;
        }
        var url = og.getUrl('dianzixiaoneng', 'agree_delay_apply', {id: id});
        og.openLink(url, {
            method: 'POST',
            post: parameters,
            callback: function (success, data) {
                if (success && !data.errorCode) {
                    Ext.MessageBox.alert("提示", "操作成功");
                    Ext.getCmp('dianzixiaoneng-panel').reload();
                } else {
                    if (!data.errorMessage || data.errorMessage == '')
                        og.err(lang("error task delay apply"));
                }
            },
            scope: this
        });
        $('#xukeHandleDelayApplyModal').modal('hide');
    },

    /**
     * 不同意的处理
     * @param id
     * @param task_id
     */
    disagreeDelayApply: function () {
        var id = $('#xuke-handle-apply-id').val();
        var task_id = $('#xuke-handle-task-id').val();
        var url = og.getUrl('dianzixiaoneng', 'disagree_delay_apply', {id: id});
        og.openLink(url, {
            method: 'POST',
            callback: function (success, data) {
                if (success && !data.errorCode) {

                    Ext.MessageBox.alert("提示", "操作成功");
                    Ext.getCmp('dianzixiaoneng-panel').reload();
                } else {
                    if (!data.errorMessage || data.errorMessage == '')
                        og.err(lang("error task delay apply"));
                }
            },
            scope: this
        });
        $('#xukeHandleDelayApplyModal').modal('hide');
    },

    cancelDelayApply: function (id) {
        Ext.MessageBox.confirm('撤回申请', '您确认要撤回该申请吗？', function (btn) {
            if (btn == 'yes') {
                og.dianzixiaoneng.doCancelDelayApply(id);
            }
        });
    },
    doCancelDelayApply: function (id) {
        var url = og.getUrl('dianzixiaoneng', 'task_delay_apply_cancel', {id: id});
        og.openLink(url, {
            method: 'POST',
            post: {},
            callback: function (success, data) {
                if (success && !data.errorCode) {
                    Ext.MessageBox.alert("提示", "操作成功");
                    Ext.getCmp('dianzixiaoneng-panel').reload();
                } else {
                    if (!data.errorMessage || data.errorMessage == '')
                        og.err(lang("error task delay apply"));
                }
            },
            scope: this
        });
    }
}

