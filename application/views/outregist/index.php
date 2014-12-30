<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/out/out.js');
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
<?php
    if($userRole == '局长' || $departName == '效能办'){
?>
        <div class="new-button" style="margin-left: 10px; margin-top: 10px;" onclick="og.out.viewData()">数据统计</div>
<?php
    }
?>
<div>
    <?php
    $lastDepart = '';
    foreach ($allUserInfo as $item) {
        // 第一个科室，或者跟上一个科室的id不一样，就重新画个框
        if ($lastDepart == '' || $lastDepart != $item['depart_id']){
            // 结束上一个框
            if ($lastDepart != $item['depart_id']){
                ?>
                </div>
                </div>
            <?php
            }
   ?>
            <div class="kaoqin-depart-wrapper">
                <div class="header"><?php echo $item['depart_name'];?></div>
                <div class="body">
                    <p class="item itemheader"><span class="name">人&nbsp;&nbsp;员</span><span class="status">状&nbsp;&nbsp;态</span></p>
            <?php echo getUserLine($item);?>


    <?php
        }else{
            echo getUserLine($item);
        }
        $lastDepart = $item['depart_id'];
    }
    ?>
</div>
<?php
function transKaoqinStatus($status,$isSelf,$userid){
    $str = '';
    switch ($status) {
        case 1:
            $str ='<a class="red">公差</a>';
            break;
        case 2:
            $str ='<a class="blue">病假</a>';
            break;
        case 3:
            $str ='<a class="blue">事假</a>';
            break;
        case 4:
            $str ='<a class="red">其它</a>';
            break;
        case 5:
            $str ='<a class="blue">年休假</a>';
        break;
        case 8:
            $str ='在岗';
            break;
    }
    // 自己可以把自己改成在岗状态
    if ($isSelf && $status!=8){
        $str .='<span class="turnToWork"  titile="在岗" onclick="og.out.setAtWork('.$userid.','.$status.')"></span>';
    }
    return $str;
}

function getUserLine($item){
    $className = 'item';
    // 不在岗的灰色
  /*  if($item['kaoqin_status']!=8){
        $className = 'item notin';
    }*/
    $str = '<div class="'.$className.'"><span class="name">'.
        $item['username'].
        '</span><span class="status" id="status-'.$item['id'].'">'.transKaoqinStatus($item['kaoqin_status'],$item['isSelf'],$item['id']).'</span>';
    if($item['optable'] == 'y'){
        $str .= '<span class="editOptIcon"  userid="'.$item['id'].'"></span>'.
            '<ul class="kaoqin-float hide">
            <li value="1" onclick=og.out.changeStatus(1,'.$item['id'].','.$item['kaoqin_status'].')>公差</li>
            <li value="2" onclick=og.out.changeStatus(2,'.$item['id'].','.$item['kaoqin_status'].')>病假</li>
            <li value="3" onclick=og.out.changeStatus(3,'.$item['id'].','.$item['kaoqin_status'].')>事假</li>
            <li value="5" onclick=og.out.changeStatus(5,'.$item['id'].','.$item['kaoqin_status'].')>年休假</li>
            <li value="4" onclick=og.out.changeStatus(4,'.$item['id'].','.$item['kaoqin_status'].')>其它</li>
            <li value="8" onclick=og.out.changeStatus(8,'.$item['id'].','.$item['kaoqin_status'].')>在岗</li>
            </ul>';
    }
        $str .= '</div>';
    return $str;
}
?>
<script>
    $('.editOptIcon').click(og.out.editClickHandler);
    // 每60秒更新一次状态
    setInterval(og.out.updateStatus,1000*60);
    // 点击控件区域以外的地方，消失选项列表
    $('body').click(function () {
        $('.kaoqin-float').addClass('hide');
    });
    $('.kaoqin-float').click(function (e) {
        e.stopPropagation();
    });
    $('.editOptIcon').click(function (e) {
        e.stopPropagation();
    });
</script>