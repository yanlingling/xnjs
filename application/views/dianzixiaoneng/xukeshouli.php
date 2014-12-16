
<div id="xukeTabContent" class="<?php echo $tab == 'xuke' ? '' : 'hide'; ?>">
    <div>
        <div  style="margin-top: 10px;margin-left: 10px"
             class=" <?php echo $isYaoxie2== true ? '' : 'hide' ?>">
            <span class="new-button"
                  onclick="og.dianzixiaoneng.add()">新建许可</span>
        </div>
    </div>
    <div>
        <div class="content-wraper">

                <table width='100%' class="og-table">
                    <thead class="table-header">
                        <td class="dianzi-d1">申请人</td>
                        <td class="dianzi-d2">申请事项</td>
                        <td class="dianzi-d3">申请时间</td>
                        <td class="dianzi-d5">任务内容</td>
                        <td class="dianzi-d4">到达时间</td>
                        <td class="dianzi-d5">完成时限</td>
                        <td class="dianzi-d6">办结时间</td>
                        <td class="dianzi-d7">结论</td>
                        <td class="dianzi-d8">效能状态</td>
                        <td class="dianzi-d9">操作</td>
                    </thead>
            <?php
            $i = 0;
            foreach ($xukeshouliList as $item) {
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
                        <td class='dianzi-d5'> <?php echo transDianziType($item['sub_process']); ?> </td>
                        <td class='dianzi-d4'> <?php echo transDate($item['create_time']); ?> </td>
                        <td class='dianzi-d5'> <?php echo transDate($item['dead_time']); ?> </td>
                        <td class='dianzi-d6'> <?php echo transDate($item['complete_time']); ?> </td>
                        <td class='dianzi-d7'> <?php echo transDianziResult($item['result'],$item['sub_process']); ?> </td>
                        <td class='dianzi-d8'> <?php echo getLightStatus($item['light_status']); ?> </td>
                    <td class='dianzi-d9'> <?php echo getDianziXukeOpt($item['id'],$item['task_id'],$item['sub_process'],$item['apply_type'],$item['dead_time']); ?> </td>
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
