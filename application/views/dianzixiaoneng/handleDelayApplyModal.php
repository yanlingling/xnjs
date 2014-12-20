<div class="modal" id="xukeHandleDelayApplyModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                        class="sr-only">Close</span></button>
                <h4 class="modal-title">处理延期申请</h4>
            </div>
            <div class="modal-body">
                <form role="form">
                    <input type="hidden" id="xuke-task-id">
                    <input type="hidden" id="xuke-apply-id">

                    <div class="form-group">
                        <label class="control-label">
                             延期原因:
                        </label>
                        <textarea id="xuke-delay-apply-detail" class="form-control" roww="4" readonly></textarea>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-2">
                            <label class="control-label">
                                 申请延期:
                            </label>
                        </div>

                        <div class="col-sm-3">
                        <span id="xuke-delay-apply-day" class="form-control"></span>
                        </div>
                        <div class="col-sm-1">
                        <label class="control-label">
                            天 
                        </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-4">
                            <span class="bg-danger" id="xuke-apply-error"> </span>
                            <span class="bg-success" id="xuke-apply-info">  </span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" onclick="og.dianzixiaoneng.agreeDelayApply()">同意</button>
                <button type="button" class="btn btn-default" onclick="og.dianzixiaoneng.disagreeDelayApply()" data-dismiss="modal">拒绝</button>
            </div>
        </div>
    </div>
</div>

