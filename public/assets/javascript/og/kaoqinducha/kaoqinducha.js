/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.kaoqinducha = og.kaoqinducha || {};
og.kaoqinducha.addKaoqinducha = function () {
    var url = og.getUrl('kaoqin', 'add_kaoqinducha');
    og.openLink(url, {post: {}});
};
og.kaoqinducha.submit = function (type, id) {
    var params = {};
    params.id = id;
    var opt = 'add';
    if (params.id) {
        opt = 'save';
    }
    params.name = $.trim($('#kaoqinducha-name-input').val());
    params.content = UE.getEditor('kaoqinducha-ue-editor').getContent();
    var valiresult = og.kaoqinducha.validatekaoqinduchaInput(params);
    if (valiresult != '') {
        $('#add-kaoqinducha-tip').html(valiresult);
        return;
    } else {
        $('#add-kaoqinducha-tip').html('');
    }
    var url = og.getUrl('kaoqin', 'add_kaoqinducha', {opt: opt});
    og.openLink(url, {
        method: 'POST',
        post: params,
        callback: function (success, data) {
            if (success && !data.errorCode) {

                Ext.MessageBox.alert("提示", "操作成功！");
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error add_kaoqinducha"));
            }
        },
        scope: this
    });
}
og.kaoqinducha.validatekaoqinduchaInput = function (param) {
    if (param.name == '') {
        return '请输入文件名称';
    }
    if (param.content == '') {
        return '请输入文件内容';
    }
    return '';
}
og.kaoqinducha.view = function (id, type) {
    var url = og.getUrl('kaoqin', 'view_kaoqinducha', {id: id, opt: 'view', type: type});
    og.openLink(url, {post: {}});
}
og.kaoqinducha.del = function (id) {
    var url = og.getUrl('kaoqin', 'del_kaoqinducha', {id: id});
    Ext.MessageBox.confirm('', '您确认删除该文件吗？', function (btn) {
        if (btn == 'yes') {
            og.openLink(url, {
                method: 'POST',
                callback: function (success, data) {
                    if (success && !data.errorCode) {
                        Ext.MessageBox.alert("提示", "删除成功！");
                        Ext.getCmp('kaoqin-panel').back();
                    } else {
                        if (!data.errorMessage || data.errorMessage == '')
                            og.err(lang("error del_kaoqinducha"));
                    }
                },
                scope: this
            });
        }
    });


};

og.kaoqinducha.onSearch = function () {
    $('#kaoqinducha-search-input').val('');
    $('#kaoqinducha-search-input').removeClass('gray');

};
og.kaoqinducha.leaveSearch = function () {
    if ($('#kaoqinducha-search-input').val() == '') {
        $('#kaoqinducha-search-input').val('输入文件名进行查询');
        $('#kaoqinducha-search-input').addClass('gray');
    }
};
og.kaoqinducha.beginSearch = function () {
    var url = og.getUrl('kaoqinducha', 'index');
    og.openLink(url,
        {
            post: {
                condition: $.trim($('#kaoqinducha-search-input').val()) == '输入文件名进行查询' ? '' : $.trim($('#kaoqinducha-search-input').val()),
                currentTabId: og.kaoqinducha.currentTabId
            }
        });
};


og.kaoqinducha.currentTabId = 'to-read-tab';

og.jilvjiancha = og.jilvjiancha || {};
og.jilvjiancha = {
    addJilvjiancha: function () {
        var url = og.getUrl('kaoqin', 'write_jilvjiancha');
        og.openLink(url, {post: {}});
    },

    viewJilvjiancha: function (id) {
        var url = og.getUrl('kaoqin', 'write_jilvjiancha', {opt: 'view', id: id});
        og.openLink(url, {post: {}});
    },
    editJilvjiancha: function (id) {
        var url = og.getUrl('kaoqin', 'write_jilvjiancha', {opt: 'edit', id: id});
        og.openLink(url, {post: {}});
    },
    saveClickHandler: function (type, id) {
        var params = {};
        var opt = type || 'add';

        params.work_status1 = Ext.getCmp('work-status-1').getValue().join(',');
        params.work_status2 = Ext.getCmp('work-status-2').getValue().join(',');
        params.work_status3 = Ext.getCmp('work-status-3').getValue().join(',');
        params.work_status4 = Ext.getCmp('work-status-4').getValue().join(',');
        params.work_status5 = Ext.getCmp('work-status-5').getValue().join(',');
        params.jiancha_time = $('#jilvjiancha-time').val();
        params.jiancha_user = $('#jilvjiancha-user').val();

        params.mostCleanDepart = og.common.getCheckboxValue('most-clean-depart');
        params.mostCleanFloor = og.common.getCheckboxValue('most-clean-floor');
        params.leastCleanDepart = og.common.getCheckboxValue('least-clean-depart');
        params.leastCleanFloor = og.common.getCheckboxValue('least-clean-floor');
        params.otherContent = $('#other-detail').val();
        var me = this;
        var res = this.valiInput(params);
        if (res == '') {
            this.doOpration(opt, id, params);
            $('#error-tip').val('');
        } else {
            $('#error-tip').val(res);
            $('#error-tip').removeClass('hide');
        }
    },

    valiInput: function (p) {
        if ($.trim(p.jiancha_time) == '') {
            return '请输入检查时间';
        }
        if ($.trim(p.jiancha_user) == '') {
            return '请输入检查人';
        }
        return '';
    },

    doOpration: function (opt, id, params) {
        if (opt == 'add') {
            var url = og.getUrl('kaoqin', 'add_jilvjiancha', {opt: opt});
            og.openLink(url, {
                method: 'POST',
                post: params,
                callback: function (success, data) {
                    if (success && !data.errorCode) {
                        Ext.getCmp('kaoqin-panel').back();
                    } else {
                        if (!data.errorMessage || data.errorMessage == '')
                            og.err(lang("error add_duty"));
                    }
                },
                scope: this
            });
        } else {
            var url = og.getUrl('kaoqin', 'edit_jilvjiancha', {opt: opt, id: id});
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
    del: function (id) {
        var url = og.getUrl('kaoqin', 'del_jilvjiancha', {id: id});
        Ext.MessageBox.confirm('', '您确认删除该检查记录吗？', function (btn) {
            if (btn == 'yes') {
                og.openLink(url, {
                    method: 'POST',
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "删除成功！");
                            Ext.getCmp('kaoqin-panel').back();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error del_kaoqinducha"));
                        }
                    },
                    scope: this
                });
            }
        })
    },

    showError: function (tip) {
        $('#error-tip').html(tip);
        $('#error-tip').removeClass('hide');
    }
};

