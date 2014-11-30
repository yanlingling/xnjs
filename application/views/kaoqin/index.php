<?php
require_javascript('og/modules/addMessageForm.js');
require_javascript('og/kaoqinducha/kaoqinducha.js');
require_javascript("og/jquery.min.js");
$genid = gen_id();
?>

<div>
    <div class="sub-tab">
        <span id='kaoqin-sub-link'
              class="<?php echo $tab == 'kaoqin' ? 'sub-tab-content' : ''; ?>"><?php //echo $departName; ?>考勤通报</span>
        &nbsp;| &nbsp;
        <span id='jilv-sub-link' class="<?php echo $tab == 'jilv' ? 'sub-tab-content' : ''; ?>">纪律检查</span>
    </div>
    <div class="clearFloat"></div>
</div>

<!--begin of 考勤通报-->
<div id="kaoqinTabContent" class="<?php echo $tab == 'kaoqin' ? '' : 'hide'; ?>">
    <div>
        <div id="createFileBtn" style="margin-top: 10px;margin-left: 10px"
             class=" <?php echo $canManageKaoqinducha == 1 ? '' : 'hide' ?>">
            <span class="new-button" id='add-kaoqinducha-file'
                  onclick="og.kaoqinducha.addKaoqinducha()">新建考勤督查</span>
        </div>
    </div>

    <div>
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
            foreach ($kaoqinduchaFileInfo as $item) {
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
                        <td class='file-d4'> <?php echo getKaoqinOpt($item['id'], $canManageKaoqinducha); ?> </td>
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

</div>
<!--end of 考勤通报-->
<!--begin of 纪律检查-->
<div id="jilvTabContent" class="<?php echo $tab == 'jilv' ? '' : 'hide'; ?>">

    <div>
        <div id="createFileBtn" style="margin-top: 10px;margin-left: 10px"
             class=" <?php echo $canManageJilvjiancha == 1 ? '' : 'hide' ?>">
            <span class="new-button" id='add-jilvjiancha'
                  onclick="og.jilvjiancha.addJilvjiancha()">新建纪律检查</span>
        </div>
    </div>

    <div class="content-wraper">

        <div class="table-header">
            <table width='100%' class="og-table">
                <tr>
                    <td class="duty-d1">时间</td>
                    <td class="duty-d2">检查人</td>
                    <td class="duty-d3">创建时间</td>
                    <td class="duty-d4">创建人</td>
                    <td class="duty-d5">操作</td>
                </tr>
            </table>
        </div>
        <?php
        $i = 0;
        foreach ($jilvInfo as $item) {
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
                    <td class='duty-d1'>
                        <?php echo $item['jiancha_time'];
                        ?>
                    </td>


                    <td class='duty-d2'><?php echo $item['onDutyUser']; ?></td>
                    <td class="duty-d3"><?php echo $item['create_time']; ?>
                    <td class='duty-d4'><?php echo $item['username']; ?></td>
                    <td class='duty-d5'><?php
                        $str="<a onclick='og.jilvjiancha.viewJilvjiancha(" . $item['id'] . ")'>查看</a>";
                        if ($canManageJilvjiancha) {
                            $str.="&nbsp;&nbsp;<a onclick='og.jilvjiancha.del(" . $item['id'] . ")'>删除</a>";
                            //echo "<a onclick='og.jilvjiancha.writeJilvjiancha(1," . $item['id'] . ")'>编辑</a>";
                        } else if (logged_user()->getUserRole() == '局长') {
                            $str.="&nbsp;&nbsp;<a onclick='og.jilvjiancha.editJilvjiancha(" . $item['id'] . ")'>编辑</a>";
                        }
                        echo $str;
                        ?></td>
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
</div>
<!--end of 纪律检查-->
<?php


function getKaoqinOpt($id, $canManage)
{
    $str = "<a onclick='og.kaoqinducha.view(" . $id . ",2)'>查看</a>";
    if ($canManage) {
        $str .= "&nbsp;&nbsp;<a onclick='og.kaoqinducha.del(" . $id . ")'>删除</a>";
    }
    return $str;
}

?>
<script>

    // 用户点击过tab的切换，按用户点击的来
    if (typeof og.kaoqinduchaSubTab != 'undefined') {
        showSubTab($('#' + og.kaoqinduchaSubTab ));
    }

    function showSubTab(ele) {
        $('.sub-tab span').removeClass('sub-tab-content');
        ele.addClass('sub-tab-content');
        $('#kaoqinTabContent').addClass('hide');
        $('#jilvTabContent').addClass('hide');
        if (ele.html() == '考勤通报') {
            $('#kaoqinTabContent').removeClass('hide');
        } else if (ele.html() == '纪律检查') {
            $('#jilvTabContent').removeClass('hide');
        }
    }
    $('.sub-tab span').click(function () {
        var ele = $(this);

        og.kaoqinduchaSubTab = ele.attr('id');
        showSubTab(ele);
    });
</script>

