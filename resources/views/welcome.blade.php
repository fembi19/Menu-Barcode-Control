<!DOCTYPE html>
<html>

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Our Menu</title>

    <link rel="icon" type="image/png" href="logo.png" />

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
</head>

<body style="background-color: #2e2e36;">
    <div class="container">
        <div class="row">
            <div class="col">


                <a href="mycomment">
                    <div class="tombol"> Klik untuk kritik & saran
                        <img src="https://cdn-icons-png.flaticon.com/512/1381/1381552.png" width="30px">
                    </div>
                </a>
                <?php
                $folder = "assets/pages/"; //Sesuaikan Folder nya

                $content = file_get_contents("datagambar.json");
                //mengubah standar encoding
                $content = utf8_encode($content);

                //mengubah data json menjadi data array asosiatif
                $result = json_decode($content, true);
                sort($result);
                $no = 1;
                foreach ($result as $value) {
                    $nama_file = $value['nama_file'];

                    if ($no == 1) {
                        echo "<img src='$folder$nama_file' class='img-fluid animate__animated animate__bounce'>";
                    } else {
                        echo "<img src='$folder$nama_file' class='img-fluid'>";
                    }
                    $no++;
                }

                ?>

                <style>
                    a {
                        text-decoration: none;
                    }

                    .tombol {
                        text-align: center;
                        padding: 15px;
                        margin-top: 10px;
                        border-radius: 10px 10px 0px 0px;
                        background-color: white;
                    }

                    .img-fluid {
                        width: 100%;
                    }
                </style>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>

</body>

</html>