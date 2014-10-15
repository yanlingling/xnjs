<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/risk/risk.js');
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
<div>
    <div class="sub-tab">
        <span id='my-risk-tab'
              class="<?php echo $tab == 'person' ? 'sub-tab-content' : '';?>  ?>"><?php //echo $departName; ?>个人风险点</span>
        <span class="<?php
        echo $role == '科长' ? '':'hide'?>";>&nbsp;| &nbsp;</span>

        <span id='depart-risk-tab' class="<?php
        echo $tab == 'depart' ? 'sub-tab-content' : '';
        echo $role == '科长' ? '':'hide'; ?>">
             科室风险点</span>

        <span class="<?php
        echo $canCreateRisk == 0 ? 'hide' : '';?>";>&nbsp;| &nbsp;</span>

        <span id='risk-content-tab' class="<?php
        echo $tab == 'question' ? 'sub-tab-content' : '';
        echo $canCreateRisk == 0 ? '' : ''; ?>">
             风险点问卷</span>
    </div>
    <div class="clearFloat"></div>
</div>

<!-- 个人风险点-->
<div class="content-wraper" id="my-risk-tab-content">
    <div class="task-bulletin">
        <table width="100%" border="0" class="og-table" style="text-align: center">
            <tr style="color:#ACA4A4">
                <td rowspan="2"
                    style="vertical-align: middle;font-weight: bold;color:#000">
                    <?php
                    echo $departName;
                    ?>
                </td>
                <td>红灯防控问卷</td>
                <td>黄灯防控问卷</td>

                <td>进行中防控问券</td>
                <td>已完成防控问卷</td>
                <td>个人得分</td>
            </tr>
            <tr>
                <td id="light-count-4">0</td>
                <td id="light-count-3">0</td>
                <td id="light-count-1">0</td>
                <td id="light-count-1">0</td>
                <td id="person-score"></td>
            </tr>

        </table>
    </div>

    <div id="createLearningBtn" style="margin-top: 10px" class="<?php echo $role == '科长' ? '' : 'hide' ?>">
        <span class="new-button" id='view-by-depart'   onclick="og.risk.goToDepartLearning(<?php echo $risk_list[1]['depart_id'] ?>)">我的科室</span>
    </div>

    <div id="createLearningBtn" style="margin-top: 10px" class="<?php echo $role == '局长' ? '' : 'hide' ?>">
        <span class="new-button" id='view-by-depart'   onclick="og.risk.goToAllDepartLearning()"> 查看全局</span>
    </div>

    <div class="table-header">
        <table width='100%' class="og-table">
            <tr>
                <td class="ld1">学习内容</td>
                <td class="ld2">截止时间</td>
                <td class="ld6">完成时间</td>
                <td class="ld3">状态</td>
                <td class="ld4">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    $lateLearning = array();
    foreach ($risk_list as $item) {

        $j = explode(" ", $item['due_date']);
// 判断是否能申请延期的时候 要用原始的时间 ，精确到秒
        $raw_time = $item['due_date'];
        $item['due_date'] = $j[0];

        ?>

        <div id="risk-list-<?php echo $item['riskId']; ?>">
            <table width=100% class="og-table">
                <?php
                $i++;
                if ($i % 2 == 0) {
                    echo '<tr>';
                } else {
                    echo '<tr class="dashaltrow">';
                }?>
                <td class='ld1' title=" <?php echo $item['name'] ?>">
                    <?php echo mb_substr($item['name'], 0, 18, "UTF-8"); ?>
                </td>


                <td class='ld2'><?php  echo $item['due_date'];
                    $dateDiff = (strtotime($j[0]) - strtotime(date('y-m-d', time()))) / 86400;
                    // 最近7天要到期的任务，还没完成 都要提醒
                    if ($dateDiff >= 0 && $dateDiff <= 6 && $item['status'] != 1) {
                        array_push($lateLearning, $item['name']);
                    }
                    ?></td>
                <td class="ld6"><?php echo $item['complete_on']=='0000-00-00 00:00:00'? '-':$item['complete_on']; ?></td>

                <td class='ld3'><?php echo getLightStatus($item['status']) ?></td>
                <td class='ld4'><?php echo getRiskOptContent($item['status'], $item['riskId'], $item['contentId'],$selfView) ?></td>
                </tr>
            </table>
        </div>
    <?php
    }
    if ($i == 0) {
        ?>
        <div class="no-data">当前暂无相关数据</div>
    <?php
    }
    ?>
</div>



<!--风险点问卷管理面板-->
<div id="risk-content-tab-content" class="hide">
    <div id="createLearningBtn" style="margin-top: 10px">
        <span class="new-button <?php echo $canCreateLearning == 0 ? 'hide' : '' ?>" id='add-text-risk'   onclick="og.risk.addPersonQuestion()">新建个人问卷</span>
        <span class="new-button <?php echo $canCreateLearning == 0 ? 'hide' : '' ?>" id='add-vedio-risk'   onclick="og.risk.addDepartQuestion()">新建科室问卷</span>
    </div>
    <div class="table-header">
        <table width='100%' class="og-table">
            <tr>
                <td class="lcontent1">问卷名称</td>
                <td class="lcontent2">截止时间</td>
                <td class="lcontent3">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    foreach ($risk_content_list as $item) {

        $j = explode(" ", $item['due_date']);
// 判断是否能申请延期的时候 要用原始的时间 ，精确到秒
        $raw_time = $item['due_date'];
        $item['due_date'] = $j[0];

        ?>

        <div id="supervise-list-<?php echo $item['id']; ?>">
            <table width=100% class="og-table">
                <?php
                $i++;
                if ($i % 2 == 0) {
                    echo '<tr>';
                } else {
                    echo '<tr class="dashaltrow">';
                }?>
                <td class='lcontent1' title=" <?php echo $item['name'] ?>">
                    <?php echo mb_substr($item['name'], 0, 18, "UTF-8"); ?>
                </td>
                <td class="lcontent2"><?php echo $item['due_date'] ?></td>

                <td class='lcontent3'><?php echo getLearningContentOpt($item['id']);?></td>
                </tr>
            </table>
        </div>
    <?php
    }
    if ($i == 0) {
        ?>
        <div class="no-data">当前暂无相关数据</div>
    <?php
    }
    ?>
</div>
<?php
function getLearningContentOpt($id)
{
    return "<a onclick=og.risk.delLearnContent($id)>删除</a>".
    "<a onclick=og.risk.editLearnContent($id)>&nbsp;&nbsp;编辑</a>" ;

}
function getRiskOptContent($status, $riskId, $contentId,$isSelf)
{
    // 如果是局长或者科长进入的话，只有查看功能
    if(!$isSelf){
        return "<a onclick=og.risk.toRiskContent($riskId,$contentId,'view')>查看</a>";
    }else{
        if ($status == 1) {
            return "<a onclick=og.risk.toRiskContent($riskId,$contentId,'view')>查看</a>";
        } else {
            return "<a onclick=og.risk.toRiskContent($riskId,$contentId,'learn')>开始答题</a>";
        }
    }

}

?>

<script>
    function renderBulletin() {
        eval('var riskOverviewData=<?php echo $risk_overview_data;?>');
        var redLightCount = 0;
        var yellowLightCount = 0;
        var baseScore = riskOverviewData[0].score || 100;
        for (var i = 0, item; item = riskOverviewData[i++];) {
            var status = item['status'];
            if (status == '4') {
                redLightCount = +item['count'];
            }
            if (status == '3') {
                yellowLightCount = +item['count'];
            }
            $('#light-count-' + status).html(item['light_count']);
        }
        $('#person-score').html(baseScore);
    }
    //renderBulletin();

    function showSubTab(ele) {
        $('.sub-tab span').removeClass('sub-tab-content');
        ele.addClass('sub-tab-content');
        $('#my-risk-tab-content').addClass('hide');
        $('#depart-risk-tab-content').addClass('hide');
        $('#risk-content-tab-content').addClass('hide');
        if (ele.html() == '个人风险点') {
            $('#my-risk-tab-content').removeClass('hide');
        } else if (ele.html() == '科室风险点') {
            $('#depart-risk-tab-content').removeClass('hide');
        }
        else {
            $('#risk-content-tab-content').removeClass('hide');
        }
    }
    $('.sub-tab span').click(function () {
        var ele = $(this);
        og.taskSubTab = ele.attr('id');
        showSubTab(ele);
    });
</script>