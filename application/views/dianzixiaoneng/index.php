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
        <span id='xuke-sub-link' class="<?php echo $tab == 'xuke' ? 'sub-tab-content' : ''; ?>"><?php //echo $departName; ?>许可受理</span>
            <span id='yanshou-sub-link' class="<?php echo $tab == 'yanshou' ? 'sub-tab-content' : ''; ?>">许可验收</span>
            <span id='fazheng-sub-link' class="<?php echo $tab == 'fazheng' ? 'sub-tab-content' : ''; ?>">shenpifazheng</span>
        </div>
        <div class="clearFloat"></div>
    </div>
    <?php
    include('xukeshouli.php');
    include('yanshou.php');
    ?>
</div>
