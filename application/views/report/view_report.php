<?php

require_javascript('og/modules/addMessageForm.js');
require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
require_javascript('og/report/report.js');
require_javascript('og/common.js');
require_javascript('editor/ueditor.config.js');
require_javascript('editor/ueditor.all.js');
require_javascript('editor/lang/zh-cn/zh-cn.js');
function HtmlEncode($fString)
{
    if ($fString != "") {

        $fString = str_replace('"', '&quot;', $fString);
        $fString = str_replace('\'', '&#39;', $fString);

    }
    return $fString;
}

$genid = gen_id();
?>
<div class="learn-title">
    <span>
        <?php
        echo $reportInfo['name'];
        ?>
    </span>
</div>
<div style="padding: 5px">
    <span>
        <?php
        echo $reportInfo['content'];
        ?>
    </span>
</div>

<script>

</script>




