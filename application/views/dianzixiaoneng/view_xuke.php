<div class="xuke-detail-container">
    <div class="container-fluid">
        <div class="row">
            <label class="col-sm-2 control-label">申请人:</label>

            <div class="col-sm-4">
                <?php
                echo $xukeInfo['apply_name'];
                ?>
            </div>
            <label class="col-sm-2 control-label">申请人片区:</label>

            <div class="col-sm-4">
                <?php
                echo getApplyArea($xukeInfo['apply_area']);;
                ?>
            </div>

        </div>
        <div class="row">
            <label class="col-sm-2 control-label">申请时间:</label>

            <div class="col-sm-4">
                <?php
                echo $xukeInfo['apply_time'];
                ?>
            </div>
            <label class="col-sm-2 control-label">申请类别:</label>

            <div class="col-sm-4">
                <?php
                echo getApplyType($xukeInfo['apply_type']);;
                ?>
            </div>

        </div>
        <div class="row">
            <label class="col-sm-2 control-label">详情:</label>

            <div class="col-sm-8">
                <textarea class="form-control-static"
                          readonly="readonly"><?php echo $xukeInfo['apply_detail'] ?></textarea>
            </div>
        </div>
    </div>
    <h3>处理记录</h3>
    <table class="table">
        <thead>
        <td>处理时间</td>
        <td>内容</td>
        <td>处理结果</td>
        </thead>
        <tbody>
        <?php
        foreach ($handleInfo as $item) {
            ?>
            <tr>
                <td><?php echo $item['complete_time'] ? $item['complete_time'] : '-'; ?></td>
                <td><?php echo transDianziType($item['sub_process']); ?></td>
                <td><?php echo transDianziResult($item['result'],$item['sub_process']); ?></td>
            </tr>
        <?php
        }

        ?>
        </tbody>
    </table>
</div>


