/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.risk = og.risk || {};
og.risk = {
    /**
     * 添加个人学习内容
     */
    addPersonQuestion: function () {
        var url = og.getUrl('fengxiandian', 'add_risk', {type: 'person'});
        og.openLink(url, {post: {}});
    },

    /**
     * 添加题目按钮点击的事件
     */
    addQuestionClickHandler: function () {
        var num = parseInt($('#questionNum').val(), 10) + 1;
        var str = '<div class="risk-question">' +
            '<input id="xuhao1" name="xuhao' + num + '" value="' + num + '" type="hidden">' +
            '<div>' +
            '题目' + num + '：<input class="question-title"  id="question-title-' + num + '"/>' +
            '</div>' +
            '<div>' +
            '答案1：<input id="question-answer1-' + num + '"  class="answer1" />' +
            '答案2：<input id="question-answer2-' + num + '" class="answer2" />' +
            '</div>' +
            '</div>';
        $('#question-area').append(str);
        $('#questionNum').val(num);
    },

    /**
     * 提交问卷
     */
    submit: function (type) {
        var res = this.validateAddInput();
        if (res != '') {
            $('#add-risk-tip').html(res);
            return;
        } else {
            var params = {};
            params.id = id;
            var opt = 'add';

            params.name = $.trim($('#risk-name-input').val());
            params.dueDate = $('#risk-date-picker').val();

            var num = parseInt($('#questionNum').val(), 10);
            params.questionNum = num;
            for (var i = 1; i <= num; i++) {
                params['question-title-' + i] = $('#question-title-' + i).val();
                params['question-answer1-' + i] = $('#question-answer1-' + i).val();
                params['question-answer2-' + i] = $('#question-answer2-' + i).val();
            }

            var url = og.getUrl('fengxiandian', 'add_risk', {opt: opt, type: type});
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

        }
    },

    validateAddInput: function () {
        if ($.trim($('#risk-name-input').val()) == '') {
            return '请输入问卷名称';
        }
        else if ($('#risk-date-picker').val() == '') {
            return '请输入时间'
        }
/*        if (!og.common.lateThenNow($('#risk-date-picker').val())) {
            return '到期时间必须大于当前时间';
        }*/
        var res = '';
        $('.risk-question').each(function (index) {
            var index = index + 1;
            if ($.trim($(this).find('.question-title').val()) == '') {
                res = '请输入题目' + index + '的题目';
            } else if ($.trim($(this).find('.answer1').val()) == '') {
                res = '请输入题目' + index + '的答案1';
            } else if ($.trim($(this).find('.answer2').val()) == '') {
                res = '请输入题目' + index + '的答案2';
            }
        });
        return res;
    },
    toRiskContent: function (riskId, contentId, opt) {
        var url = og.getUrl('fengxiandian', 'to_risk',
            {opt: opt, learnId: riskId, contentId: contentId});
        og.openLink(url, {
            method: 'POST',
            scope: this
        });
    },

    completeRisk: function (learningId, contentId) {
        var questionNum = $('.risk-question-learn').length;
        var params = {};
        params.questionNum = questionNum;
        for (var i = 1; i <= questionNum; i++) {
            params['question' + i] = $('#question-id-' + i).val();
            params['answer' + i] = $('input[name="answer-radio-' + i + '"]:checked').val();
        }
        var res = this.validateAnswer(params);
        if (res != '') {
            $('#error-tip').html(res);
            return;
        } else {
            $('#error-tip').html('');
        }
        Ext.MessageBox.confirm('', '您确定要提交本风险点自查自控问券吗？提交后将不可修改。', function (btn) {
            if (btn == 'yes') {
                var url = og.getUrl('fengxiandian', 'complete_risk', {id: learningId, contentId: contentId});
                og.openLink(url, {
                    method: 'POST',
                    post: params,
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "操作成功！");
                            $('#sumit-answer-risk').hide();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error add_learning"));
                        }
                    },
                    scope: this
                });
            }
        });


    },

    /**
     * 验证答题情况
     */
    validateAnswer: function (param) {
        for (var i = 1; i <= param.questionNum; i++) {
            if (typeof param['answer' + i] == 'undefined') {
                return '请回答问题' + i;
            }
        }
        return '';
    },

    delLearnContent: function (id, type) {
        Ext.MessageBox.confirm('', '您确定要删除本风险点自查自控问券吗？', function (btn) {
            if (btn == 'yes') {
                var url = og.getUrl('fengxiandian', 'del_risk', {id: id, type: type});
                og.openLink(url, {
                    method: 'POST',
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "操作成功！");
                            Ext.getCmp('fengxiandian-panel').reload();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error add_learning"));
                        }
                    },
                    scope: this
                });
            }
        });
    },


    goToDepartLearning: function (depId) {
        var url = og.getUrl('fengxiandian', 'index_of_depart', {depart_id: depId});
        var param = {};

        og.openLink(url, {
            method: 'POST',
            post: param,
            scope: this
        });
    },
    goToAllDepartLearning: function () {
        var url = og.getUrl('fengxiandian', 'index_of_juzhang');
        var param = {};

        og.openLink(url, {
            method: 'POST',
            post: param,
            scope: this
        });
    },

    goToPersonLearning: function (id) {
        var url = og.getUrl('fengxiandian', 'index', {userid: id});
        var param = {};

        og.openLink(url, {
            method: 'POST',
            post: param,
            scope: this
        });
    }
}
