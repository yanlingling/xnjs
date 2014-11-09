<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/duty/duty.js');
require_javascript('og/tasks/main.js');

require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
<div>
    <div class="sub-tab">
        <span id='my-learning-tab'
              class="sub-tab-content sub-tab-span"><?php //echo $departName; ?>
            日志查看
        </span>

        <div  class="<?php echo ($userRole == '局长' || $departName == '效能办') ? 'inline' : 'hide'; ?>">

            &nbsp;| &nbsp;
             <span id='best-depart-tab' class="sub-tab-span">
             卫生最佳科室
            </span>
            &nbsp;| &nbsp;
            <span id='best-floor-tab' class="sub-tab-span">
             卫生最佳责任区
            </span>
            &nbsp;| &nbsp;
            <span id='chuqintongji-tab' class="sub-tab-span">
            考勤统计
            </span>
        </div>

        <span  class=" <?php
        // 日志还没创建的时候，任何人都可以创建
        if ($duty_been_created == 0) {
            echo 'hide';
        } else {
            // 已经创建的，如果是当前用户创建的才能填写。
            if ($is_on_duty) {
                echo '';
            } else {
                echo 'hide';
            }
        }
        ?>  " >
            <p  class="zhibanzhang-tip">
            您是今天的值班长，
            <?php
            if ($is_commit) {
                echo '日志已经填写完成';
            } else {
                echo '请及时完成值班长日志的填写';
            }
            ?>
                <span class="zhibanzhang-icon"></span></p>
        </span>
    </div>

    <div class="clearFloat"></div>

</div>


<div id="duty-view-tab-content">
    <div class="duty-search-wrap">
        <input class='duty-search-input gray' id='duty-search-input' onfocus="og.duty.onSearch()"
               onblur="og.duty.leaveSearch()"
               value="<?php echo $condition ? $condition : '输入日期(如2014-01-01)或值班人进行查询' ?>">
        <span onclick="og.duty.beginSearch()" class="searchIcon"></span>
    </div>


    <div style="top: 54px; position: absolute; margin-left: 14px; text-align: left; width: 157px;" class=" <?php
    // 日志还没创建的时候，任何人都可以创建
    if ($duty_been_created == 0) {
        echo '';
    } else {
        // 已经创建的，如果是当前用户创建的才能填写。
        if ($is_on_duty && !$is_commit) {
            echo '';
        } else {
            echo 'hide';
        }
    }
    ?>" onclick="og.duty.writeDuty( <?php echo $duty_been_created; ?><?php echo $duty_id ? ',' . $duty_id : ''; ?>)">


        <a>
            <?php
            // 日志还没创建的时候，任何人都可以创建
            if ($duty_been_created == 0) {
                echo '新建值班长日志';
            } else {
                echo '编辑值班长日志';
            }
            ?>
        </a><span class="write-icon  "></span>
    </div>

    <!-- 日志查看-->
    <div class="content-wraper">

        <div class="table-header">
            <table width='100%' class="og-table">
                <tr>
                    <td class="duty-d1">日期</td>
                    <td class="duty-d2">值班人</td>
                    <td class="duty-d3">创建时间</td>
                    <td class="duty-d4">最后修改时间</td>
                    <td class="duty-d6">状态</td>
                    <td class="duty-d5">操作</td>
                </tr>
            </table>
        </div>
        <?php
        $i = 0;
        foreach ($dutyInfo as $item) {
            ?>

            <div>
                <table width=100% class="og-table">
                    <?php
                    $i++;
                    if ($i % 2 == 0) {
                        echo '<tr>';
                    } else {
                        echo '<tr class="dashaltrow">';
                    }?>
                    <td class='duty-d1'>
                        <?php echo $item['cur_date'];
                        ?>
                    </td>


                    <td class='duty-d2'><?php echo $item['username']; ?></td>
                    <td class="duty-d3"><?php echo $item['create_time']; ?>
                    <td class='duty-d4'><?php echo $item['last_modify_time']; ?></td>
                    <td class='duty-d6'><?php echo  buildDutyStatus($item['isCommited']); ?></td>
                    <td class='duty-d5'><?php
                        if ($departName == '效能办') {
                            echo "<a onclick='og.duty.writeDuty(1," . $item['id'] . ")'>编辑</a>";
                        } else {
                            echo "<a onclick='og.duty.viewDuty(" . $item['id'] . ")'>查看</a>";
                        }
                        ?></td>
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
</div>

<!--最佳科室 -->
<div id="best-depart-tab-content" class="hide">
    <?php if ($userRole == '局长' || $departName == '效能办') { ?>
        <div class="table-header">
            <table width='100%' class="og-table">
                <tr>
                    <td class="duty-d1">科室</td>
                    <td class="duty-d2">统计次数</td>
                </tr>
            </table>
        </div>
        <?php
        foreach ($depart_tongji as $item) {
            if($item['depart_name'] == '局长室' || $item['depart_name'] == '效能办'){
                continue;
            }
            ?>

            <div>
                <table width=100% class="og-table">
                    <?php
                    $i++;
                    if ($i % 2 == 0) {
                        echo '<tr>';
                    } else {
                        echo '<tr class="dashaltrow">';
                    }?>
                    <td class='duty-d1'>
                        <?php echo $item['depart_name'];
                        ?>
                    </td>
                    <td class='duty-d2'><?php echo $item['count']; ?></td>
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
    <?php } ?>
</div>
<!--end最佳科室 -->
<!--最佳楼层 -->
<div id="best-floor-tab-content" class="hide">
    <?php if ($userRole == '局长' || $departName == '效能办') {?>
        <div class="table-header">
            <table width='100%' class="og-table">
                <tr>
                    <td class="duty-d1">科室</td>
                    <td class="duty-d2">统计次数</td>
                </tr>
            </table>
        </div>
        <?php
        foreach ($floor_tongji as $item) {
            if($item['depart_name'] == '局长室' || $item['depart_name'] == '效能办'){
                continue;
            }
            ?>

            <div>
                <table width=100% class="og-table">
                    <?php
                    $i++;
                    if ($i % 2 == 0) {
                        echo '<tr>';
                    } else {
                        echo '<tr class="dashaltrow">';
                    }?>
                    <td class='duty-d1'>
                        <?php echo $item['depart_name'];
                        ?>
                    </td>
                    <td class='duty-d2'><?php echo $item['count']; ?></td>
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
    <?php } ?>
</div>

<!--end最佳楼层 -->

<!--出勤统计 -->
<div id="chuqintongji-tab-content" class="hide">
    <?php if ($userRole == '局长' || $departName == '效能办') {?>
        <div class="table-header">
            <table width='100%' class="og-table">
                <tr>
                    <td class="duty-d1">姓名</td>
                    <td class="duty-d2">公差</td>
                    <td class="duty-d2">病假</td>
                    <td class="duty-d2">事假</td>
                    <td class="duty-d2">年休假</td>
                    <td class="duty-d2">其它</td>
                </tr>
            </table>
        </div>
        <?php
        foreach ($chuqin_tongji as $item) {
            if($item['depart_name'] == '局长室' || $item['depart_name'] == '效能办'){
                continue;
            }
            ?>

            <div>
                <table width=100% class="og-table">
                    <?php
                    $i++;
                    if ($i % 2 == 0) {
                        echo '<tr>';
                    } else {
                        echo '<tr class="dashaltrow">';
                    }?>
                    <td class='duty-d1'>
                        <?php echo $item['username'];
                        ?>
                    </td>
                    <td class='duty-d2'><?php echo $item['count1']; ?></td>
                    <td class='duty-d2'><?php echo $item['count2']; ?></td>
                    <td class='duty-d2'><?php echo $item['count3']; ?></td>
                    <td class='duty-d2'><?php echo $item['count5']; ?></td>
                    <td class='duty-d2'><?php echo $item['count4']; ?></td>
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
    <?php } ?>
</div>
<script>
    function showSubTab(ele) {
        $('.sub-tab span').removeClass('sub-tab-content');
        ele.addClass('sub-tab-content');
        $('#duty-view-tab-content').addClass('hide');
        $('#best-depart-tab-content').addClass('hide');
        $('#best-floor-tab-content').addClass('hide');
        $('#chuqintongji-tab-content').addClass('hide');
        var htmlStr = $.trim(ele.html());
        if (htmlStr == '卫生最佳科室') {
            $('#best-depart-tab-content').removeClass('hide');
        } else if (htmlStr == '卫生最佳责任区') {
            $('#best-floor-tab-content').removeClass('hide');
        } else if (htmlStr == '考勤统计') {
            $('#chuqintongji-tab-content').removeClass('hide');
        }
        else {
            $('#duty-view-tab-content').removeClass('hide');
        }
    }
    $('.sub-tab .sub-tab-span').click(function () {
        var ele = $(this);
        og.taskSubTab = ele.attr('id');
        showSubTab(ele);
    });
</script>
<?php
function buildDutyStatus ($isCommit){
    if ($isCommit){
        return '<span class="ico-task-light-green" title="已完成"></span>';
    }else {
        return '<span class="ico-task-light-gray" title="进行中"></span>';
    }
}
?>