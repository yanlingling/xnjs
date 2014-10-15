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
                <td width="30%">科室</td>
                <td width="30%">状态</td>
                <td width="30%">平均得分</td>
            </tr>
        </table>
    </div>
    <?php
    $i = 0;
    $hasApplyDepart = array();
    foreach ($group_risk_list as $item) {
    echo '<div  id="group-learning-list-' . $item['depart_id'] . '"><table width=100% style="text-align:center">';
    $i++;
    if ($i % 2 == 0) {
        echo '<tr>';
    } else {
        echo '<tr class="dashaltrow">';
    }?>
    <td  width="30%">
        <a onclick="og.risk.goToDepartLearning(<?php echo $item['depart_id'] ?>)">
            <?php echo $item['depart_name'] ?>
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
