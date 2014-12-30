/**
 *
 * This module holds the rendering logic for the add new task div
 *
 * @author Carlos Palma <chonwil@gmail.com>
 */
og.out = og.out || {};
og.out = {
    editClickHandler: function(){
        //$('.kaoqin-float').hide();
        $('.kaoqin-float').addClass('hide');
        var value = $(this).prev().html();
        $(this).parent().find('.kaoqin-float li').removeClass('current');
        $(this).parent().find('.kaoqin-float li').each(function(){
             if($(this).html() == value){
                 $(this).addClass('current')
             }
        });
        $(this).parent().find('.kaoqin-float').removeClass('hide');

    },

   updateStatus: function(){
       var url = og.getUrl('outregist', 'get_newstatus', {});
       og.openLink(url, {
           method: 'POST',
           callback: function (success, data) {
               if (success && !data.errorCode) {
                   og.out.updateStatusShow(data);
               } else {
               }
           },
           scope: this
       });
   },
    updateStatusShow: function(data){
        for(var i= 0,item;item = data[i++];){
           var status = og.out.statusMap[item.kaoqin_status];
           var str = '';
            if(item.kaoqin_status==8){
                str = status;
            }else{
                switch (item.kaoqin_status){
                    case '1':
                        str = '<a class="red">'+status+'</a>';
                        break;
                    case '2':
                        str = '<a class="blue">'+status+'</a>';
                        break;
                    case '3':
                        str = '<a class="blue">'+status+'</a>';
                        break;
                    case '4':
                        str = '<a class="red">'+status+'</a>';
                        break;
                    case '5':
                        str = '<a class="blue">'+status+'</a>';
                        break;
                }
            }
            if(item.isSelf &&item.kaoqin_status!=8){
                str +='<span class="turnToWork"  titile="在岗" onclick="og.out.setAtWork('+item.id+','+item.kaoqin_status+')"></span>'
            }
            $('#status-'+item['id']).html(str);
        }
    },

    statusMap: {
        1:'公差',
        2:'病假',
        3:'事假',
        4:'其它',
        5:'年休假',
        8:'在岗'
    },

    setAtWork: function(userid,formerStatus){
        var url = og.getUrl('outregist', 'change_status', {id: userid, status: 8,formerStatus:formerStatus});
        og.openLink(url, {
            method: 'POST',
            callback: function (success, data) {
                if (success && !data.errorCode) {
                    Ext.MessageBox.alert("提示", "修改成功！");
                    Ext.getCmp('outregist-panel').reload();
                } else {
                    if (!data.errorMessage || data.errorMessage == '')
                        og.err(lang("error change_status"));
                }
            },
            scope: this
        });
    },

    /**
     * 改变考勤状态
     */
    changeStatus: function(status,userid,formerStatus){
        Ext.MessageBox.confirm('', '您确认修改该人员的考勤状态为'+og.out.statusMap[status]+'吗？', function (btn) {
            if (btn == 'yes') {
                var url = og.getUrl('outregist', 'change_status', {id: userid, status: status,formerStatus:formerStatus});
                og.openLink(url, {
                    method: 'POST',
                    callback: function (success, data) {
                        if (success && !data.errorCode) {
                            Ext.MessageBox.alert("提示", "修改成功！");
                            Ext.getCmp('outregist-panel').reload();
                        } else {
                            if (!data.errorMessage || data.errorMessage == '')
                                og.err(lang("error change_status"));
                        }
                    },
                    scope: this
                });
            }
        });
    },
    viewData: function(){
        var url = og.getUrl('outregist', 'view_data');
        og.openLink(url, {post: {}});
    }
};

