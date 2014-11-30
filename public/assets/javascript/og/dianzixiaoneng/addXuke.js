og.addXuke = {
    /**
     * 初始化时间控件
     */
    initDate: function () {
        var datePicker = new og.DateField({
            renderTo: 'apply-time',
            name: 'apply-time-picker',
            id: 'apply-time-picker',
            value: '',
            editable: false,
            readOnly: true
        });
    },

    submit: function () {
        var params = {};
        params.name= $('#apply-name').val();
        params.area= $('#apply-area').val();
        params.type= $('#apply-type').val();
        params.detail= $('#apply-detail').val();
        params.date= $('#apply-time-picker').val();
        params.id = $('#apply-id').html();
        var valiresult = og.addXuke.validateInput(params);

        if (valiresult != '') {
            $('#add-xuke-tip').html(valiresult);
            return;
        }else{
            $('#add-xuke-tip').html('');
        }
        var opt = 'add';
        if (params.id != '') {
            opt = 'edit';
        }
        var url = og.getUrl('dianzixiaoneng', 'add_xuke', {opt: opt});

        og.openLink(url, {
            method: 'POST',
            post: params,
            callback: function (success, data) {
                if (success && !data.errorCode) {

                    Ext.MessageBox.alert("提示", "添加成功！");
                } else {
                    if (!data.errorMessage || data.errorMessage == '')
                        og.err(lang("error task delay apply"));
                }
            },
            scope: this
        });
    },


    /**
     * 验证新建任务输入的合法性
     */
    validateInput: function (params) {
        if ($.trim(params.name) == '') {
            return '请输入申请人';
        }
        if ($.trim(params.area) == '') {
            return '请输入申请人片区';
        }
        if ($.trim(params.date) == '') {
            return '请选择申请时间';
        }
        if ($.trim(params.type) == '') {
            return '请选择申请类别';
        }
        if ($.trim(params.detail) != '' && $.trim(params.detail).length>200) {
            return '请输入少于200字的详情';
        }
        var now = new Date();
        var nowYear = now.getFullYear();
        var nowMonth = now.getMonth()+1;
        var nowDate = now.getDate();
        var nowDate = nowYear + '-' + nowMonth + '-' + nowDate;


        if (new Date(nowDate).getTime() < new Date($.trim(params.date)).getTime()) {
            return '申请时间必须早于当前时间';
        }
        return '';
    },

    /**
     * 验证新建法定职责
     */
    validateLawInput: function (params) {
        if ($.trim(params.content) == '') {
            return '请输入法定职责';
        }

        return '';
    },
    fillTaskField: function (task) {
        if (!task) {
            return;
        }
        $('#add-task-taskid').html(task.id);
        $('#task-name-input').val(task.title);
        $('#task-detail-input').html(task.text);
        Ext.getCmp("task-date-picker").setValue(task.due_date.split(' ')[0]);
        Ext.getCmp("departCombotask-due-depart").setValue(task.depart_name);
    }
}