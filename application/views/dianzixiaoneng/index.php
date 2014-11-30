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
            <span id='yanshou-sub-link' class="<?php echo $tab == 'yanshou' ? 'sub-tab-content' : ''; ?>">验收材料受理</span>
        </div>
        <div class="clearFloat"></div>
    </div>
    <?php
    include('xukeList.php');
    ?>
</div>