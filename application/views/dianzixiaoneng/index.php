<?php
require_javascript('og/bootstrap.min.js');
require_javascript('og/jquery.min.js');
require_javascript('og/cookie.js');
require_javascript("og/CSVCombo.js");
require_javascript("og/common.js");
require_javascript("og/DateField.js");
require_javascript('og/dianzixiaoneng/dianzixiaoneng.js');
require_javascript('og/dianzixiaoneng/addXuke.js');
$genid = gen_id();
?>

<div>
    <div>
        <div class="sub-tab">
            <span id='xuke-sub-link'
                  class="<?php echo $tab == 'xuke' ? 'sub-tab-content' : ''; ?>"><?php //echo $departName; ?>许可受理</span>
            <span id='yanshou-sub-link' class="<?php echo $tab == 'yanshou' ? 'sub-tab-content' : ''; ?>">许可验收</span>
            <span id='fazheng-sub-link' class="<?php echo $tab == 'fazheng' ? 'sub-tab-content' : ''; ?>">审批发证</span>
            <span id='all-sub-link' class="<?php echo $tab == 'all' ? 'sub-tab-content' : ''; ?>">所有许可</span>
            <span id='delay-sub-link' class="<?php echo $tab == 'delay' ? 'sub-tab-content' : ''; ?>">延期申请</span>
        </div>
        <div class="clearFloat"></div>
    </div>
    <?php
    include('xukeshouli.php');
    include('xukeyanshou.php');
    include('xukefazheng.php');
    include('allxuke.php');
    include('delayApply.php');
    include('handleModal.php');
    include('delayApplyModal.php');
    ?>
</div>
<script>

    // 用户点击过tab的切换，按用户点击的来
    if (typeof og.dianzixiaonengSubTab != 'undefined') {
        showSubTab($('#' + og.dianzixiaonengSubTab));
    }

    function showSubTab(ele) {
        $('.sub-tab span').removeClass('sub-tab-content');
        ele.addClass('sub-tab-content');
        $('#xukeTabContent').addClass('hide');
        $('#yanshouTabContent').addClass('hide');
        $('#allTabContent').addClass('hide');
        $('#fazhengTabContent').addClass('hide');
        if (ele.html() == '许可受理') {
            $('#xukeTabContent').removeClass('hide');
        } else if (ele.html() == '所有许可') {
            $('#allTabContent').removeClass('hide');
        } else if (ele.html() == '许可验收') {
            $('#yanshouTabContent').removeClass('hide');
        } else if (ele.html() == '审批发证') {
            $('#fazhengTabContent').removeClass('hide');
        }else if (ele.html() == '延期申请') {
            $('#delayTabContent').removeClass('hide');
        }
    }
    $('.sub-tab span').click(function () {
        var ele = $(this);
        og.dianzixiaonengSubTab = ele.attr('id');
        showSubTab(ele);
    });
</script>
