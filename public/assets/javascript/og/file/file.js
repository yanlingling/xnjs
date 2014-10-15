/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.file = og.file || {};
og.file.addFile = function () {
    var url = og.getUrl('file', 'add_file', {type: 'chuanyue'});
    og.openLink(url, {post: {}});
};
og.file.addJufaFile = function () {
    var url = og.getUrl('file', 'add_file', {type: 'jufa'});
    og.openLink(url, {post: {}});
};
og.file.submit = function (type, id) {
    var params = {};
    params.id = id;
    var opt = 'add';
    if (params.id) {
        opt = 'save';
    }
    params.name = $.trim($('#file-name-input').val());
    params.content = UE.getEditor('file-ue-editor').getContent();
    params.readers = Ext.getCmp('fileReader').getValue().join(',');
    params.type = (type == 'jufa'? 2 : 1);
    // params.needSendMessage = $('#needSendMessage').is(":checked");
    // 新建局发的文件，不需要发短信通知
    if (type == 'jufa') {
        params.needSendMessage = false;
    } else {
        params.needSendMessage = true;
    }
    var valiresult = og.file.validateFileInput(params);
    if (valiresult != '') {
        $('#add-file-tip').html(valiresult);
        return;
    } else {
        $('#add-file-tip').html('');
    }
    var url = og.getUrl('file', 'add_file', {opt: opt});
    og.openLink(url, {
        method: 'POST',
        post: params,
        callback: function (success, data) {
            if (success && !data.errorCode) {

                Ext.MessageBox.alert("提示", "操作成功！");
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error add_file"));
            }
        },
        scope: this
    });
}
og.file.validateFileInput = function (param) {
    if (param.name == '') {
        return '请输入文件名称';
    }
    if (param.content == '') {
        return '请输入文件内容';
    }
    if (param.type == 1 && param.readers == '') {
        return '请输入阅读人';
    }
    return '';
}
og.file.view = function (id,type) {
    var url = og.getUrl('file', 'view_file', {id: id, opt: 'view',type:type});
    og.openLink(url, {post: {}});
}
og.file.handleFile = function (id, read_id) {
    var url = og.getUrl('file', 'view_file', {id: id, opt: 'handle', read_id: read_id});
    og.openLink(url, {post: {}});
}
og.file.rehandleFile = function (id, read_id) {
    var url = og.getUrl('file', 'view_file', {id: id, opt: 'rehandle'});
    og.openLink(url, {post: {}});
}
og.file.hasRead = function (id,name, readId) {
    var url = og.getUrl('file', 'read_file', {id: id,name:name});
    var params = {};
    params.comment = $('#file-comment').val();
    params.readId = readId;
    params.newReaders = Ext.getCmp('continueReader').getValue().join(',');
    //params.needSendMessage = $('#needSendMessage').is(":checked");
    params.needSendMessage = true;
    this.submitRead(params,url);
};
og.file.del = function (id) {
    var url = og.getUrl('file', 'del_file', {id: id});
    Ext.MessageBox.confirm('', '您确认删除该文件吗？', function (btn) {
        if (btn == 'yes') {
            og.openLink(url, {
                method: 'POST',
                callback: function (success, data) {
                    if (success && !data.errorCode) {
                        Ext.MessageBox.alert("提示", "删除成功！");
                        Ext.getCmp('file-panel').back();
                    } else {
                        if (!data.errorMessage || data.errorMessage == '')
                            og.err(lang("error del_file"));
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
og.file.submitRead = function (params, url) {
    og.openLink(url, {
        method: 'POST',
        post: params,
        callback: function (success, data) {
            if (success && !data.errorCode) {
                Ext.MessageBox.alert("提示", "操作成功！");
                Ext.getCmp('file-panel').back();
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error add_file"));
            }
        },
        scope: this
    });
};

og.file.onSearch = function () {
    $('#file-search-input').val('');
    $('#file-search-input').removeClass('gray');

};
 og.file.leaveSearch = function () {
    if ($('#file-search-input').val() == '') {
        $('#file-search-input').val('输入文件名进行查询');
        $('#file-search-input').addClass('gray');
    }
};
 og.file.beginSearch = function () {
    var url = og.getUrl('file', 'index');
    og.openLink(url,
        {
            post: {
                condition: $.trim($('#file-search-input').val()) == '输入文件名进行查询'? '':$.trim($('#file-search-input').val()),
                currentTabId: og.file.currentTabId
            }
        });
};


og.file.currentTabId = 'to-read-tab';