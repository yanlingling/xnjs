
<div id="allTabContent" class="<?php echo $tab == 'all' ? '' : 'hide'; ?>">
    <div>
        <div class="content-wraper">

                <table width='100%' class="og-table">
                    <thead class="table-header">
                        <td class="dianzi-d1">申请人</td>
                        <td class="dianzi-d2">申请事项</td>
                        <td class="dianzi-d3">申请时间</td>
                        <td class="dianzi-d4">状态</td>
                        <td class="dianzi-d9">操作</td>
                    </thead>
            <?php
            $i = 0;
            foreach ($allxukeList as $item) {
                ?>

                        <?php
                        $i++;
                        if ($i % 2 == 0) {
                            echo '<tr>';
                        } else {
                            echo '<tr class="dashaltrow">';
                        }?>
                        <td class='dianzi-d1'>
                            <?php  echo $item['apply_name'];
                            ?>
                        </td>
                        <td class='dianzi-d2'><?php echo getApplyType($item['apply_type']); ?></td>
                        <td class='dianzi-d3'> <?php echo transDate($item['apply_time']); ?> </td>
                        <td class='dianzi-d4'> <?php echo transDianziProcess($item['process']); ?> </td>
                    <td class='dianzi-d9'> <?php echo getDianziAllxukeOpt($item['id'],$item['task_id'],$item['sub_process']); ?> </td>
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
function getDianziAllxukeOpt($apply_id,$task_id,$sub_process) {
    $str = "<a onclick='og.dianzixiaoneng.view($apply_id)'>查看</a>";
    $str .= "&nbsp;&nbsp;<a onclick='og.dianzixiaoneng.del($apply_id)'>删除</a>";
    return $str;
}
?>
