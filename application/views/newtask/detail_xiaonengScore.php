<?php
$item = $taskDetail;
function  getXiaonengScore($status) {
    if ($status ==1) {
        return 50;
    }
    if ($status ==2) {
        return 40;
    }
    if ($status ==3) {
        return 30;
    }
    return '-';
}
?>

<form class="form-horizontal" role="form">
    <div class="form-group">
        <label class="col-sm-2 control-label">完成时限得分:</label>
        <div class="col-sm-3">
            <p class="form-control-static"><?php
                if (strtotime($item['completed_on']) >strtotime($item['due_date'])) {
                        echo '30';
                    } else {
                    echo '50';
                }
                ?></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">效能办评价得分:</label>
        <div class="col-sm-3">
            <p class="form-control-static"><?php
                echo getXiaonengScore($item['comment_status_xiaoneng'])
                ?></p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">分管领导评价得分:</label>
        <div class="col-sm-3">
            <p class="form-control-static"><?php
                echo getXiaonengScore($item['comment_status_fujuzhang'])
                ?></p>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">局领导评价得分:</label>
        <div class="col-sm-3">
            <p class="form-control-static"><?php
                echo getXiaonengScore($item['comment_status_juzhang'])
                ?></p>
        </div>
    </div>


</form>
<?php
function getCommentValue($key) {
    switch($key) {
        case 0:
            return '待评价';
        case 1:
            return '好';
        case 2:
            return '中';
        case 3:
            return '差';
    }
}
?>