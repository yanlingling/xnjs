var USER_ROLE_JUZHANG = '局长';
var USER_ROLE_FUJUZHANG = '副局长';
var USER_ROLE_KEZHANG = '科长';
var USER_ROLE_KEYUAN = '科员';
Ext.onReady(function () {
    Ext.get("loading").hide();

    // fix cursor not showing on message boxs
    Ext.MessageBox.getDialog().on("show", function (d) {
        var div = Ext.get(d.el);
        div.setStyle("overflow", "auto");
        var text = div.select(".ext-mb-textarea", true);
        if (!text.item(0))
            text = div.select(".ext-mb-text", true);
        if (text.item(0))
            text.item(0).dom.select();
    });

    if (og.preferences['rememberGUIState']) {
        Ext.state.Manager.setProvider(new og.HttpProvider({
            saveUrl: og.getUrl('gui', 'save_state'),
            readUrl: og.getUrl('gui', 'read_state'),
            autoRead: false
        }));
        Ext.state.Manager.getProvider().initState(og.initialGUIState);
    }

    Ext.QuickTips.init();

    // SETUP PANEL LAYOUT
    og.panels = {};
    var taskAction = 'new_list_tasks';
    var lianzhengAction = 'index';
/**/
    if (og.loggedUser.userRole == '局长' || og.loggedUser.userRole == '副局长') {
       taskAction = 'new_list_tasks_of_juzhang';
        //lianzhengAction = 'index_of_juzhang';
    }


    var panels = [
        og.panels.overview = new og.ContentPanel({
            title: langhtml('overview'),
            id: 'overview-panel',
            iconCls: 'ico-overview',
            refreshOnWorkspaceChange: true,
            refreshOnTagChange: true,
            defaultContent: {
                type: "url",
                data: og.getUrl('newdashboard', 'index')
            }
        }),
        og.panels.notes = new og.ContentPanel({
            title: lang('messages'),
            id: 'messages-panel',
            iconCls: 'ico-messages-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('message', 'init')
            }
        }),
        og.panels.tasks = new og.ContentPanel({
            title: lang('tasks'),
            id: 'tasks-panel',
            iconCls: 'ico-tasks-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: "url",
                data: og.getUrl('newtask', taskAction)
            }
        }),
        og.panels.email = new og.ContentPanel({
            title: '法定职责',
            id: 'mails-panel',
            iconCls: 'ico-webpages-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: "url",
                data: og.getUrl('newtask2', 'content')
            }
        }),
        og.panels.contacts = new og.ContentPanel({
            title: lang('contacts'),
            id: 'contacts-panel',
            iconCls: 'ico-contacts-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('contact', 'init')
            }
        }),



        /*og.panels.tasks2 = new og.ContentPanel({
            title: '法定职责',
            id: 'tasks2-panel',
            iconCls: 'ico-webpages-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: "url",
                data: og.getUrl('newtask2', 'content')
            }
        }),*/


        og.panels.documents = new og.ContentPanel({
            title: '岗位职责',
            id: 'documents-panel',
            iconCls: 'ico-documents-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('newtask3', 'content')
            }
        }),
        /* og.panels.tasks3 = new og.ContentPanel({
            title: '岗位职责',
            id: 'tasks3-panel',
            iconCls: 'ico-documents-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: "url",
                data: og.getUrl('newtask3', 'content')
            }
        }), */
        og.panels.lianzhengxuexi = new og.ContentPanel({
            title: '廉政学习',
            id: 'lianzhengxuexi-panel',
            iconCls: 'ico-webpages-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('lianzhengxuexi', lianzhengAction)
            }
        }),
        og.panels.fengxiandian = new og.ContentPanel({
            title: '风险点自查自控',
            id: 'fengxiandian-panel',
            iconCls: 'ico-time-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: "url",
                data: og.getUrl('fengxiandian', 'index')
            }
        }),
        og.panels.yigangshuangze = new og.ContentPanel({
            title: '一岗双责责任书',
            id: 'yigangshuangze-panel',
            iconCls: 'ico-reporting-layout',
            //refreshOnWorkspaceChange: true,
            defaultContent: {
                type: "url",
                data: og.getUrl('yigangshuangze', 'index')
            }
        }) ,
        og.panels.wushu = new og.ContentPanel({
            title: '述德述学述职述法述廉',
            id: 'wushu-panel',
            iconCls: 'ico-calendar-layout',
            refreshOnWorkspaceChange: true,
            refreshOnTagChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('wushu', 'index')
            }
        }),
        og.panels.dianzixiaoneng = new og.ContentPanel({
            title: '电子效能监察',
            id: 'dianzixiaoneng-panel',
            iconCls: 'ico-calendar-layout',
            refreshOnWorkspaceChange: true,
            refreshOnTagChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('dianzixiaoneng', 'index')
            }
        }),
        og.panels.outregist = new og.ContentPanel({
            title: '考勤状态',
            id: 'outregist-panel',
            iconCls: 'ico-time-layout',
            refreshOnWorkspaceChange: true,
            refreshOnTagChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('outregist', 'index')
            }
        }),
        og.panels.zhibanzhang = new og.ContentPanel({
            title: '值班长日志',
            id: 'zhibanzhang-panel',
            iconCls: 'ico-time-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: "url",
                data: og.getUrl('zhibanzhang', 'index')
            }
        }),
        og.panels.qingxiaojia = new og.ContentPanel({
            title: '请假管理',
            id: 'qingxiaojia-panel',
            iconCls: 'ico-reporting-layout',
            //refreshOnWorkspaceChange: true,
            defaultContent: {
                type: "url",
                data: og.getUrl('qingxiaojia', 'index')
            }
        }) ,

        og.panels.kaoqin = new og.ContentPanel({
            title: '考勤督察',
            id: 'kaoqin-panel',
            iconCls: 'ico-webpages-layout',
            refreshOnWorkspaceChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('kaoqin', 'index')
            }
        }),


        /*og.panels.xingfeng = new og.ContentPanel({
            title: '行风监督',
            id: 'xingfeng-panel',
            iconCls: 'ico-calendar-layout',
            refreshOnWorkspaceChange: true,
            refreshOnTagChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('xingfeng', 'index')
            }
        }),*/


        og.panels.carmanage = new og.ContentPanel({
            title: '车辆管理',
            id: 'carmanage-panel',
            iconCls: 'ico-report-layout',
            refreshOnWorkspaceChange: true,
            refreshOnTagChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('carmanage', 'index')
            }
        }),

        og.panels.file= new og.ContentPanel({
            title: '文件传阅',
            id: 'file-panel',
            iconCls: 'ico-report-layout',
            refreshOnWorkspaceChange: true,
            refreshOnTagChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('file', 'index')
                }
        }),

        og.panels.report= new og.ContentPanel({
            title: '效能简报',
            id: 'report-panel',
            iconCls: 'ico-report-layout',
            refreshOnWorkspaceChange: true,
            refreshOnTagChange: true,
            defaultContent: {
                type: 'url',
                data: og.getUrl('report', 'index')
            }
        })
    ];
    var tab_panel = new Ext.TabPanel({
        id: 'tabs-panel',
        region: 'center',
        activeTab: 0,
        enableTabScroll: true,
        items: panels,
        listeners: {
            'render': function () {
                if (panel = Ext.get('tabs-panel__mails-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__documents-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__tasks-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__contacts-panel')) panel.setDisplayed(false);
                // 廉政建设模块的panel
                if (panel = Ext.get('tabs-panel__lianzhengxuexi-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__fengxiandian-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__yigangshuangze-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__wushu-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__dianzixiaoneng-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__report-panel')) panel.setDisplayed(false);

                // z作风建设模块的panel
                if (panel = Ext.get('tabs-panel__kaoqin-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__file-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__qingxiaojia-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__xingfeng-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__zhibanzhang-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__outregist-panel')) panel.setDisplayed(false);
                if (panel = Ext.get('tabs-panel__carmanage-panel')) panel.setDisplayed(false);

                // hide disabled modules
               /* */if (!og.config['enable_notes_module']) if (panel = Ext.get('tabs-panel__messages-panel')) panel.setDisplayed(false);
                if (!og.config['enable_email_module']) if (panel = Ext.get('tabs-panel__mails-panel')) panel.setDisplayed(false);
                if (!og.config['enable_contacts_module']) if (panel = Ext.get('tabs-panel__contacts-panel')) panel.setDisplayed(false);
                if (!og.config['enable_calendar_module']) if (panel = Ext.get('tabs-panel__calendar-panel')) panel.setDisplayed(false);
                if (!og.config['enable_documents_module']) if (panel = Ext.get('tabs-panel__documents-panel')) panel.setDisplayed(false);
                if (!og.config['enable_tasks_module']) if (panel = Ext.get('tabs-panel__tasks-panel')) panel.setDisplayed(false);
                if (!og.config['enable_weblinks_module']) if (panel = Ext.get('tabs-panel__webpages-panel')) panel.setDisplayed(false);
                if (!og.config['enable_time_module']) if (panel = Ext.get('tabs-panel__time-panel')) panel.setDisplayed(false);
                if (!og.config['enable_reporting_module']) if (panel = Ext.get('tabs-panel__reporting-panel')) panel.setDisplayed(false);
            }
        }
    });

    // ENABLE / DISABLE MODULES
    var module_tabs = {
        'notes': ['tabs-panel__messages-panel'],
        'email': ['tabs-panel__mails-panel'],
        'contacts': ['tabs-panel__contacts-panel'],
        'calendar': ['tabs-panel__calendar-panel'],
        'tasks': ['tabs-panel__tasks-panel'],
        'tasks2': ['tabs-panel__tasks2-panel'],
        'tasks3': ['tabs-panel__tasks3-panel'],
        'documents': ['tabs-panel__documents-panel'],
        'weblinks': ['tabs-panel__webpages-panel'],
        'time': ['tabs-panel__time-panel'],
        'reporting': ['tabs-panel__reporting-panel']
    };
    og.eventManager.addListener('config option changed', function (option) {
        if (option.name.substring(0, 7) == 'enable_' && option.name.substring(option.name.length - 7) == '_module') {
            var module = option.name.substring(7, option.name.length - 7);
            var tabs = module_tabs[module] || [];
            for (var i = 0; i < tabs.length; i++) {
                Ext.get(tabs[i]).setDisplayed(option.value);
            }
        }
    });

    // BUILD VIEWPORT
    var viewport = new Ext.Viewport({
        layout: 'border',
        stateful: og.preferences['rememberGUIState'],

        items: [
            new Ext.BoxComponent({
                region: 'north',
                height: 70,
                el: 'header'
            }),
            new Ext.BoxComponent({
                region: 'south',
                el: 'footer'
            }),
            /*helpPanel = new og.HelpPanel({
             region: 'east',
             collapsible: true,
             collapsed: true,
             split: true,
             width: 225,
             minSize: 175,
             maxSize: 400,
             id: 'help-panel',
             title: lang('help'),
             iconCls: 'ico-help'
             }),*/
            {
                region: 'west',
                id: 'menu-panel',
                title: lang('workspaces'),
                iconCls: 'ico-workspaces',
                split: true,
                width: 200,
                bodyBorder: false,
                minSize: 175,
                maxSize: 400,
                collapsible: true,
                margins: '0 0 0 0',
                layout: 'border',
                stateful: og.preferences['rememberGUIState'],
                listeners: {
                    'render': function () {
                        Ext.get('menu-panel').setDisplayed(false);
                        document.getElementById('menu-panel').style.display = 'none';
                    }
                },
                items: [
                    {
                        id: 'workspaces-tree',
                        xtype: 'wspanel',
                        wstree: {
                            listeners: {
                                workspaceselect: function (ws) {
                                    og.eventManager.fireEvent('workspace changed', ws);
                                    og.updateWsCrumbs(ws);
                                }
                            },
                            autoLoadWorkspaces: true
                        },
                        listeners: {
                            render: function () {
                                this.getTopToolbar().setHeight(25);
                            }
                        }
                    },
                    {
                        xtype: 'tagpanel',
                        tagtree: {
                            listeners: {
                                tagselect: function (tag) {
                                    og.eventManager.fireEvent('tag changed', tag);
                                    og.updateWsCrumbsTag(tag);
                                }
                            },
                            autoLoadTags: true
                        }
                    }
                ]
            },
            Ext.getCmp('tabs-panel')
        ]
    });

    og.captureLinks();
    if (og.preferences['email_polling'] > 0) {
        function updateUnreadCount() {
            og.openLink(og.getUrl('mail', 'get_unread_count'), {
                onSuccess: function (d) {
                    if (typeof d.unreadCount != 'undefined') {
                        og.updateUnreadEmail(d.unreadCount);
                    }
                },
                hideLoading: true,
                hideErrors: true,
                preventPanelLoad: true
            });
        }

        updateUnreadCount();
        setInterval(updateUnreadCount, Math.max(og.preferences['email_polling'], 5000));
    }

    if (og.hasNewVersions) {
        og.msg(lang('new version notification title'), og.hasNewVersions, 0);
    }
});