<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/learning/learning.js');
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
<div class="content-wraper">

    <div  class="table-header">
        <table width='100%' class="og-table">
            <tr>
                <td width="30%">姓名</td>
                <td width="30%">状态</td>
                <td width="30%">廉能得分</td>
            </tr>
        </table>
    </div>
    <?php
    $i =0;
    foreach ($personInfo as $item) {
    echo '<div  id="group-learning-list-' . $item['id'] . '"><table width=100% style="text-align:center">';
    $i++;
    if ($i % 2 == 0) {
        echo '<tr>';
    } else {
        echo '<tr class="dashaltrow">';
    }?>
    <td  width="30%">
        <a onclick="og.risk.goToPersonLearning(<?php echo $item['id'] ?>)">
            <?php echo $item['name'] ?>
        </a>
    </td>
    <td  width="30%"><?php echo getLightStatus($item['status'])?></td>
    <td  width="30%"><?php echo $item['score'] ?></td>

    </tr></table></div>
<?php
}
if($i==0){
?>
    <div class="no-data">当前暂无相关数据</div>
<?php
}
?>
</div>
