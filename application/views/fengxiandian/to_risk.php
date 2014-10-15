<?php

require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/main.js');
require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
require_javascript('og/risk/risk.js');
require_javascript('og/common.js');

$genid = gen_id();
?>

<div style="margin-top: 10px;float: right;margin-right: 30px">

</div>
<div class="learn-title">
    <span>
        <?php
        echo $contentInfo['name'];
        ?>
    </span>
</div>

<div class="risk-question-area">
    <?php
    $i = 1;
    foreach ($question as $item) {

        ?>
        <div class="risk-question-learn">
            <input type="hidden" value=" <?php echo $item['id']; ?>" id="question-id-<?php echo $i; ?>">
            <div class="title">
                <?php echo $i; ?>. <?php echo $item['question']; ?>
            </div>
            <div class="answer">
             <input class="answerRadio" type="radio" <?php
             echo isset($item['answer'])&&($item['answer'] == $item['answer1'])?'checked':'';
             echo isset($item['answer'])?'  disabled':'';
             ?>
                    value="<?php echo $item['answer1']; ?>" name="answer-radio-<?php echo $i; ?>"/>
                <span><?php echo $item['answer1']; ?></span>
            &nbsp; &nbsp; &nbsp;
            <input class="answerRadio" type="radio" <?php
             echo isset($item['answer'])&&($item['answer'] == $item['answer2'])?'checked':'';
            echo isset($item['answer'])?'  disabled':'';
            ?>
                   value="<?php echo $item['answer2']; ?>"  name="answer-radio-<?php echo $i; ?>"/>
                <span><?php echo $item['answer2']; ?></span>
            </div>
        </div>
    <?php
        $i++;
    }
    ?>

</div>

<div style="text-align: center" class="<?php echo $opt == 'view'?'hide':'';?>">
    <div class="new-button" onclick="og.risk.completeRisk(<?php
    echo $learnId.','. $contentInfo['id'];
    ?>)" id="sumit-answer-risk">提交</div><span id="error-tip" class="red"></span>
</div>

<script>

</script>