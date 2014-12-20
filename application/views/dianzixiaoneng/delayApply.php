
<div id="delayTabContent" class="<?php echo $tab == 'delay' ? '' : 'hide'; ?>">
    <div>
        <div class="content-wraper">

            <table width='100%' class="og-table">
                <thead class="table-header">
                <td class="dianzi-d1">任务内容</td>
                <td class="dianzi-d2">申请延期天数</td>
                <td class="dianzi-d3">申请创建时间</td>
                <td class="dianzi-d5">创建人</td>
                <td class="dianzi-d4">批准天数</td>
                <td class="dianzi-d5">状态</td>
                <td class="dianzi-d6">审批时间</td>
                <td class="dianzi-d7">操作</td>
                </thead>
                <?php
                $i = 0;
                foreach ($apply_list as $item) {
                    ?>

                    <?php
                    $i++;
                    if ($i % 2 == 0) {
                        echo '<tr>';
                    } else {
                        echo '<tr class="dashaltrow">';
                    }?>
                    <td class='dianzi-d1'><?php echo transDianziType($item['sub_process']); ?></td>
                    <td class='dianzi-d2'> <?php echo $item['hope_day']; ?> </td>
                    <td class='dianzi-d3'> <?php echo $item['create_time']; ?> </td>
                    <td class='dianzi-d5'> <?php echo $item['username']; ?> </td>
                    <td class='dianzi-d4'> <?php echo $item['agree_day']; ?> </td>
                    <td class='dianzi-d5'> <?php echo get_apply_status_dzxn($item['status']); ?> </td>
                    <td class='dianzi-d6'> <?php echo gethandletime_dzxn($item['handle_time']); ?> </td>
                    <td class='dianzi-d7'> <?php echo getoptcontent_dzxn($item['status'], $item['id'], $item['task_id'], $isSelf, $item['reason'], $item['hope_day']); ?> </td>

                    </tr>
                <?php
                }
                ?>
            </table>
            <?php
            if ($i == 0) {
                ?>
                <div class="no-data">当前暂无相关数据</div>
            <?php
            }
            ?>
        </div>
    </div>
</div>
<?php
function gethandletime_dzxn($time)
{
    if ($time == '0000-00-00 00:00:00') {
        return '-';
    } else {
        return $time;
    }
}

function get_apply_status_dzxn($status)
{
    switch ($status) {
        case 0:
            return '待审批';
            break;
        case 1:
            return '审批通过';
            break;
        case 2:
            return '审批不通过';
            break;
        case 3:
            return '已撤回';
            break;
    }
}

function getoptcontent_dzxn($status, $id, $task_id, $isSelf,$detail,$hopeDay)
{
    $userRole = logged_user()->getUserRole();
    $str = '';
//$str = '<a onclick="og.dialog.draw()">查看</a>';
// 只有局长有处理延期申请的权限
    if ($userRole == '局长') {
        if ($status == 0) {
            $str .= '&nbsp;&nbsp;<a onclick="og.dianzixiaoneng.handleDelayApply(' . $id . ',' . $task_id . ',' . $detail. ',' . $hopeDay. ')">处理申请</a>';
        }
        return $str;
    } else {
        $str = '<a onclick="og.dianzixiaoneng.viewDelayApplyDetail(' . $id . ',' . $status . ',' . $detail. ',' . $hopeDay. ')">查看</a>';
// 还未处理的请求可以撤回
        if ($status == 0 && $isSelf == 'self') {
            $str .= '&nbsp;&nbsp;<a onclick="og.dianzixiaoneng.cancelDelayApply(' . $id . ')">撤回</a>';
        }
        return $str;
    }
}

?>
<!--  我的申请面板end -->