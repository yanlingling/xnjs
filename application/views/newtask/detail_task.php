<?php
$item = $taskDetail;
?>

<form class="form-horizontal" role="form">
    <div class="form-group">
        <label class="col-sm-2 control-label">岗位职责:</label>
        <div class="col-sm-3">
            <p class="form-control-static"><?php echo $item['title'] ?></p>
        </div>
        <label class="col-sm-2 control-label">责任人:</label>
        <div class="col-sm-3">
            <p class="form-control-static"><?php echo $item['username'] ?></p>

        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">目标任务:</label>
        <div class="col-sm-10">
            <textarea class="form-control-static" readonly="readonly"><?php echo $item['text'] ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">当前状态:</label>
        <div class="col-sm-3">
            <p class="form-control-static"><?php echo transTaskStatus($item['light_status'],$item['toDepart']) ?></p>
        </div>
        <label class="col-sm-2 control-label">到期时间:</label>
        <div class="col-sm-3">
            <p class="form-control-static"><?php echo $item['due_date'];  ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">创建时间:</label>
        <div class="col-sm-3">
            <p class="form-control-static"><?php echo $item['created_on'];?></p>
        </div>
    </div>

    <?php
    if ($item['light_status'] == 1) {
        ?>
        <div class="form-group">
            <label class="col-sm-2 control-label">完成时间:</label>
            <div class="col-sm-3">
                <p class="form-control-static">
                    <?php echo $item['completed_on'];?>
                </p>
            </div>
            <label class="col-sm-2 control-label">督察情况:</label>
            <div class="col-sm-3">
                <p class="form-control-static" >
                    <?php echo transTaskSupervise($item['supervise_status']);?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">完成情况描述:</label>
            <div class="col-sm-10">
                <textarea class="form-control-static" readonly="readonly">
                    <?php echo $item['complete_detail'];?>
                </textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">局领导评价: </label>

            <div class="col-sm-3">
                <p class="form-control-static" >
                    <?php echo getCommentValue($item['comment_status_juzhang'])?>
                </p>
            </div>
        </div>
    <div class="form-group">
        <div  class="col-sm-2">&nbsp;</div>
        <div class="col-sm-10">
            <textarea class="form-control-static" readonly="readonly">
                <?php echo $item['comment_juzhang'];?>
            </textarea>
        </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">分管领导评价:</label>
            <div class="col-sm-3">
                <p class="form-control-static" >
                    <?php echo getCommentValue($item['comment_status_fujuzhang'])?>
                </p>
            </div>
        </div>

        <div class="form-group">
            <div  class="col-sm-2">&nbsp;</div>
            <div class="col-sm-10">
                <textarea class="form-control-static" readonly="readonly">
                    <?php echo $item['comment_fujuzhang'];?>
                </textarea>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">效能办评价:
            </label>
            <div class="col-sm-3">
                <p class="form-control-static" >
                    <?php echo getCommentValue($item['comment_status_xiaoneng'])?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <div  class="col-sm-2">&nbsp;</div>
            <div class="col-sm-10">
                <textarea class="form-control-static" readonly="readonly">
                    <?php echo $item['comment_xiaoneng'];?>
                </textarea>
            </div>
        </div>
    <?php
    }
    ?>

    <?php
    if ($item['deliver_from_departid']) {
        ?>

        <div class="form-group">
            <label class="col-sm-2 control-label">转交科室:</label>
            <div class="col-sm-3">
                <p class="form-control-static" >
                    <?php echo $item['fromDepart'];?>
                </p>
            </div>
            <label class="col-sm-2 control-label">转交时间:</label>
            <div class="col-sm-3">
                <p class="form-control-static" >
                    <?php echo $item['created_on'];?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">转交理由:</label>
            <div class="col-sm-10">
                <textarea class="form-control-static" readonly="readonly">
                    <?php echo $item['reason_deliver'];?>
                </textarea>
            </div>
        </div>
    <?php
    }
    ?>

    <?php
    if ($item['supervise_status'] == 3 || $item['supervise_status'] == 2) {

        ?>

        <div class="form-group">
            <label class="col-sm-2 control-label">随机督查意见:</label>
            <div class="col-sm-10">
                <textarea class="form-control-static" readonly="readonly">
                    <?php echo $item['supervise_feedback'];?>
                </textarea>
            </div>
        </div>
    <?php
    }
    ?>

    <?php
    if ($item['advanced_supervise'] == 3 || $item['advanced_supervise'] == 2) {

        ?>

        <div class="form-group">
            <label class="col-sm-2 control-label">随机督查意见:</label>
            <div class="col-sm-10">
                <textarea class="form-control-static" readonly="readonly">
                    <?php echo $item['advanced_supervise_feedback'];?>
                </textarea>
            </div>
        </div>
    <?php
    }
    ?>

</form>
<?php
function getCommentValue($key) {
    switch($key) {
        case 0:
            return '待评价';
        case 1:
            return '优秀';
        case 2:
            return '好';
        case 3:
            return '中';
        case 4:
            return '差';
    }
}
?>