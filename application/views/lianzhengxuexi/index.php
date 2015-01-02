<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/learning/learning.js');
require_javascript("og/common.js");
require_javascript("og/jquery.min.js");
$genid = gen_id();

?>
<div>
    <input id="user-id" type="hidden" value="<?php echo $uid ;?>"/>
    <div class="sub-tab">
        <span id='my-learning-tab'
              class="<?php echo $tab == 'apply' ? '' : 'sub-tab-content'; ?>"><?php //echo $departName; ?>
            必学内容</span>

        <span id='optional-learning-tab'>
            选学内容
        </span>

        <span id='learning-content-tab' class="<?php
        echo $tab == 'apply' ? 'sub-tab-content' : '';
        echo ($canCreateLearning == 1 && $selfView) ? '' : 'hide'; ?>">
            学习内容</span>
        <div class='year-select-area'>
            <input type="radio" value="2015" name="task-year-selector" onclick="og.learning.onselectyear()">&nbsp;2015
            <input type="radio" value="2014" name="task-year-selector" onclick="og.learning.onselectyear()">&nbsp;2014
        </div>
    </div>


    <div class="clearFloat"></div>
</div>

<!-- 必学学习-->
<div class="content-wraper" id="my-learning-tab-content">
    <div class="task-bulletin">
        <table width="100%" border="0" class="og-table" style="text-align: center">
            <tr style="color:#ACA4A4">
                <td rowspan="2"
                    style="vertical-align: middle;font-weight: bold;color:#000">
                    <?php
                    echo $departName;
                    ?>
                </td>
                <td>红灯学习内容</td>
                <td>黄灯学习内容</td>

                <td>进行中学习内容</td>
                <td>已完成学习内容</td>
                <td>廉能得分</td>
            </tr>
            <tr>
                <td id="light-count-4">0</td>
                <td id="light-count-3">0</td>
                <td id="light-count-2">0</td>
                <td id="light-count-1">0</td>
                <td id="person-score"></td>
            </tr>

        </table>
    </div>

    <span style="margin-top: 10px" class="<?php echo ($role == '局长' || $role == '副局长' ||  $canCreateLearning == 1) ? '' : 'hide' ?>">
        <span class="new-button" id='view-by-depart' onclick="og.learning.goToAllDepartLearning()">查看科室</span>
    </span>

    <span id="createLearningBtn" style="margin-top: 10px" class="<?php echo $role == '科长' ? '' : 'hide' ?>">
        <span class="new-button" id='view-by-depart'
              onclick="og.learning.goToDepartLearning(<?php echo $depart_id ?>)">我的科室</span>
    </span>

    <div class="table-header">
        <table width='100%' class="og-table">
            <tr>
                <td class="ld1">学习内容</td>
                <td class="ld2">截止时间</td>
                <td class="ld6">完成时间</td>
                <?php echo ($role == '局长') ? '<td  class="ld9">学习时长</td>' : '' ?>
                <td class="ld3">学习状态</td>
                <td class="ld4">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    $lateLearning = array();
    foreach ($learning_list as $item) {

        $j = explode(" ", $item['due_date']);
// 判断是否能申请延期的时候 要用原始的时间 ，精确到秒
        $raw_time = $item['due_date'];
        $item['due_date'] = $j[0];

        ?>

        <div id="learning-list-<?php echo $item['learningId']; ?>">
            <table width=100% class="og-table">
                <?php
                $i++;
                if ($i % 2 == 0) {
                    echo '<tr>';
                } else {
                    echo '<tr class="dashaltrow">';
                }?>
                <td class='ld1' title=" <?php echo $item['name'] ?>">
                    <?php echo mb_substr($item['name'], 0, 18, "UTF-8");
                    if (daysFromNow($item['create_time']) > -3) {
                        echo "<span class='new-icon'></span>";
                    }
                    ?>

                </td>


                <td class='ld2'><?php  echo $item['due_date'];
                    $dateDiff = (strtotime($j[0]) - strtotime(date('y-m-d', time()))) / 86400;
                    // 最近7天要到期的任务，还没完成 都要提醒
                    if ($dateDiff >= 0 && $dateDiff <= 6 && $item['status'] != 1) {
                        array_push($lateLearning, $item['name']);
                    }
                    ?></td>
                <td class="ld6"><?php echo $item['complete_on'] == '0000-00-00 00:00:00' ? '-' : $item['complete_on']; ?></td>
                <?php echo ($role == '局长') ? getLearnTimeLong($item['time_long']) : '' ?>
                <td class='ld3'><?php echo getLightStatus($item['status']) ?></td>
                <td class='ld4'><?php echo getLearningOptContent($item['status'], $item['learningId'], $item['contentId'], $selfView) ?></td>
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

<!-- 选学学习-->
<div class="content-wraper hide" id="optional-learning-tab-content">

    <div class="table-header">
        <table width='100%' class="og-table">
            <tr>
                <td class="ld1">学习内容</td>
                <td class="ld22">创建时间</td>
                <td class="ld4">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    $lateLearning = array();
    foreach ($optional_learning_list as $item) {

        ?>

        <div id="learning-list-<?php echo $item['learningId']; ?>">
            <table width=100% class="og-table">
                <?php
                $i++;
                if ($i % 2 == 0) {
                    echo '<tr>';
                } else {
                    echo '<tr class="dashaltrow">';
                }?>
                <td class='ld1' title=" <?php echo $item['name'] ?>">
                    <?php echo mb_substr($item['name'], 0, 18, "UTF-8");
                    if (daysFromNow($item['create_time']) > -3) {
                        echo "<span class='new-icon'></span>";
                    }
                    ?>

                </td>
                <td class='ld22'><?php echo $item['create_time'];?></td>
                <td class='ld4'><?php echo getOptionalLearningOptContent($item['learningId'], $item['contentId']);?></td>
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



<!--督察学习面板-->
<div id="supervise-tab-content" class="hide">
    <div class="table-header">
        <table width='100%' class="og-table">
            <tr>
                <td class="ld1">学习内容</td>
                <td class="ld7">学习人</td>
                <td class="ld2">截止时间</td>
                <td class="ld6">完成时间</td>
                <td class="ld3">状态</td>
                <td class="ld4">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    foreach ($supervise_list as $item) {

        $j = explode(" ", $item['due_date']);
// 判断是否能申请延期的时候 要用原始的时间 ，精确到秒
        $raw_time = $item['due_date'];
        $item['due_date'] = $j[0];

        ?>

        <div id="supervise-list-<?php echo $item['learningId']; ?>">
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
                <td class="ld7"><?php echo $item['user_name'] ?></td>

                <td class='ld2'><?php  echo $item['due_date'];
                    $dateDiff = (strtotime($j[0]) - strtotime(date('y-m-d', time()))) / 86400;
                    // 最近7天要到期的任务，还没完成 都要提醒
                    if ($dateDiff >= 0 && $dateDiff <= 6 && $item['status'] != 1) {
                        array_push($lateLearning, $item['name']);
                    }
                    ?></td>
                <td class="ld6"><?php echo $item['complete_on'] == '0000-00-00 00:00:00' ? '-' : $item['complete_on']; ?></td>

                <td class='ld3'><?php echo getLightStatus($item['status']) ?></td>
                <td class='ld4'><?php echo getLearningSuperviseOpt($item['supervise_status'], $item['learningId'], $item['due_date'], $item['userId']) ?></td>
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
function getLearnTimeLong($timeLong)
{
    if ($timeLong == 0) {
        return "<td class='ld9'>-</td>";
    }
    $hour = floor($timeLong / (60 * 60));
    $minitue = floor($timeLong / (60)) - $hour * 60;
    if ($minitue < 10) {
        $minitue = '0' . $minitue;
    }
    $second = $timeLong - $hour * 60 * 60 - $minitue * 60;
    if ($second < 10) {
        $second = '0' . $second;
    }

    return "<td class='ld9'>" . $hour . "时" . $minitue . "分" . $second . "秒</td>";

}


function getLearningSuperviseOpt($superviseStatus, $learningId, $due_date, $userid)
{
    if ($superviseStatus == 1) {
        return "<a onclick=og.learning.passSupervise($learningId)>通过</a>&nbsp;&nbsp;" .
        "<a onclick=og.learning.rejectSupervise($learningId,$due_date,$userid)>不通过</a>";
    } else {
        return '-';
    }
}

?>
<!--学习内容管理面板-->
<div id="leaning-content-tab-content" class="hide">
    <div id="createLearningBtn" style="margin-top: 10px">
        <span class="new-button <?php echo $canCreateLearning == 0 ? 'hide' : '' ?>" id='add-text-learning'
              onclick="og.learning.addTextLearning()">新建学习</span>
        <span class="new-button <?php echo $canCreateLearning == 0 ? 'hide' : '' ?>" id='add-vedio-learning'
              onclick="og.learning.addVedioLearning()">新建视频学习</span>
    </div>
    <div class="table-header">
        <table width='100%' class="og-table">
            <tr>
                <td class="lcontent1">学习内容</td>
                <td class="lcontent2">截止时间</td>
                <td class="lcontent4">是否必学</td>
                <td class="lcontent3">操作</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    foreach ($learning_content_list as $item) {
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
                <td class='lcontent1' style="text-align: left"   title=" <?php echo $item['name'] ?>">
                    <?php echo mb_substr($item['name'], 0, 18, "UTF-8"); ?>
                </td>
                <td class="lcontent2"><?php echo $item['must_learn']==1?$item['due_date']:'-' ?></td>
                <td class="lcontent4"><?php echo $item['must_learn']==1?'是':'否' ?></td>
                <td class='lcontent3'><?php echo getLearningContentOpt($item['id']); ?></td>
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
    return "<a onclick=og.learning.delLearnContent($id)>删除</a>" .
    "<a onclick=og.learning.editLearnContent($id)>&nbsp;&nbsp;编辑</a>";

}

function getLearningOptContent($status, $learningId, $contentId, $isSelf)
{
    // 如果是局长或者科长进入的话，只有查看功能
    if (!$isSelf) {
        return "<a onclick=og.learning.toLearnContent($learningId,$contentId,'view')>查看</a>";
    } else {
        if ($status == 1) {
            return "<a onclick=og.learning.toLearnContent($learningId,$contentId,'view')>查看</a>";
        } else {
            return "<a onclick=og.learning.toLearnContent($learningId,$contentId,'learn')>开始学习</a>";
        }
    }

}

function getOptionalLearningOptContent($learningId, $contentId)
{
        return "<a onclick=og.learning.toLearnContent($learningId,$contentId,'view')>查看</a>";
}
?>

<script>
    function renderBulletin() {
        eval('var learningOverviewData=<?php echo $learning_overview_data;?>');
        eval('var personScore=<?php echo $personScore;?>');
        var redLightCount = 0;
        var yellowLightCount = 0;
        var baseScore = personScore;
        if (learningOverviewData) {
            for (var i = 0, item; item = learningOverviewData[i++];) {
                var status = item['status'];
                if (status == '4') {
                    redLightCount = +item['count'];
                }
                if (status == '3') {
                    yellowLightCount = +item['count'];
                }
                $('#light-count-' + status).html(item['light_count']);
            }
        }
        $('#person-score').html(baseScore);
    }
    renderBulletin();

    // 用户点击过tab的切换，按用户点击的来
    if (typeof og.learningSubTab != 'undefined') {
        showSubTab($('#' + og.learningSubTab ));
    }
    function showSubTab(ele) {
        $('.sub-tab span').removeClass('sub-tab-content');
        ele.addClass('sub-tab-content');
        $('#my-learning-tab-content').addClass('hide');
        $('#supervise-tab-content').addClass('hide');
        $('#leaning-content-tab-content').addClass('hide');
        $('#optional-learning-tab-content').addClass('hide');

        var htmlStr = $.trim(ele.html());
        if (htmlStr == '必学内容') {
            $('#my-learning-tab-content').removeClass('hide');
        } else if (htmlStr == '督察学习内容') {
            $('#supervise-tab-content').removeClass('hide');
        }else if(htmlStr == '选学内容'){
            $('#optional-learning-tab-content').removeClass('hide');
        }
        else {
            $('#leaning-content-tab-content').removeClass('hide');
        }
    }
    $('.sub-tab span').click(function () {
        var ele = $(this);
        og.learningSubTab = ele.attr('id');
        showSubTab(ele);
    });
    og.learning&& og.learning.initYear(<?php echo $year;?>);
</script>
