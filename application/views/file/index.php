<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/file/file.js');
require_javascript("og/common.js");
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>
    <script>
        function showSubTab(ele) {
            $('.sub-tab span').removeClass('sub-tab-content');
            ele.addClass('sub-tab-content');
            $('#to-read-content').addClass('hide');
            $('#has-read-content').addClass('hide');
            $('#all-file-content').addClass('hide');
            $('#jufa-file-content').addClass('hide');
            var htmlStr = $.trim(ele.html());
            if (htmlStr == '已阅文件') {
                $('#has-read-content').removeClass('hide');
            } else if (htmlStr == '待阅文件') {
                $('#to-read-content').removeClass('hide');
            } else if (htmlStr == '所有传阅文件') {
                $('#all-file-content').removeClass('hide');
            } else if (htmlStr == '局发文件') {
                $('#jufa-file-content').removeClass('hide');
            }
        }
        $('.sub-tab span').click(function () {
            var ele = $(this);
            og.fileSubTab = ele.attr('id');
            showSubTab(ele);
        });
        if (typeof og.fileSubTab != 'undefined') {
            showSubTab($('#' + og.fileSubTab));
        }
        //showSubTab($('#<?php echo $currentTabId?>'));
        og.file && og.file.initYear(<?php echo $year;?>);
    </script>
    <div>
        <div style="position: relative" id="file-header-wrapper">
            <div class="sub-tab">
        <span id='to-read-tab'
              class="<?php echo $currentTabId == 'to-read-tab'? 'sub-tab-content': ''?>">
           待阅文件
        </span>


             <span id='has-read-tab' class="<?php echo $currentTabId == 'has-read-tab'? 'sub-tab-content': ''?>">
             已阅文件
            </span>


                <div class="<?php echo $canManageFile==1? 'inline' : 'hide'; ?>">

             <span id='all-file-tab'  class="<?php echo $currentTabId == 'all-file-tab'? 'sub-tab-content': ''?>" >
           所有传阅文件</span>


                </div>
             <span id='jufa-file-tab'   class="<?php echo $currentTabId == 'jufa-file-tab'? 'sub-tab-content': ''?>">
           局发文件
            </span>
                <div class='year-select-area'>
                    <input type="radio" value="2015" name="task-year-selector" onclick="og.file.onselectyear()">&nbsp;2015
                    <input type="radio" value="2014" name="task-year-selector" onclick="og.file.onselectyear()">&nbsp;2014
                </div>
            </div>
            <div class="file-search-wrap">
                <input class='file-search-input gray' id='file-search-input' onfocus="og.file.onSearch()"
                       onblur="og.file.leaveSearch()"
                       value="<?php echo $condition ? $condition : '输入文件名进行查询' ?>">
                <span onclick="og.file.beginSearch()" class="searchIcon"></span>
            </div>
            <div class="clearFloat"></div>
        </div>
        <div>
            <div id="createFileBtn" style="margin-top: 10px;margin-left: 10px" class=" <?php echo $canManageFile == 1 ? '' : 'hide' ?>">
            <span class="new-button" id='add-file'
              onclick="og.file.addFile()">新建文件</span>
            <span class="new-button" id='add-jufa-file'
                  onclick="og.file.addJufaFile()">新建局发文件</span>
            </div>
        </div>


        <!---待阅文件-->
        <div id="to-read-content">
            <div class="content-wraper">

                <div class="table-header">
                    <table width='100%' class="og-table">
                        <tr>
                            <td class="file-d1">文件名称</td>
                            <td class="file-d2">创建时间</td>
                            <td class="file-d3">上次阅读人</td>
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
                                <?php  echo $item['file_name'];
                                ?>
                            </td>
                            <td class='file-d2'><?php echo $item['create_time']; ?></td>
                            <td class='file-d3'> <?php echo $item['from_user']; ?> </td>
                            <td class='file-d4'> <?php echo getFileOpt($item['file_id'], $item['id']); ?> </td>
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
        <!---end待阅文件-->
        <!---已阅文件-->
        <div id="has-read-content" class="hide">
            <div class="content-wraper">

                <div class="table-header">
                    <table width='100%' class="og-table">
                        <tr>
                            <td class="file-d1">文件名称</td>
                            <td class="file-d2">创建时间</td>
                            <td class="file-d3">阅读时间</td>
                            <td class="file-d4">操作</td>
                        </tr>
                    </table>
                </div>
                <?php
                $i = 0;

                foreach ($hasReadInfo as $item) {
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
                                <?php  echo $item['file_name'];
                                ?>
                            </td>
                            <td class='file-d2'><?php echo $item['create_time']; ?></td>
                            <td class='file-d2'><?php echo $item['handle_time']; ?></td>
                            <td class='file-d4'> <?php echo getHasReadFileOpt($item['file_id']); ?> </td>
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
        <!---end已阅文件-->

        <!---所有文件-->
        <div id="all-file-content" class="hide">
            <div class="content-wraper">

                <div class="table-header">
                    <table width='100%' class="og-table">
                        <tr>
                            <td class="file-d1">文件名称</td>
                            <td class="file-d2">创建时间</td>
                            <td class="file-d3">传阅状态</td>
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
                            <td class='file-d3'><?php echo getFileStatus($item['not_read_count']); ?></td>
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
        <!---end所有文件-->

        <!---局发文件-->
        <div id="jufa-file-content" class="hide">
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
                foreach ($jufaFileInfo as $item) {
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
                            <td class='file-d4'> <?php echo getJufaFileOpt($item['id'],$canManageFile); ?> </td>
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
        <!---end局发文件-->

    </div>
<?php

function getFileOpt($id, $read_id)
{
    $str = "<a onclick='og.file.handleFile($id,$read_id)'>处理</a>";
    return  $str;
}

function getHasReadFileOpt($id)
{
    return "<a onclick='og.file.view(" . $id . ")'>查看</a>" .
    "&nbsp;&nbsp;<a onclick='og.file.rehandleFile($id)'>再处理</a>";
}
function getAllFileOpt($id)
{
    return "<a onclick='og.file.view(" . $id . ")'>查看</a>&nbsp;&nbsp;"
    ."<a onclick='og.file.del(" . $id . ")'>删除</a>";
}
function getJufaFileOpt($id,$canManage)
{
    $str = "<a onclick='og.file.view(" . $id . ",2)'>查看</a>";
    if ($canManage) {

        $str.= "&nbsp;&nbsp;<a onclick='og.file.del(" . $id . ")'>删除</a>";
    }
    return $str;
}
function getFileStatus($count) {
    // 都已经读完了，绿色
    if ($count == 0){
       return '<span class="ico-task-light-green" title="传阅完成"></span>';
    } else {
        return '<span class="ico-task-light-gray" title="传阅中"></span>';
    }
}
?>