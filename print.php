<html>
<head>
    <title>打印-公差</title>
</head>
<body>
<style>
    tr {
        height: 30px;
    }
    td {
        padding: 10px;
    }
</style>
<div style="width: 900px;
text-align: center;
margin: 10px auto;">
<h1>
    赣榆县食品药品监督管理局公务出差审批单
</h1>
<p style="text-align: right">
    <?php
    $createTime = urldecode($_GET['create']);
    $begin =  urldecode($_GET['begin']);
    $end =  urldecode($_GET['end']);
    ?>
申请日期：<?php echo date('Y',strtotime($createTime));?> 年
    <?php echo date('m',strtotime($createTime));?> 月
    <?php echo date('d',strtotime($createTime));?> 日
</p>

<table width="100%"  border="1" style="border: 1px solid gray;border-collapse: collapse;
border-spacing: 0;">
    <tr>
        <td>出差人员</td>
        <td colspan="5"><?php echo urldecode($_GET['username'])?></td>
    </tr>
    <tr>
        <td>出差时间</td>
        <td colspan="5"><?php echo date('Y',strtotime($begin));?> 年
            <?php echo date('m',strtotime($begin));?> 月
            <?php echo date('d',strtotime($begin));?> 日 至 <?php echo date('Y',strtotime($end));?> 年
            <?php echo date('m',strtotime($end));?> 月
            <?php echo date('d',strtotime($end));?> 日
        </td>
    </tr>

    <tr>
        <td>出差事由</td>
        <td colspan="5"><?php echo urldecode($_GET['detail'])?></td>
    </tr>
    <tr>
        <td>出差地点</td>
        <td colspan="2"></td>
        <td>是否在外用餐</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td rowspan="2">交通工具</td>
        <td>单位派车</td>
        <td>飞机</td>
        <td>火车</td>
        <td>客车</td>
        <td>轮船</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td>办公室审核</td>
        <td colspan="2"></td>
        <td>科室负责人审核</td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td>分管领导审批</td>
        <td colspan="2"></td>
        <td>主要领导审批</td>
        <td colspan="2"></td>
    </tr>
</table>
</div>
</body>
</html>