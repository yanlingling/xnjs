<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/report/report.js');
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
    <script>
        function showSubTab(ele) {
            $('.sub-tab span').removeClass('sub-tab-content');
            ele.addClass('sub-tab-content');
            $('#to-read-content').addClass('hide');
            $('#all-report-content').addClass('hide');
            var htmlStr = $.trim(ele.html());
            if (htmlStr == '效能简报') {
                $('#to-read-content').removeClass('hide');
            } else if (htmlStr == '所有简报') {
                $('#all-report-content').removeClass('hide');
            }
        }
        $('.sub-tab span').click(function () {
            var ele = $(this);
            og.taskSubTab = ele.attr('id');
            showSubTab(ele);
        });

    </script>
    <div>
        <div>
            <div class="sub-tab">
        <span id='my-apply-tab'
              class="sub-tab-content">
            效能简报
        </span>

                <div class="<?php echo $canCreateReport==1? 'inline' : 'hide'; ?>">

                    &nbsp;| &nbsp;
             <span id='all-report-tab'>
                 所有简报
            </span>


                </div>
            </div>

            <div class="clearFloat"></div>
        </div>
        <div>
            <div id="createFileBtn" style="margin-top: 10px;margin-left: 10px" class=" <?php echo $canCreateReport == 1 ? '' : 'hide' ?>">
            <span class="new-button" id='add-report'
              onclick="og.report.addReport()">新建简报</span>
            </div>
        </div>


        <!---效能简报-->
        <div id="to-read-content">
            <div class="content-wraper">

                <div class="table-header">
                    <table width='100%' class="og-table">
                        <tr>
                            <td class="file-d1">简报名称</td>
                            <td class="file-d2">创建时间</td>
                            <td class="file-d4">操作</td>
                        </tr>
                    </table>
                </div>
                <?php
                $i = 0;
                foreach ($toReadInfo as $item) {
                    ?>

                    <div>
                        <table width=100% class="og-table">
                            <?php
                            $i++;
                            if ($i % 2 == 0) {
                                echo '<tr>';
                            } else {
                                echo '<tr class="dashaltrow">';
                            }?>
                            <td class='file-d1'>
                                <?php  echo $item['name'];
                                ?>
                            </td>
                            <td class='file-d2'><?php echo $item['create_time']; ?></td>
                            <td class='file-d4'> <?php echo getReportOpt($item['id']); ?> </td>
                            </tr>
                        </table>
                    </div>
                <?php
                }
                if ($i == 0) {
                    ?>
                    <div class="no-data">当前暂无相关数据</div>
                <?php
                }
                ?>
            </div>
        </div>
        <!---end效能简报-->

        <!---所有简报-->
        <div id="all-report-content" class="hide">
            <div class="content-wraper">

                <div class="table-header">
                    <table width='100%' class="og-table">
                        <tr>
                            <td class="file-d1">文件名称</td>
                            <td class="file-d2">创建时间</td>
                            <td class="file-d4">操作</td>
                        </tr>
                    </table>
                </div>
                <?php
                $i = 0;
                foreach ($allFileInfo as $item) {
                    ?>

                    <div>
                        <table width=100% class="og-table">
                            <?php
                            $i++;
                            if ($i % 2 == 0) {
                                echo '<tr>';
                            } else {
                                echo '<tr class="dashaltrow">';
                            }?>
                            <td class='file-d1'>
                                <?php  echo $item['name'];
                                ?>
                            </td>
                            <td class='file-d2'><?php echo $item['create_time']; ?></td>
                            <td class='file-d4'> <?php echo getAllFileOpt($item['id']); ?> </td>
                            </tr>
                        </table>
                    </div>
                <?php
                }
                if ($i == 0) {
                    ?>
                    <div class="no-data">当前暂无相关数据</div>
                <?php
                }
                ?>
            </div>
        </div>
        <!---end所有简报-->



    </div>
<?php

function getReportOpt($id)
{
    $str = "<a onclick='og.report.view(" . $id . ")'>查看</a>";
    return  $str;
}

function getHasReadFileOpt($id)
{
    return "<a onclick='og.report.view(" . $id . ")'>查看</a>";
}
function getAllFileOpt($id)
{
    return "<a onclick='og.report.view(" . $id . ")'>查看</a>&nbsp;&nbsp;"
    ."<a onclick='og.report.edit(" . $id . ")'>编辑</a>&nbsp;&nbsp;"
    ."<a onclick='og.report.del(" . $id . ")'>删除</a>";
}
?>