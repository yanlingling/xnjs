/**
 * 点击我的延期申请
 */
ogTasks.showApplyPanel = function () {
    og.openLink(og.getUrl('task', 'delay_apply_list'));
}

/**
 * 撤回申请
 */
ogTasks.cancelDelayApply = function (id) {
    Ext.MessageBox.confirm('撤回申请', '您确认要撤回该申请吗？', function (btn) {
        if (btn == 'yes') {
            ogTasks.doCancelDelayApply(id);
        }
    });
}

/**
 * 查看申请详情
 * @param id
 */
ogTasks.viewDelayApplyDetail = function (id) {

    var container_id = 'apply-detail' + id;
    var ele = $('#' + container_id);
    if (ele.is(':visible')) {
        ele.addClass('hide');
    } else {
        ele.removeClass('hide');
    }
}

/**
 * 保存延期申请
 * @param id
 */
ogTasks.saveDelayApplyDetail = function (id) {
    var parameters = {
        applyId: id,
        reason: document.getElementById('apply-reason-' + id).value,
        hopeDay: document.getElementById('apply-day-' + id).value,
        agreeDay: document.getElementById('agree-day-' + id).value
    };
    var result = og.taskList.checkTaskDelayApplyInput(parameters);
    if (result != '') {
        $('#edit-apply-tip').html(result);
        return;
    }else{
        $('#edit-apply-tip').html('');
    }
    var url = og.getUrl('newtask', 'task_delay_apply_edit', {id: id});
    og.openLink(url, {
        method: 'POST',
        post: parameters,
        callback: function (success, data) {
            if (success && !data.errorCode) {
                Ext.MessageBox.alert("提示", "修改成功");
                Ext.getCmp('tasks-panel').reload();
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error task delay apply"));
            }
        },
        scope: this
    });
}


/**
 * 撤回延期申请
 * @param id
 */
ogTasks.doCancelDelayApply = function (id) {
    var url = og.getUrl('newtask', 'task_delay_apply_cancel', {id: id});
    og.openLink(url, {
        method: 'POST',
        post: {},
        callback: function (success, data) {
            if (success && !data.errorCode) {
                Ext.MessageBox.alert("提示", "操作成功");
                Ext.getCmp('tasks-panel').reload();
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error task delay apply"));
            }
        },
        scope: this
    });
}


