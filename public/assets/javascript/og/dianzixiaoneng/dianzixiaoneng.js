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
    }
}

