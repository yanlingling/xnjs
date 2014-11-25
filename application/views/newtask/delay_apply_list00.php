<?php
require_javascript("og/CSVCombo.js");
require_javascript("og/DateField.js");
require_javascript('og/tasks/main.js');
require_javascript('og/tasks/addTask.js');
require_javascript('og/tasks/drawing.js');
require_javascript('og/tasks/TasksTopToolbar.js');
require_javascript('og/tasks/TasksBottomToolbar.js');
require_javascript('og/tasks/print.js');
require_javascript('og/tasks/delayApply.js');
require_javascript('og/dialog.js');
require_javascript('og/jquery.min.js');
?>

<div id="tasksPanel" class="ogContentPanel"
     style="background-color:white;background-color:#F0F0F0;height:100%;width:100%;">
    <div id="tasksPanelTopToolbar" class="x-panel-tbar"
         style="width:100%;height:30px;display:block;background-color:#F0F0F0;"></div>
    <div id="tasksPanelBottomToolbar" class="x-panel-tbar"
         style="width:100%;height:30px;display:block;background-color:#F0F0F0;border-bottom:1px solid #CCC;display:none;"></div>
    <div>
        <div><span>***科室岗位职责</span>|<span onclick="ogTasks.showApplyPanel()">延期申请</span></div>
        <div id="tasksPanelContent"
             style="background-color:white;padding:7px;padding-top:0px;overflow-y:scroll;position:relative;">
            <table width='100%'>
                <tr>
                    <td class="d1">岗位职责</td>
                    <td class="d2">申请延期天数</td>
                    <td class="d3">创建时间</td>
                    <td class="d8">创建人</td>
                    <td class="d4">批准天数</td>
                    <td class="d5">状态</td>
                    <td class="d6">审批时间</td>
                    <td class="d7">操作</td>
                </tr>
            </table>
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
            <td class='d1'><?php echo $item['title'] ?></td>
            <td class='d2'><?php echo $item['hope_day'] ?></td>
            <td style='display:none'><?php echo $item['reason'] ?></td>
            <td class='d3'><?php echo $item['create_time'] ?></td>
            <td class='d8'><?php echo $item['username'] ?></td>
            <td class='d4'><?php echo $item['agree_day'] ?></td>
            <td class='d5'><?php echo get_apply_status($item['status']) ?></td>
            <td class='d6'><?php echo gethandletime($item['handle_time']) ?></td>
            <td class='d7'><?php echo getoptcontent($item['status'], $item['id']) ?></td>
            </tr></table></div>
        <div class="ogAppendBox hide" id="apply-detail<?php echo $item['id'] ?>">
            <table width="500px">
                <tr>
                    <td>岗位职责：</td>
                    <td><?php echo $item['title'] ?></td>
                </tr>
                <tr>
                    <td>延期原因：</td>
                    <td><textarea id='apply-reason-<?php echo $item['id'] ?>'><?php echo $item['reason'] ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">申请延期：<input id='apply-day-<?php echo $item['id'] ?>'
                                                value="<?php echo $item['hope_day'] ?>"/>天
                    </td>
                </tr>
                <tr class="<?php echo $item['status'] == 0 ? '' : 'hide'; ?>">
                    <td><input onclick="ogTasks.saveDelayApplyDetail(<?php echo $item['id'] ?>)" type="button"
                               value="保存"/></td>
                    <td><input onclick="ogTasks.viewDelayApplyDetail(<?php echo $item['id'] ?>)" type="button"
                               value="关闭"/></td>
                </tr>
            </table>
        </div>
        <?php
        }
        ?>
        <tr></tr>
        </table>
    </div>
</div>
</div>

<?php
function gethandletime($time)
{
    if ($time == '0000-00-00 00:00:00') {
        return '-';
    } else {
        return $time;
    }
}

function get_apply_status($status)
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

function getoptcontent($status, $id)
{
    $userRole = logged_user()->getUserRole();
    $str = '<a onclick="ogTasks.viewDelayApplyDetail(' . $id . ')">查看</a>';
    //$str = '<a onclick="og.dialog.draw()">查看</a>';
    // 只有局长有处理延期申请的权限
    if ($userRole == '局长') {
        if ($status == 0) {
            $str .= '&nbsp;&nbsp;<a>处理申请</a>';
        }
        return $str;
    } else {
        // 还未处理的请求可以撤回
        if ($status == 0) {
            $str .= '&nbsp;&nbsp;<a onclick="ogTasks.cancelDelayApply(' . $id . ')">撤回</a>';
        }
        return $str;
    }
}

?>
<script type="text/javascript">
</script>