<?php

require_javascript('og/modules/addMessageForm.js');
require_javascript('og/tasks/main.js');
require_javascript("og/DateField.js");
require_javascript("og/jquery.min.js");
require_javascript('og/learning/learning.js');
require_javascript('og/common.js');

$genid = gen_id();
?>

<div style="margin-top: 10px;float: right;margin-right: 30px">
    <div class="<?php
    if ($opt == 'view') {
        echo 'hide';
    }
    ?>">
    <span id="learning-timer" class="red">
        <span id="learning-timer-hour">0</span>时
        <span id="learning-timer-minute">0</span>分
        <span id="learning-timer-second">0</span>秒
    </span>
    <span class="new-button <?php
    if ($opt == 'view') {
        echo 'hide';
    }
    ?>" id='complete-learning'
          onclick="og.learning.completeLearning(<?php echo $learnId; ?>)">学习完成</span>
    </div>
</div>
<div class="learn-title">
    <span>
        <?php
        echo $contentInfo['name'];
        ?>
    </span>
</div>

<div class="<?php echo $contentInfo['content_type'] == 0 ? '' : 'hide' ?>" style="padding: 5px">
    <span>
        <?php
        echo $contentInfo['content'];
        ?>
    </span>
</div>

<div class="<?php echo $contentInfo['content_type'] == 1 ? '' : 'hide' ?>  vedio-wrapper">
    <object id="player" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" name="player" width="820" height="470">
        <param name="movie" value="player.swf"/>
        <param name="allowfullscreen" value="true"/>
        <param name="allowscriptaccess" value="always"/>
        <param name="wmode" value="transparent"/>
        <param name="flashvars" value="file=upload/<?php echo $contentInfo['location'] ?>&image=preview0.jpg"/>
        <embed
            type="application/x-shockwave-flash"
            id="player2"
            name="player2"
            src="player.swf"
            width="820"
            height="470"
            allowscriptaccess="always"
            allowfullscreen="true"
            flashvars="file=upload/<?php echo $contentInfo['location'] ?>&image=preview0.jpg"
            wmode="transparent"
            />
    </object>
</div>

<div id="view-comment-area">
    <div class="learn-comment">学习体会</div>
    <table width="100%"  style="
    border: 1px solid #D1F4D5;
">
    <?php
    $i = 0;
    foreach ($commentList as $item) {
         $i++;
        ?>
      <tr>
          <td  class="topicTabletd1"><?php echo $item['username'];?></td>
          <td class="topicTabletd2">
              <div class="firstLine">
                  <span class="line_image"></span>
                  <span >发表于&nbsp;&nbsp;<?php echo $item['create_time'];?></span>
              </div>
              <div class="postContent">
                  <?php echo $item['comment'];?>
              </div>
          </td>
      </tr>

    <?php
    }

    if ($i == 0) {
        ?>
        <div class="no-data">现在还没有人发布学习体会。</div>
    <?php
    }
    ?>

    </table>
</div>
<div id="create-comment-area">
    <div id="comment-box"></div>
    <div class="new-button"
         onclick="og.learning.publishComment(<?php echo $contentInfo['id']?>)">
        发布学习体会
    </div>
    <span class="red" id="comment-error"></span>
</div>

<script>
    new Ext.form.HtmlEditor({
        renderTo: 'comment-box',
        id: 'learning-comment-box',
        width: 1000,
        height: 200,
        fieldLabel: '',
        enableAlignments: true,  //允许编辑器中的按钮居左，居中和居右显示
        enableColors: true,      //允许前景/高亮颜色按钮显示
        enableFont: true,       //允许增大、缩小字号按钮显示
        enableFontSize: true,   //Enable the increase/decrease font size buttons (defaults to true)
        enableFormat: true,     //Enable the bold, italic and underline buttons (defaults to true)
        enableLinks: true,      //Enable the create link button. Not available in Safari. (defaults to true)
        enableLists: true,      //Enable the bullet and numbered list buttons. Not available in Safari. (defaults to true)
        enableSourceEdit: true,  //Enable the switch to source edit button. Not available in Safari. (defaults to true)
        value: '',
        fontFamilies: ["宋体", "隶书", "黑体"]
    });
    var learningBeginTime = new Date();
    function thenceThen(){
        var date1=learningBeginTime;
        var totalSecs=(new Date()-date1)/1000;
        var days=Math.floor(totalSecs/3600/24);
        var hours=Math.floor((totalSecs-days*24*3600)/3600);
        var mins=Math.floor((totalSecs-days*24*3600-hours*3600)/60);
        var secs=Math.floor((totalSecs-days*24*3600-hours*3600-mins*60));
        $('#learning-timer-hour').html(hours);
        $('#learning-timer-minute').html(mins);
        $('#learning-timer-second').html(secs);
    }
    var clock=setInterval("thenceThen()", 1000);
</script>