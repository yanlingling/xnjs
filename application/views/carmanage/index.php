<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/car/car.js');
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
<script>
    function showSubTab(ele) {
        $('.sub-tab span').removeClass('sub-tab-content');
        ele.addClass('sub-tab-content');
        $('#my-apply-content').addClass('hide');
        $('#to-handle-content').addClass('hide');
        $('#all-car-content').addClass('hide');
        $('#car-data-content').addClass('hide');
        var htmlStr = $.trim(ele.html());
        if (htmlStr == '我的申请') {
            $('#my-apply-content').removeClass('hide');
        } else if (htmlStr == '待处理申请') {
            $('#to-handle-content').removeClass('hide');
        } else if (htmlStr == '所有申请') {
            $('#all-car-content').removeClass('hide');
        }else if (htmlStr == '用车统计') {
            $('#car-data-content').removeClass('hide');
        }
    }
    $('.sub-tab span').click(function () {
        var ele = $(this);
        og.carSubTab = ele.attr('id');
        showSubTab(ele);
    });

    if (typeof og.carSubTab != 'undefined') {
        showSubTab($('#' + og.carSubTab));
    }
</script>
<div>
    <div>
        <div class="sub-tab">
        <span id='my-apply-tab'
              class="sub-tab-content">
            我的申请
        </span>

            <div
                class="<?php echo ($canManageCar) ? 'inline' : 'hide'; ?>">

             <span id='to-handle-tab'>
             待处理申请
            </span>
            </div>
            <div  class="<?php echo $canManageCar || $userRole=='局长'? 'inline' : 'hide'; ?>">

             <span id='all-apply-tab'>
             所有申请
            </span>

             <span id='car-data-tab'>
             用车统计
            </span>

            </div>
        </div>

        <div class="clearFloat"></div>
    </div>
    <div class="new-button" style="margin-left: 10px" onclick="og.car.create()">创建用车申请</div>

    <!---我的申请-->
    <div id="my-apply-content">
        <div class="content-wraper">

            <div class="table-header">
                <table width='100%' class="og-table">
                    <tr>
                        <td class="car-d1">用车人员</td>
                        <td class="car-d4">创建时间</td>
                        <td class="car-d1">科室</td>
                        <td class="car-d8">地点</td>
                        <td class="car-d9">目的地</td>
                        <td class="car-d2">开始时间</td>
                        <td class="car-d3">结束时间</td>
                        <td class="car-d5">车辆</td>
                        <td class="car-d6">状态</td>
                        <td class="car-d7">操作</td>
                    </tr>
                </table>
            </div>
            <?php
            $i = 0;
            $place = array(
                '1' => '县内',
                '2' => '市内',
                '3' => '市外'
            );

            foreach ($carInfo as $item) {
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
                        <td class='car-d1'>
                            <?php  echo $item['car_users'];
                            ?>
                        </td>
                        <td class='car-d4'><?php echo $item['create_time']; ?></td>
                        <td class='car-d1'> <?php  echo $item['depart_name']; ?> </td>
                        <td class='car-d8'><?php echo $place[$item['place']]; ?></td>
                        <td class="car-d9"><?php echo $item['place_detail']; ?>
                        <td class='car-d2'><?php echo $item['begin_time']; ?></td>
                        <td class="car-d3"><?php echo $item['end_time']; ?>
                        <td class='car-d5'>
                            <div style="position: relative">
                                <span class="car-number"><?php echo $item['car_number'] == '无'? '-': $item['car_number']; ?></span>
                                <div class="car-detail hide">
                                    <?php echo $item['name']; ?></br>
                                    司机：<?php echo $item['driver_name']; ?></br>
                                    电话：<?php echo $item['driver_phone']; ?></br>
                                    车牌：<?php echo $item['car_number']; ?></br>
                                </div>
                            </div>
                        </td>
                        <td class='car-d6'><?php echo getCarStatus($item['status'],$item['car_returned']); ?></td>
                        <td class='car-d7'><?php  echo getCarOpt($item['status'],$item['id'],$item['car_returned']); ?></td>
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
    <!---end我的申请-->
    <!---待处理的申请-->
    <div id="to-handle-content" class="hide">
        <div class="content-wraper">

            <div class="table-header">
                <table width='100%' class="og-table">
                    <tr>
                        <td class="car-d1">用车人员</td>
                        <td class="car-d4">创建时间</td>
                        <td class="car-d1">科室</td>
                        <td class="car-d8">地点</td>
                        <td class="car-d9">目的地</td>
                        <td class="car-d2">开始时间</td>
                        <td class="car-d3">结束时间</td>
                        <td class="car-d5">车辆</td>
                        <td class="car-d6">状态</td>
                        <td class="car-d7">操作</td>
                    </tr>
                </table>
            </div>
            <?php
            $i = 0;
            $place = array(
                '1' => '县内',
                '2' => '市内',
                '3' => '市外'
            );
            foreach ($toHandleApply as $item) {
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
                        <td class='car-d1'>
                            <?php  echo $item['car_users'];
                            ?>
                        </td>
                        <td class='car-d4'><?php echo $item['create_time']; ?></td>
                        <td class='car-d1'> <?php  echo $item['depart_name']; ?> </td>
                        <td class='car-d8'><?php echo $place[$item['place']]; ?></td>
                        <td class="car-d9"><?php echo $item['place_detail']; ?>
                        <td class='car-d2'><?php echo $item['begin_time']; ?></td>
                        <td class="car-d3"><?php echo $item['end_time']; ?>
                        <td class='car-d5'><?php echo '-'; ?></td>
                        <td class='car-d6'><?php echo getCarStatus($item['status'],$item['car_returned']); ?></td>
                        <td class='car-d7'><?php  echo getHandleOpt($item['status'],$item['id']); ?></td>
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
    <!---end待处理的申请-->

    <!--begin所有申请-->
    <div id="all-car-content" class="hide">
        <div class="content-wraper">

            <div class="table-header">
                <table width='100%' class="og-table">
                    <tr>
                        <td class="car-d1">用车人员</td>
                        <td class="car-d4">创建时间</td>
                        <td class="car-d1">科室</td>
                        <td class="car-d8">地点</td>
                        <td class="car-d9">目的地</td>
                        <td class="car-d2">开始时间</td>
                        <td class="car-d3">结束时间</td>
                        <td class="car-d5">车辆</td>
                        <td class="car-d6">状态</td>
                        <td class="car-d7">操作</td>
                    </tr>
                </table>
            </div>
            <?php
            $i = 0;
            $reason = array(
                '1' => '公差',
                '2' => '病假',
                '3' => '事假',
                '5' => '年休假',
                '4' => '其它'
            );
            foreach ($allCarApply as $item) {
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
                        <td class='car-d1'>
                            <?php  echo $item['car_users'];
                            ?>
                        </td>
                        <td class='car-d4'><?php echo $item['create_time']; ?></td>
                        <td class='car-d1'>
                            <?php  echo $item['depart_name'];
                            ?>
                        </td>
                        <td class='car-d8'><?php echo $place[$item['place']]; ?></td>
                        <td class="car-d9"><?php echo $item['place_detail']; ?>
                        <td class='car-d2'><?php echo $item['begin_time']; ?></td>
                        <td class="car-d3"><?php echo $item['end_time']; ?>
                        <td class='car-d5'>
                            <div style="position: relative">
                                <span class="car-number"><?php echo $item['car_number'] == '无'? '-': $item['car_number']; ?></span>
                                <div class="car-detail hide">
                                    <?php echo $item['name']; ?></br>
                                    司机：<?php echo $item['driver_name']; ?></br>
                                    电话：<?php echo $item['driver_phone']; ?></br>
                                    车牌：<?php echo $item['car_number']; ?></br>
                                </div>
                            </div>
                        </td>
                        <td class='car-d6'><?php echo getCarStatus($item['status'],$item['car_returned']); ?></td>
                        <td class='car-d7'><?php  echo getAllHandleOpt($item['status'],$item['id']); ?></td>
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
    <!--end所有申请-->

    <!--begin用车统计-->
    <div id="car-data-content" class="hide">
        <div class="content-wraper">

            <div class="table-header">
                <table width='100%' class="og-table">
                    <tr>
                        <td class="car-d1">科室</td>
                        <td class="car-d4">总计次数</td>
                        <td class="car-d1">县内</td>
                        <td class="car-d8">市内</td>
                        <td class="car-d9">市外</td>
                    </tr>
                </table>
            </div>
            <?php
            $i = 0;
            $place= array(
                '1' => '县内',
                '2' => '市内',
                '3' => '市外'
            );
            foreach ($carData as $item) {
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
                        <td class='car-d1'>
                            <?php  echo $item['depart_name'];
                            ?>
                        </td>
                        <td class='car-d4'><?php echo $item['count']; ?></td>
                        <td class='car-d1'>
                            <?php  echo $item['1'];
                            ?>
                        </td>
                        <td class='car-d8'><?php echo $item['2']; ?></td>
                        <td class="car-d9"><?php echo $item['3']; ?> </td>
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
    <!--end所有申请-->
</div>


<?php
function getCarStatus($status,$carReturned)
{
    switch ($status) {
        case 0:
            return '待审批';
            break;
        case 1:
            if ($carReturned){
                return '已归车';
            } else {
                return '审批通过';
            }
            break;
        case 2:
            return '审批不通过';
            break;
        case 3:
            return '无可用车辆';
            break;
        case 4:
            return '已撤回';
            break;
    }
}

function getCarOpt($status,$id,$carReturned)
{
    // 只有待审批的才可以编辑 & 撤回
    if ($status == 0) {
        return "<a onclick='og.car.edit(" . $id . ")'>编辑</a>&nbsp;&nbsp;<a    onclick='og.car.undo(" . $id . ")'>撤回</a>";
    } else if($status == 1 && $carReturned !=1) {
        return "<a onclick='og.car.view(" . $id . ")'>查看</a> &nbsp;&nbsp;<a onclick='og.car.returnCar(" . $id . ")'>归车</a>";
    }
    else {
        return "<a onclick='og.car.view(" . $id . ")'>查看</a>";
    }
}

function getAllHandleOpt($status,$id){
    return "<a onclick='og.car.view(" . $id . ")'>查看</a>";
}
function getHandleOpt($status,$id)
{
    return "<a onclick='og.car.view(" . $id . ")'>查看</a>&nbsp;&nbsp;"
    ."<a onclick='og.car.handleApply($id,$status)'>处理</a>";
}

?>
<script>
    $('.car-number').mouseover(function () {
         if ($(this).html() == '-'){
             return;
         }
         $(this).siblings().removeClass('hide');
    });
    $('.car-number').mouseout(function () {
        $(this).siblings().addClass('hide');
    });
</script>
