/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.learning = og.learning || {};
og.learning.addTextLearning = function () {
    var url = og.getUrl('lianzhengxuexi', 'add_learning', {type: 'text'});
    og.openLink(url, {post: {}});
}
og.learning.addVedioLearning = function () {
    var url = og.getUrl('lianzhengxuexi', 'add_learning', {type: 'vedio'});
    og.openLink(url, {post: {}});
}


 og.learning.onselectyear = function () {
    var year = og.common.getCheckboxValue('task-year-selector');
    var url = og.getUrl('lianzhengxuexi', 'index',
        {
            userid: $('#user-id').val(),
            year: year
        });
    og.openLink(url, {});
};

 og.learning.initYear = function (year) {
    $('input[name="task-year-selector"][value='+year+']').attr('checked',true);
    return;
};

og.learning.editLearnContent = function (id) {
    var url = og.getUrl('lianzhengxuexi',
        'add_learning',
        {
            type: 'vedio',
            opt:'edit',
            id:id
        });
    og.openLink(url, {post: {}});
}

og.learning.submit = function (type,id) {
    var params = {};
    params.id = id;
    var opt = 'add';
    if (params.id) {
        opt = 'save';
    }
    params.name = $.trim($('#learning-name-input').val());
    params.dueDate = $('#learning-date-picker').val();
    params.mustLearn = $('#is-must-learn').is(':checked')?1:0;
    if (type == 0) {
        params.content = UE.getEditor('ue-editor').getContent();
        var valiresult = og.learning.validateAddLearningInput(params);
        if (valiresult != '') {
            $('#add-learning-tip').html(valiresult);
            return;
        } else {
            $('#add-learning-tip').html('');
        }
        var url = og.getUrl('lianzhengxuexi', 'add_learning', {opt: opt, type: type});
        og.openLink(url, {
            method: 'POST',
            post: params,
            callback: function (success, data) {
                if (success && !data.errorCode) {

                    Ext.MessageBox.alert("提示", "操作成功！");
                } else {
                    if (!data.errorMessage || data.errorMessage == '')
                        og.err(lang("error add_learning"));
                }
            },
            scope: this
        });
    } else {
        var valiresult = og.learning.validateAddVedioInput(params);
        params.vedio = $('#vedio-name-input').val();
        if (valiresult != '') {
            $('#add-learning-tip').html(valiresult);
            return;
        } else {
            var url = og.getUrl('lianzhengxuexi', 'add_learning', {opt: opt, type: type});
            og.openLink(url, {
                method: 'POST',
                post: params,
                callback: function (success, data) {
                    if (success && !data.errorCode) {
                        Ext.MessageBox.alert("提示", "创建成功！");
                    } else {
                        if (data.errorCode == 20) {
                            $('#add-learning-tip').html('文件不存在');
                        }
                        else if (!data.errorMessage || data.errorMessage == '') {
                            og.err(lang("error add_learning"));
                        }
                    }
                },
                scope: this
            });
        }
    }
}
og.learning.validateAddVedioInput = function (param) {
    if ($('#learning-name-input').val() == '') {
        return '请输入学习名称';
    }
    if ($('#learning-date-picker').val() == '' && param.mustLearn == 1) {
        return '请输入时间'
    }
    /*if (!og.common.lateThenNow($('#learning-date-picker').val())) {
        return '到期时间必须大于当前时间';
    }*/
    var fileName = $('#learning-name-input').val();

    if (fileName == '') {
        return '输入视频文件名称';
    }
    return '';
}
og.learning.validateAddLearningInput = function (param) {
    if (param.name == '') {
        return '请输入学习名称';
    }
    if (param.content == '') {
        return '请输入学习内容';
    }
    if (param.dueDate == '' && param.mustLearn == 1) {
        return '请输入时间';
    }
   /* if (!og.common.lateThenNow(param.dueDate)) {
        return '到期时间必须大于当前时间';
    }*/
    return '';
}
og.learning.toLearnContent = function (learnId, contentId, opt) {
    var url = og.getUrl('lianzhengxuexi', 'to_learning',
        {opt: opt,learnId:learnId,contentId:contentId});
    og.openLink(url, {
        method: 'POST',
        scope: this
    });
}
og.learning.completeLearning = function (learnId) {
    var hour = parseInt($('#learning-timer-hour').html(),10);
    var minute = parseInt($('#learning-timer-minute').html(),10);
    var second =  parseInt($('#learning-timer-second').html(),10);
    var tip = '您本次的学习时间为'+hour+'时'+minute+'分'+second+'秒，您确认学习完成吗？'
    Ext.MessageBox.confirm('', tip, function (btn) {
        if (btn == 'yes') {
            var url = og.getUrl('lianzhengxuexi', 'complete_learning', {id: learnId});
            var param = {};
            param.timeLong = hour*60+minute*60+second;
            og.openLink(url, {
                method: 'POST',
                post: param,
                callback: function (success, data) {
                    if (success && !data.errorCode) {
                        Ext.MessageBox.alert("提示", "操作成功！");
                        $('#complete-learning').hide();
                        $('#learning-timer').hide();
                    } else {
                        og.err(lang("complete learning error"));
                    }
                },
                scope: this
            });
        }
    });

}
og.learning.goToDepartLearning = function (depId) {
    var url = og.getUrl('lianzhengxuexi', 'index_of_depart', {depart_id: depId});
    var param = {};

    og.openLink(url, {
        method: 'POST',
        post: param,
        scope: this
    });
}
og.learning.goToAllDepartLearning = function () {
    var url = og.getUrl('lianzhengxuexi', 'index_of_juzhang');
    var param = {};

    og.openLink(url, {
        method: 'POST',
        post: param,
        scope: this
    });
}
og.learning.goToPersonLearning = function (id) {
    var url = og.getUrl('lianzhengxuexi', 'index', {userid: id});
    var param = {};

    og.openLink(url, {
        method: 'POST',
        post: param,
        scope: this
    });
}

/**
 * 点击通过
 * @param task_id
 */
og.learning.passSupervise = function (id) {
    var url = og.getUrl('lianzhengxuexi', 'pass_supervise', {id: id});
    og.openLink(url, {
        method: 'POST',

        callback: function (success, data) {
            if (success && !data.errorCode) {

                Ext.MessageBox.alert("提示", "操作成功");
                Ext.getCmp('lianzhengxuexi-panel').reload();
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error pass_supervise"));
            }
        },
        scope: this
    });
}

/**
 * 点击不通过
 * @param task_id
 */
og.learning.rejectSupervise = function (id, due_date, user_id) {
    var url = og.getUrl('lianzhengxuexi', 'reject_supervise', {id: id});
    og.openLink(url, {
        method: 'POST',
        post: {
            due_date: due_date,
            user_id: user_id
        },
        callback: function (success, data) {
            if (success && !data.errorCode) {
                Ext.MessageBox.alert("提示", "操作成功");
                Ext.getCmp('lianzhengxuexi-panel').reload();
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error reject_supervise"));
            }
        },
        scope: this
    });
}

/**
 * 发布评论学习体会
 * @param content_id
 */
og.learning.publishComment = function(content_id){
    var url = og.getUrl('lianzhengxuexi', 'publish_comment', {id: content_id});
    var params = {};
    params.content = $.trim(Ext.getCmp('learning-comment-box').getValue());
    if($.trim(params.content) == ''){
        $('#comment-error').html('请输入学习体会');
        return ;
    }
    og.openLink(url, {
        method: 'POST',
        post: params,
        callback: function (success, data) {
            if (success && !data.errorCode) {

                Ext.MessageBox.alert("提示", "操作成功");
                Ext.getCmp('lianzhengxuexi-panel').reload();
            } else {
                if (!data.errorMessage || data.errorMessage == '')
                    og.err(lang("error publish_comment"));
            }
        },
        scope: this
    });
}

/**
 * 删除学习内容
 * @param id
 */
og.learning.delLearnContent = function (id) {
    Ext.MessageBox.confirm('', '您确认删除该学习内容吗？', function (btn) {
        if (btn == 'yes') {
            var url = og.getUrl('lianzhengxuexi', 'delete_learning_content', {id: id});
            og.openLink(url, {
                method: 'POST',
                callback: function (success, data) {
                    if (success && !data.errorCode) {
                        Ext.MessageBox.alert("提示", "操作成功");
                        Ext.getCmp('lianzhengxuexi-panel').reload();
                    } else {
                        if (!data.errorMessage || data.errorMessage == '')
                            og.err(lang("delete-learning_content"));
                    }
                },
                scope: this
            });
        }
    });
}
