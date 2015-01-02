/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.report = og.report || {};
og.report.addReport= function () {
    var url = og.getUrl('report', 'add_report', {type: 'text'});
    og.openLink(url, {post: {}});
};
og.report.onselectyear= function () {
    var year = og.common.getCheckboxValue('task-year-selector');
    var url = og.getUrl('report', 'index',
        {
            year: year
        });
    og.openLink(url, {});
};

og.report.initYear= function (year) {
    $('input[name="task-year-selector"][value=' + year + ']').attr('checked', true);
    return;
};
og.report.submit = function (id) {
    var params = {};
    params.id = id;
    var opt = 'add';
    if (params.id) {
        opt = 'save';
    }
    params.name = $.trim($('#report-name-input').val());
    params.content = UE.getEditor('report-ue-editor').getContent();
    var valiresult = og.report.validateReportInput(params);
    if (valiresult != '') {
        $('#add-report-tip').html(valiresult);
        return;
    } else {
        $('#add-report-tip').html('');
    }
    var url = og.getUrl('report', 'add_report', {opt: opt});
    og.openLink(url, {
        method: 'POST',
        post: params,
        callback: function (success, data) {
            if (success && !data.errorCode) {

                Ext.MessageBox.alert("提示", "操作成功！");
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error add_report"));
            }
        },
        scope: this
    });
}
og.report.validateReportInput = function (param) {
    if (param.name == '') {
        return '请输入文件名称';
    }
    if (param.content == '') {
        return '请输入文件内容';
    }
    return '';
}
og.report.view = function (id) {
    var url = og.getUrl('report', 'view_report', {id: id, opt: 'view'});
    og.openLink(url, {post: {}});
}
og.report.edit= function (id) {
    var url = og.getUrl('report', 'add_report', {id: id, opt: 'edit'});
    og.openLink(url, {post: {}});
}
og.report.handleReport = function (id, read_id) {
    var url = og.getUrl('report', 'view_report', {id: id, opt: 'handle', read_id: read_id});
    og.openLink(url, {post: {}});
}
og.report.hasRead = function (id,name, readId) {
    var url = og.getUrl('report', 'read_report', {id: id,name:name});
    var params = {};
    params.comment = $('#report-comment').val();
    params.readId = readId;
    params.newReaders = Ext.getCmp('continueReader').getValue().join(',')
    this.submitRead(params,url);
};
og.report.del = function (id) {
    var url = og.getUrl('report', 'del_report', {id: id});
    Ext.MessageBox.confirm('', '您确认删除该文件吗？', function (btn) {
        if (btn == 'yes') {
            og.openLink(url, {
                method: 'POST',
                callback: function (success, data) {
                    if (success && !data.errorCode) {
                        Ext.MessageBox.alert("提示", "删除成功！");
                        Ext.getCmp('report-panel').back();
                    } else {
                        if (!data.errorMessage || data.errorMessage == '')
                            og.err(lang("error del_report"));
                    }
                },
                scope: this
            });
        }
    });



};
/**
 * 提交已阅
 */
og.report.submitRead = function (params, url) {
    og.openLink(url, {
        method: 'POST',
        post: params,
        callback: function (success, data) {
            if (success && !data.errorCode) {
                Ext.MessageBox.alert("提示", "操作成功！");
                Ext.getCmp('report-panel').back();
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error add_report"));
            }
        },
        scope: this
    });
}
