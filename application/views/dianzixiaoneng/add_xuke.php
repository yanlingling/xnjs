<div class="form-horizontal" role="form" name="modForm" novalidate style="margin-top: 20px" target="xuke-handle">

    <div class="form-group">
        <div id="apply-id"></div>
        <label class="col-sm-4 control-label">
            <span class="red">*</span>
            申请人</label>

        <div class="col-sm-4">
            <input class="form-control" id="apply-name">
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">
            <span class="red">*</span>
            申请人片区</label>

        <div class="col-sm-4">
            <select id="apply-area" class="form-control">
                <option value="0">南片</option>
                <option value="1">北片</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">
            <span class="red">*</span>
            申请时间</label>

        <div class="col-sm-4">
            <div id="apply-time">
            </div>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">
            <span class="red">*</span>
            申请类别</label>

        <div class="col-sm-4">
            <select id="apply-type" class="form-control">
                <option value="0">药品零售企业</option>
                <option value="1">药品零售连锁企业(含门店)</option>
            </select>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">
            详情</label>

        <div class="col-sm-4">
            <textarea rows="4" class="form-control"  id="apply-detail">
                </textarea>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label">
            &nbsp;
        </label>

        <div class="col-sm-4">
            <div id="add-xuke-tip" class="bg-danger">
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">
            &nbsp;
        </label>

        <div class="col-sm-4">
            <button type="submit" class="btn btn-primary btn-block" onclick="og.addXuke.submit()"
                    >
                添加
            </button>
        </div>
    </div>
</div>
<script>
    og.addXuke.initDate();
    var xukeContent = undefined;
    <?php if(isset($xukeContent)){
        echo "eval('var xukeContent =$xukeContent;')";
    }?>;
    // 查看任务
    if (xukeContent) {
        og.addXuke.fillXukeField(xukeContent);
    }
</script>

