<div id="xukeTabContent" class="<?php echo $tab == 'xuke' ? '' : 'hide'; ?>">
    <div>
        <div  style="margin-top: 10px;margin-left: 10px"
             class=" <?php echo $isYaoxie2== true ? '' : 'hide' ?>">
            <span class="new-button"
                  onclick="og.dianzixiaoneng.add()">新建许可</span>
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
