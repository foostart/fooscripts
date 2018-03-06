<?php
include('./standard.php');
?>
<html>
    <head>
        <title>Chương trình kiểm tra Block</title>
        <link href="styles.css" rel="stylesheet" type="text/css"/>
    </head>
    <body>
        <div class='container'>
            <h1>Chương trình chuẩn hóa block</h1>

            <form method="GET" action="#">
                <label for='block_id'>Đường dẫn tới block</label>
                <input type="text" id ='block_id' name="block_url">
                <span class='hint-text'>Ví dụ: http://localhost/blocks/201/201.php</span>
                <br>
                <input type="submit">
            </form>

            <?php if (!empty($_GET) && !empty($_GET['block_url'])): ?>

                <?php
                    $classes = $standard->checkHtmlCss($_GET['block_url']);
                ?>
            <h2>Thẻ CSS dư</h2>
            <caption>Có <b><?php echo count($classes) ?></b> thẻ CSS dư</caption>
                    <table>
                        <tr>
                            <td style="width:100px;">#<?php $counter = 1;?></td>
                            <td>Class name</td>
                        </tr>
                        <?php if (!empty($classes)): ?>

                            <?php foreach ($classes as $class): ?>
                                <tr>
                                    <td><?php echo $counter;$counter++ ?></td>
                                    <td><?php echo $class ?></td>
                                </tr>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </table>

            <?php endif;?>
        </div>
    </body>
</html>