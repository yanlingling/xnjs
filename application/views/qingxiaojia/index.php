<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/holiday/holiday.js');
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>

<div>
    <div>
        <div class="sub-tab">
        <span id='my-apply-tab'
              class="<?php echo $tab == 'my' ? 'sub-tab-content' : ''; ?>"><?php //echo $departName; ?>
            我的申请
        </span>

            <div
                class="<?php echo ($userRole == '局长' || $userRole == '副局长' || $userRole == '科长') ? 'inline' : 'hide'; ?>">

             <span class="<?php echo $tab == 'handle' ? 'sub-tab-content' : ''; ?>" id='to-handle-tab'>
             待处理申请
            </span>
            </div>
            <div  class="<?php echo $canViewAllHolidayApply ? 'inline' : 'hide'; ?>">

             <span  class="<?php echo $tab == 'all' ? 'sub-tab-content' : ''; ?>" id='all-holiday-tab'>
             所有申请
            </span>

            </div>
            <div  class="<?php echo $canViewAllHolidayApply ? 'inline' : 'hide'; ?>">

             <span class="<?php echo $tab == 'data' ? 'sub-tab-content' : ''; ?>" id='tongji-holiday-tab'>
                 数据统计
            </span>

            </div>
        </div>

        <div class="clearFloat"></div>
    </div>
    <div class="new-button" style="margin-left: 10px" onclick="og.holiday.create()">创建申请</div>
    <!---我的申请-->
    <div id="my-apply-content" class="<?php echo $tab == 'my' ? '' : 'hide'; ?>">
        <div class="content-wraper">

            <div class="table-header">
                <table width='100%' class="og-table">
                    <tr>
                        <td class="hld-d1">请假事由</td>
                        <td class="hld-d2">申请休假时间</td>
                        <td class="hld-d3">批准休假时间</td>
                        <td class="hld-d4">创建时间</td>
                        <td class="hld-d6">状态</td>
                        <td class="hld-d7">操作</td>
                    </tr>
                </table>
            </div>
            <?php
            $i = 0;
            $reason = array(
                '1' => '公差',
                '2' => '病假',
                '3' => '事假',
                '5'=> '年休假',
                '4' => '其它'
            );
            foreach ($holidayInfo as $item) {
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
                        <td class='hld-d1'>
                            <?php echo $reason[$item['reason']];
							//.print_r($item);
                            ?>
                        </td>
                        <td class='hld-d2' style="font-size:13px;"><?php echo $item['apply_begin_date']; ?>到<?php echo $item['apply_end_date']; ?></td>
                        <td class="hld-d3" style="font-size:13px;"><?php if($item['begin_date'] == '0000-00-00' || $item['end_date'] == '0000-00-00'){echo "-";}else{echo $item['begin_date']; ?>到<?php echo $item['end_date'];} ?></td>
                        <td class='hld-d4'><?php echo $item['create_time']; ?></td>
                        <td class='hld-d6'><?php echo getHolidayStatus($item['apply_status']); ?></td>
                        <td class='hld-d7'><?php echo getHolidayOpt($item['apply_status'], $item['isHandled'], $item['id'], $item['end_date']); ?></td>
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
    <div id="to-handle-content"  class="<?php echo $tab == 'handle' ? '' : 'hide'; ?>">
        <div class="content-wraper">

            <div class="table-header">
                <table width='100%' class="og-table">
                    <tr>

                        <td class="hld-d6">申请人</td>
                        <td class="hld-d1">请假事由</td>
                        <td class="hld-d2">申请休假时间</td>
                        <td class="hld-d3">批准休假时间</td>
                        <td class="hld-d4">创建时间</td>
                        <td class="hld-d6">状态</td>
                        <td class="hld-d7">操作</td>
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
                        <td class='hld-d6'><?php echo $item['username']; ?></td>
                        <td class='hld-d1'>
                            <?php echo $reason[$item['reason']];
                            ?>
                        </td>
                        <td class='hld-d2' style="font-size:13px;"><?php echo $item['apply_begin_date']; ?>到<?php echo $item['apply_end_date']; ?></td>
                        <td class="hld-d3" style="font-size:13px;"><?php if($item['begin_date'] == '0000-00-00' || $item['end_date'] == '0000-00-00'){echo "-";}else{echo $item['begin_date']; ?>到<?php echo $item['end_date'];} ?></td>
                        <td class='hld-d4'><?php echo $item['create_time']; ?></td>
                        <td class='hld-d6'><?php echo getHolidayStatus($item['apply_status']); ?></td>
                        <td class='hld-d7'>
						<a onclick='og.holiday.handle(<?php echo $item['id']; ?>)'>处理</a>
						<?php /* echo getHandleOpt($item['apply_status'], $item['isHandled'], $item['id'],$item['begin_date'],$item['end_date'],logged_user()->getId(),"20140803","20140808"); */ ?>
						</td>
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
    <div id="all-holiday-content"  class="<?php echo $tab == 'all' ? '' : 'hide'; ?>">
        <div class="content-wraper">

            <div class="table-header">
                <table width='100%' class="og-table">
                    <tr>
						<!--class="hld-d6" -->
                        <td style="width:100px;">申请人</td>
						<!--class="hld-d1" -->
                        <td style="width:100px;">请假事由</td>
						<!--class="hld-d2" -->
                        <td style="width:190px;">申请休假时间</td>
						<!--class="hld-d3" -->
                        <td style="width:190px;">批准休假时间</td>
						<!--class="hld-d4" -->
                        <td style="width:220px;">创建时间</td>
						
						<!--class="hld-d5" -->
                        <td style="width:220px;">申请状态</td>
						<!--class="hld-d7" -->
                        <td>操作</td>
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
            foreach ($allHolidayApply as $item) {
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
						<!--class='hld-d6'-->
                        <td  style="width:100px;"><?php echo $item['username']; ?></td>
						<!--class='hld-d1'-->
                        <td style="width:100px;">
                            <?php echo $reason[$item['reason']];
                            ?>
                        </td>
						<!--class='hld-d2'-->
                        <td style="font-size:13px;width:190px;"><?php echo $item['apply_begin_date']; ?>到<?php echo $item['apply_end_date']; ?></td>
                        <!--class='hld-d3'-->
						<td style="font-size:13px;width:190px;"><?php if($item['begin_date'] == '0000-00-00' || $item['end_date'] == '0000-00-00'){echo "-";}else{echo $item['begin_date']; ?>到<?php echo $item['end_date'];} ?></td>
						<!--class='hld-d4'-->
						<td style="width:220px;"><?php echo $item['create_time']; ?></td>
						
                        <!--class='hld-d5'-->
						<td style="width:220px;"><?php echo getHolidayStatus($item['apply_status']); ?></td>
                        <!--class='hld-d7'-->
						<td>
                            <a onclick='og.holiday.view(<?php echo $item['id']; ?>)'>查看</a>&nbsp;
                            <?php
                            if ($item['reason'] == 1){
                            ?>
                            <a href="print.php?username=<?php echo urlencode($item['username']); ?>
                            &begin=<?php echo urlencode($item['begin_date']); ?>
                            &end=<?php echo urlencode($item['end_date']); ?>
                            &create=<?php echo urlencode($item['create_time']); ?>
                            &detail=<?php echo urlencode($item['detail']); ?>" target="_blank">打印</a>&nbsp;
                            <?php
                            }
                            ?>
                        </td>
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
    <!--begin数据统计-->
    <div id="tongji-holiday-content"  class="<?php echo $tab == 'data' ? '' : 'hide'; ?>">
        <div class="content-wraper">

            <div class="table-header">
                <table width='100%' class="og-table">
                    <tr>

                        <td class="hld-d6" >申请人</td>
                        <td class="hld-d1">公差</td>
                        <td class="hld-d2">病假</td>
                        <td class="hld-d3">事假</td>
                        <td class="hld-d4">年休假</td>
                        <td class="hld-d5">其它</td>
                    </tr>
                </table>
            </div>
            <?php
            $i = 0;
            foreach ($tongjiData as $item) {
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
                        <td class='hld-d6'><?php echo $item['username']; ?></td>
                        <td class='hld-d1'>
                            <?php echo isset($item[1])?$item[1]:'-';
                            ?>
                        </td>
                        <td class='hld-d2'><?php echo isset($item[2])?$item[2]:'-'?></td>
                        <td class="hld-d3"><?php echo isset($item[3])?$item[3]:'-'; ?>
                        <td class='hld-d4'><?php echo isset($item[5])?$item[5]:'-'; ?></td>
                        <td class='hld-d5'><?php echo isset($item[4])?$item[4]:'-';?></td>
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
    <!--end数据统计-->
</div>
<?php
function getHolidayStatus($status)
{
    switch ($status) {
        case 0:
            return '已撤回';
            break;
        case 1:
            return '审批通过';
            break;
        case 2:
            return '等待科长审批';
            break;
        case 3:
            return '等待分管领导审批';
            break;
        case 5:
            return '科长审批未通过';
            break;
        case 6:
            return '分管领导审批未通过';
            break;
        case 7:
            return '局长审批未通过';
            break;
        case 4:
            return '等待局长审批';
            break;
		case 8:
            return '等待办公室审核';
		case 9:
            return '办公室审核未通过';
            break;
    }
}

function getHolidayOpt($status, $isHandled, $id,$end_date)
{
    // 已经处理过了 ，只能查看
    if ($isHandled == 1) {
        // 已通过的和未通过的，都不能撤回
        if($status != '5' && $status != '6' && $status != '7'&& $status != '1'&&$status != '0'){
            return "<a onclick='og.holiday.view(" . $id . ")'>查看&nbsp;&nbsp;</a><a  onclick='og.holiday.undo(" . $id . ")'>撤回</a>";
        }
        $str = "<a onclick='og.holiday.view(" . $id . ")'>查看</a>";
        // 审批通过的可以销假
        if ( $status == '1' && daysFromNow($end_date)>0) {
            $str .= "<a onclick='og.holiday.cancelHoliday(" . $id . ")'>&nbsp;&nbsp;销假</a>";
        }
        return $str;
    } else {
        if($status != '0'){
            return "<a onclick='og.holiday.edit(" . $id . ")'>编辑</a>&nbsp;&nbsp;<a    onclick='og.holiday.undo(" . $id . ")'>撤回</a>";
        }else {
            return "<a onclick='og.holiday.view(" . $id . ")'>查看</a>";
        }
    }
}

function getHandleOpt($status, $isHandled, $id,$start,$end,$uid,$approveBegin,$approveEnd)
{
    return "<a onclick='og.holiday.view(" . $id . ")'>查看</a>&nbsp;&nbsp;<a onclick=og.holiday.agreeApply($id,$status,$isHandled,'$start','$end',$uid,$approveBegin,$approveEnd)>同意</a>&nbsp;&nbsp;<a onclick='og.holiday.rejectApply(" . $id . ",$status,$isHandled)'>不同意</a>";
}

?>
<script>
    function showSubTab(ele) {
        $('.sub-tab span').removeClass('sub-tab-content');
        ele.addClass('sub-tab-content');
        $('#my-apply-content').addClass('hide');
        $('#to-handle-content').addClass('hide');
        $('#all-holiday-content').addClass('hide');
        $('#tongji-holiday-content').addClass('hide');
        var htmlStr = $.trim(ele.html());
        if (htmlStr == '我的申请') {
            $('#my-apply-content').removeClass('hide');
        } else if (htmlStr == '待处理申请') {
            $('#to-handle-content').removeClass('hide');
        } else if (htmlStr == '所有申请') {
            $('#all-holiday-content').removeClass('hide');
        } else if (htmlStr == '数据统计') {
            $('#tongji-holiday-content').removeClass('hide');
        }
    }
    $('.sub-tab span').click(function () {
        var ele = $(this);
        og.taskSubTab = ele.attr('id');
        showSubTab(ele);
    });
	
	$(function(){
		$('.approveInfo').mouseover(function(){
			if($(this).html() != '-'){
				//将','换成'' 一次只能去掉一个
				$(this).html($(this).html().replace(',',''));
				$(this).html($(this).html().replace(',',''));
				$(this).css('position','absolute');
				$(this).css('height','165px');
				$(this).css('width',parseInt($(this).parent().css('width'))-2);
				$(this).css('border','1px solid #999');
				$(this).css('background','#FAFAFA');
			}
		});
		$('.approveInfo').mouseout(function(){
			if($(this).html() != '-'){
				$(this).css('position','static');
				$(this).css('height','28px');
				$(this).css('width','100%');
				$(this).css('border','0px solid #999');
				$(this).css('background','');
			}
		});
	})
</script>