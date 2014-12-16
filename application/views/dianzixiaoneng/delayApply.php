
<div id="delayTabContent" class="<?php echo $tab == 'delay' ? '' : 'hide'; ?>">
        <div class="table-header">
            <table width='100%'>
                <tr>
                    <td class="yanqid1">岗位职责</td>
                    <td class="yanqid2">申请延期天数</td>
                    <td class="yanqid3">申请创建时间</td>
                    <td class="yanqid8">创建人</td>
                    <td class="yanqid4">批准天数</td>
                    <td class="yanqid5">状态</td>
                    <td class="yanqid6">审批时间</td>
                    <td class="yanqid7">操作</td>
                </tr>
            </table>
        </div>
        <?php
        $i = 0;
        foreach ($apply_list as $item) {
        echo '<div id="delay-apply-' . $item['id'] . '"><table width=100%>';
        $i++;
        if ($i % 2 == 0) {
            echo '<tr>';
        } else {
            echo '<tr class="dashaltrow">';
        }?>
        <td class='yanqid1'
            title="<?php echo $item['title'] ?>"><?php echo mb_substr($item['title'], 0, 12, "UTF-8"); ?></td>
        <td class='yanqid2'><?php echo $item['hope_day'] ?></td>
        <td style='display:none'><?php echo $item['reason'] ?></td>
        <td class='yanqid3'><?php echo $item['create_time'] ?></td>
        <td class='yanqid8'><?php echo $item['username'] ?></td>
        <td class='yanqid4'><?php echo $item['agree_day'] ?></td>
        <td class='yanqid5'><?php echo get_apply_status_dzxn($item['status']) ?></td>
        <td class='yanqid6'><?php echo gethandletime_dzxn($item['handle_time']) ?></td>
        <td class='yanqid7'><?php echo getoptcontent_dzxn($item['status'], $item['id'], $item['task_id'], $isSelf) ?></td>
        </tr></table></div>
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

function getoptcontent_dzxn($status, $id, $task_id, $isSelf)
{
    $userRole = logged_user()->getUserRole();
    $str = '';
//$str = '<a onclick="og.dialog.draw()">查看</a>';
// 只有局长有处理延期申请的权限
    if ($userRole == '局长') {
        if ($status == 0) {
            $str .= '&nbsp;&nbsp;<a onclick="ogTasks.viewDelayApplyDetail(' . $id . ',' . $task_id . ')">处理申请</a>';
        }
        return $str;
    } else {
        $str = '<a onclick="ogTasks.viewDelayApplyDetail(' . $id . ',' . $status . ')">查看</a>';
// 还未处理的请求可以撤回
        if ($status == 0 && $isSelf == 'self') {
            $str .= '&nbsp;&nbsp;<a onclick="ogTasks.cancelDelayApply(' . $id . ')">撤回</a>';
        }
        return $str;
    }
}

?>
<!--  我的申请面板end -->