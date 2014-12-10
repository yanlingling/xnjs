<?php $genid = gen_id();
require_javascript("og/jquery.min.js");
?>
<script>
    og.index = og.index || {};
    eval("og.loggedUser.departName='<?php echo $depart_name;?>'");
    eval("og.loggedUser.departid=<?php echo $depart_id;?>");
    og.index.showTaskModule = function (val) {
        if (panel = Ext.get('tabs-panel__mails-panel')) panel.setDisplayed(val);
        if (panel = Ext.get('tabs-panel__documents-panel')) panel.setDisplayed(val);
        if (panel = Ext.get('tabs-panel__tasks-panel')) panel.setDisplayed(val);
    }
    og.index.showLianzhengModule = function (val) {
        if (panel = Ext.get('tabs-panel__lianzhengxuexi-panel')) panel.setDisplayed(val);
        if (panel = Ext.get('tabs-panel__fengxiandian-panel')) panel.setDisplayed(val);
    }
    og.index.showDianzixiaonengModule = function (val) {
        if (panel = Ext.get('tabs-panel__dianzixiaoneng-panel')) panel.setDisplayed(val);
    }
    og.index.showReportModule = function (val) {
        if (panel = Ext.get('tabs-panel__report-panel')) panel.setDisplayed(val);
    }
    og.index.showZuofengModule = function (val) {
        if (panel = Ext.get('tabs-panel__kaoqin-panel')) panel.setDisplayed(val);
        if (panel = Ext.get('tabs-panel__zhibanzhang-panel')) panel.setDisplayed(val);
        if (panel = Ext.get('tabs-panel__qingxiaojia-panel')) panel.setDisplayed(val);
        if (panel = Ext.get('tabs-panel__xingfeng-panel')) panel.setDisplayed(val);
        if (panel = Ext.get('tabs-panel__file-panel')) panel.setDisplayed(val);
        if (panel = Ext.get('tabs-panel__outregist-panel')) panel.setDisplayed(val);
        if (og.loggedUser.userRole == '科长' || og.loggedUser.userRole == '局长' || og.loggedUser.userRole == '副局长') {
            if (panel = Ext.get('tabs-panel__carmanage-panel')) panel.setDisplayed(val);
        } else {
            if (panel = Ext.get('tabs-panel__carmanage-panel')) panel.setDisplayed(false);
        }
    }
    og.index.openTask = function () {
        og.index.showTaskModule(true);
        og.index.showZuofengModule(false);
        og.index.showReportModule(false);
        og.index.showLianzhengModule(false);
        og.index.showDianzixiaonengModule(false);
        Ext.getCmp('tabs-panel').setActiveTab('tasks-panel');
    }
    og.index.openLianzheng = function () {
        og.index.showTaskModule(false);
        og.index.showZuofengModule(false);
        og.index.showReportModule(false);
        og.index.showDianzixiaonengModule(false);
        og.index.showLianzhengModule(true);
        Ext.getCmp('tabs-panel').setActiveTab('lianzhengxuexi-panel');
    }
    og.index.openZuofeng = function () {
        og.index.showTaskModule(false);
        og.index.showLianzhengModule(false);
        og.index.showReportModule(false);
        og.index.showDianzixiaonengModule(false);
        og.index.showZuofengModule(true);
        Ext.getCmp('tabs-panel').setActiveTab('outregist-panel');
    }
    og.index.openJianbao= function () {
        og.index.showTaskModule(false);
        og.index.showLianzhengModule(false);
        og.index.showZuofengModule(false);
        og.index.showDianzixiaonengModule(false);
        og.index.showReportModule(true);
        Ext.getCmp('tabs-panel').setActiveTab('report-panel');
    }
    og.index.openDianzixiaoneng = function () {
        og.index.showTaskModule(false);
        og.index.showLianzhengModule(false);
        og.index.showZuofengModule(false);
        og.index.showReportModule(false);
        og.index.showDianzixiaonengModule(true);
        Ext.getCmp('tabs-panel').setActiveTab('dianzixiaoneng-panel');
    }
</script>


<div class="index-nav-block" style="position: relative">
    <ul class="dash-list" >
        <li class="item" >
            <div class="inner-border"></div>
            <div class="content">
                <div class="right">
                    <h1 class="title">岗位职责
                        <i class="font-icon-new"></i>
                    </h1>
                    <div class="description">
                        转变工作作风, 增强服务意识, 提高机关效能
                    </div>
                </div>
                <div class="left">
                    <i class="app-icon app-icon-task"></i>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="item-button status-" >
                <div class="item-line"></div>
                <a class="status-0" onclick="og.index.openTask()">进入</a>
            </div>
        </li>

        <li class="item" >
            <div class="inner-border"></div>
            <div class="content">
                <div class="right">
                    <h1 class="title">廉政责任
                        <i class="font-icon-new"></i>
                    </h1>
                    <div class="description">
                        爱岗敬业, 公正廉洁, 诚实守信，执法为民
                    </div>
                </div>
                <div class="left">
                    <i class="app-icon app-icon-lianzheng"></i>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="item-button status-" >
                <div class="item-line"></div>
                <a class="status-0" onclick="og.index.openLianzheng()">进入</a>
            </div>
        </li>


        <li class="item" >
            <div class="inner-border"></div>
            <div class="content">
                <div class="right">
                    <h1 class="title">机关作风
                        <i class="font-icon-new"></i>
                    </h1>
                    <div class="description">
                        为群众办事，请群众监督，让群众满意
                    </div>
                </div>
                <div class="left">
                    <i class="app-icon app-icon-zuofeng"></i>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="item-button status-" >
                <div class="item-line"></div>
                <a class="status-0"   onclick="og.index.openZuofeng()">进入</a>
            </div>
        </li>


        <li class="item" >
            <div class="inner-border"></div>
            <div class="content">
                <div class="right">
                    <h1 class="title">效能简报
                        <i class="font-icon-new"></i>
                    </h1>
                    <div class="description">
                        通报工作情况，交流工作心得，增强集体战斗力
                    </div>
                </div>
                <div class="left">
                    <i class="app-icon app-icon-jianbao"></i>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="item-button status-" >
                <div class="item-line"></div>
                <a class="status-0"   onclick="og.index.openJianbao()">进入</a>
            </div>
        </li>
        <li class="item" >
            <div class="inner-border"></div>
            <div class="content">
                <div class="right">
                    <h1 class="title">电子效能监察
                    </h1>
                    <div class="description">
                        电子效能监察....
                    </div>
                </div>
                <div class="left">
                    <i class="app-icon app-icon-dianzixiaoneng"></i>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="item-button status-" >
                <div class="item-line"></div>
                <a class="status-0"   onclick="og.index.openDianzixiaoneng()">进入</a>
            </div>
        </li>
    </ul>

    <table style="width: 100%;display: none">
        <tr>
            <td>
                <div class="nav-block1" id="nav-block1" onclick="og.index.openTask()"></div>
                <div class="nav-char" onclick="og.index.openTask()">岗位职责</div>
            </td>
            <td>
                <div class="nav-block2" onclick="og.index.openLianzheng()" id="nav-block2"></div>
                <div class="nav-char" onclick="og.index.openLianzheng()">廉政责任</div>
            </td>
            <td>
                <div class="nav-block3" onclick="og.index.openZuofeng()" id="nav-block3"></div>
                <div class="nav-char">机关作风</div>
            </td>
        </tr>
    </table>

    <div class="message-center" id="message-center">
        <div style="height: 20px">
            <span class="close-icon" id="mes-close"></span>
        </div>
        <div id="message-content">

        </div>
        <div class="message-horse">

        </div>
    </div>
</div>

<script>
    (function () {
        var tipHolidayApplyCount = <?php echo $holidayApplyCount;?>;
        var tipTaskDelayApplyCount = <?php echo $taskDelayApplyCount;?>;
        var tipLearningCount = <?php echo $learningCount;?>;
        var tipRiskCount = <?php echo $riskCount;?>;
        var tipFileCount = <?php echo $fileCount;?>;
        var tipTaskCount = <?php echo $taskCount;?>;
        var tipCarCount = <?php echo $carApplyCount;?>;
        var tipOnDuty= <?php echo $is_on_duty;?>;
        var toCommentCount= <?php echo $toCommentCount;?>;
        var htmlStr = '';
        if (tipTaskCount != 0) {
            htmlStr += '<p onclick=og.dashboard.go("task")>您有<span class="red">' + tipTaskCount + '</span>个即将到期的岗位职责</p>';
        }
        if (toCommentCount!= 0) {
            htmlStr += '<p onclick=og.dashboard.go("comment")>您有<span class="red">' +toCommentCount + '</span>个待评价的岗位职责</p>';
        }

        if (tipCarCount != 0) {
            htmlStr += '<p onclick=og.dashboard.go("car")>您有<span class="red">' + tipCarCount + '</span>条未处理的用车申请</p>';
        }
        if (tipHolidayApplyCount != 0) {
            htmlStr += '<p onclick=og.dashboard.go("qingxiaojia")>您有<span class="red">' + tipHolidayApplyCount + '</span>条未处理的请假申请</p>';
        }
        if (tipTaskDelayApplyCount != 0) {
            htmlStr += '<p onclick=og.dashboard.go("taskDelay")>您有<span class="red">' + tipTaskDelayApplyCount + '</span>条未处理的任务延期申请</p>';
        }
        if (tipFileCount != 0) {
            htmlStr += '<p onclick=og.dashboard.go("file")>您有<span class="red">' + tipFileCount + '</span>个待阅读文件</p>';
        }
        if (tipLearningCount != 0) {
            htmlStr += '<p onclick=og.dashboard.go("learning")>您有<span class="red">' + tipLearningCount + '</span>个即将到期的廉政学习</p>';
        }
        if (tipRiskCount != 0) {
            htmlStr += '<p onclick=og.dashboard.go("risk")>您有<span class="red">' + tipRiskCount + '</span>个即将到期的风险点自查自控</p>';
        }
        if (tipOnDuty == 1) {
            htmlStr += '<p onclick=og.dashboard.go("duty")>您是今天的值班长，请及时填写值班长日志</p>';
        }

        if (htmlStr != '') {
            $("#message-content").html(htmlStr);
            $("#message-center").animate({right: "15px"}, 1000);
            $('#mes-close').click(function () {
                $("#message-center").hide();
            });
        }
    })()
    og.dashboard={};
    og.dashboard.go = function (des){
        var url='';
         switch (des){
             case 'task':
                 url = og.getUrl('newtask', 'new_list_tasks');
                 break;
             case 'comment':
                 if (og.loggedUser.userRole == '科长') {
                     url = og.getUrl('newtask', 'new_list_tasks',{'tab': 'comment'});
                 } else {
                     url = og.getUrl('newtask', 'new_list_tasks_of_juzhang');
                 }
                 break;
             case 'car':
                 url = og.getUrl('carmanage', 'index');
                 break;
             case 'qingxiaojia':
                 url =  og.getUrl('qingxiaojia', 'index',{'tab': 'handle'});;
              break;
             case 'taskDelay':
                url = og.getUrl('newtask', 'new_list_tasks_of_juzhang');;
             break;
             case 'file':
                 url = og.getUrl('file', 'index');;
                 break;
             case 'learning':
                 url = og.getUrl('lianzhengxuexi', 'index')
             break;
             case 'risk':
                 url = og.getUrl('fengxiandian', 'index')
             break;
             case 'duty':
                 url = og.getUrl('zhibanzhang', 'index')
                 break;
         }
        og.openLink(url, {});
    }


</script>

