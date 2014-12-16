<div class="modal" id="xukeHandleModal"  tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" >许可处理</h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    <input type="hidden" id="xuke-task-id">
                    <input type="hidden" id="xuke-id">
                    <input type="hidden" id="xuke-type">
                    <input type="hidden" id="sub-process">
                    <div class="form-group">
                        <label class="radio-inline">
                            <input type="radio" name="xuke-status"  value="1">通过
                        </label>
                        <label class="radio-inline">
                            <input type="radio" name="xuke-status"  value="0">拒绝
                        </label>
                    </div>

                    <div class="">
                        <textarea id="xuke-status-detail" class="form-control"  roww="4" placeholder="请输入处理详情..."></textarea>
                    </div>
                    <div class="form-group" >
                        <div class="col-sm-4">
                            <span class="bg-danger" id="comment-error"> </span>
                            <span class="bg-success" id="comment-info">  </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="xuke-statsu-confirm" onclick="og.dianzixiaoneng.handleTaskStatusOK()">提交</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
            </div>
        </div>
    </div>
</div>

