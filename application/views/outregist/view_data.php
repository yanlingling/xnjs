<?php

require_javascript('og/tasks/new/addTask.js');

require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
<div id="chuqintongji-tab-content">
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
        $i = 0;
        foreach ($userData as $item) {
             if($i==0){
                 $i++;
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
                        <?php echo $item['name'];
                        ?>
                    </td>
                    <td class='duty-d2'><?php echo isset($item['1'])?$item['1']:0; ?></td>
                    <td class='duty-d2'><?php echo isset($item['2'])?$item['2']:0;; ?></td>
                    <td class='duty-d2'><?php echo isset($item['3'])?$item['3']:0; ?></td>
                    <td class='duty-d2'><?php echo isset($item['5'])?$item['5']:0; ?></td>
                    <td class='duty-d2'><?php echo isset($item['4'])?$item['4']:0; ?></td>
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