<?php
if (isset($_GET['opt'])) {
    // print_r($_FILES);
    if ($_FILES['videoFile']['size'] > 200 * 1024 * 1024) {
        echo "<script>alert('文件过大')</script>";
    } else {
        if (file_exists("upload/" . $_FILES["videoFile"]["name"])) {
            echo "<script>alert('文件已经存在')</script>";

        } else {

            move_uploaded_file($_FILES["videoFile"]["tmp_name"],
                "upload/" . $_FILES["videoFile"]["name"]);
            echo "<script>alert('成功');//location.reload(); </script>";
        }
    }

} else {

    ?>
    <form id='uploadFile' action='uploadVedio.php?opt=add' method="POST" enctype="multipart/form-data">
        <table>
            <tr>
                <td>选择视频</td>
                <td>
                    <input type="file" name="videoFile" id="videoFile"/>
                    <input type="hidden" name="MAX_FILE_SIZE" value="500000">
                </td>
                <td>
                    文件格式为flv，小于200M
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <input type="button" value="提交" onclick="beginSubmit()">
                </td>
            </tr>
        </table>
    </form>
    <script>
        function beginSubmit() {
            var splits = document.getElementById('videoFile').value.split('.');
            if (splits[splits.length - 1] != 'flv') {
                alert('请上传flv格式的文件');
                return;
            }
            document.getElementById('uploadFile').submit();
        }
    </script>
<?php
}
?>
