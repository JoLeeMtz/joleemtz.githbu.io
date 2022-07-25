<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>File Manager</title>
</head>
<body>
    <h4 style="text-align:left">Welcome <?= $userInfo['name']; ?></h4>
    <form action="<?= base_url('authentification/logout'); ?>" method="GET" class="form">
        <input type="submit" value="Logout" />
    </form>
    <div class="container" style="text-align:center">
        <h1>File Manager</h1>
        <hr>

        <?php if (isset($errors)) { ?>
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        <?php } ?>

        <?= form_open_multipart('FileManager/uploadFile'); ?>
        <input type="file" name="fileUploader" size="20" />
        <br/><br/>

        <div class="form-group">
            <input type="submit" class="btn btn-primary">
        </div>
        <?= form_close(); ?>

        <?php if(isset($notification)) { ?>
            <div class="alert alert-info">
                <?php echo $notification; ?>
            </div>
        <?php } ?>

        <hr>

        <?php
        echo base_url();
            $currentFiles = array_filter(scandir(getcwd() . '\uploads'), function ($file)
            {
                if ($file[0] == '.' && strlen($file) == 1) return false;
                if ($file[0] == '.' && strlen($file) == 2 && $file[1] == '.') return false;
                return !is_dir('\uploads' . $file);
            });
        ?>

        <div class="table-responsive-sm">
            <table class="table">
                <table class="table">
                    <legend>List of files in the server</legend>
                    <thead>
                        <tr>
                        <th scope="col">#</th>
                        <th scope="col">File Name</th>
                        <th scope="col">Download</th>
                        <th scope="col">Delete</th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                            // Files in the server
                            foreach ($currentFiles as $key => $fileName)
                            {
                                $key-=1;
                                echo "<tr>";
                                
                                echo "<th scope='row'>$key</th>";
                                echo "<td>$fileName </td>";
                                echo "<td><a href='FileManager/download?filename=$fileName'>Download</a></td>";
                                //echo "<td><a href='" . getcwd() . "\\uploads\\" . $fileName . "'>Download</a></td>";
                                //echo "<form action='" . base_url('FileManager/downloadFile') . "' method='POST' class='form'>";
                                //echo "<input type='hidden' name='fileName' value='$fileName'>";
                                //echo "<td><input type='submit' class='btn btn-success' value='Download'></td>";
                                //echo "</form>";

                                echo "<form action='" . base_url('FileManager/deleteFile') . "' method='POST' class='form'>";
                                echo "<input type='hidden' name='fileName' value='$fileName'>";
                                echo "<td><input type='submit' class='btn btn-danger' value='Delete'></td>";
                                echo "</form>";

                                echo "</tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </table>
        </div>
</body>
</html>
