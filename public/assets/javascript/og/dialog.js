og.dialog ={
    draw:function(){
        var win = Ext.create('Ext.window.Window', {
            title: '从农合信息导入',
            width: 650,
            height: 300,
            plain: true,
            closeAction: 'hide', // 关闭窗口
            maximizable: false, // 最大化控制 值为true时可以最大化窗体
            layout: 'border',
            contentEl: 'tab'
        });
        win.show();
    }
}