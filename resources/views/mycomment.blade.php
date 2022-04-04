<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/png" href="logo.png" />
    <title>My Comment</title>
    </title>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="class">
        <img src="logo.png" width="100px"><br>
        <div class="judul">
            Costumer Survey
        </div>
        <br>
        <form action="" method="POST">
            @csrf

            <input name="ig" placeholder="Instagram Account @abcd123" style="padding: 8px;min-width:200px" /><br><br>
            <?php

            $array = array(
                array(
                    'judul' => 'Food Quality',
                    'isi' => 'FoodQuality',
                ),
                array(
                    'judul' => 'Menu Variety',
                    'isi' => 'MenuVariety',
                ),
                array(
                    'judul' => 'Service',
                    'isi' => 'Service',
                ),
                array(
                    'judul' => 'Atmeosphere',
                    'isi' => 'Atmeosphere',
                ),
            );
            $data = json_encode($array);
            foreach ($array as $key => $value) {
                echo $value['judul'] . '<br><br> ';
                echo 'Good 
            <input class="margin" type="radio"  name="' . $value['isi'] . '" value="1">   
            <input  class="margin" type="radio" name="' . $value['isi'] . '" value="2">   
            <input  class="margin" type="radio" checked name="' . $value['isi'] . '" value="3">  
            <input  class="margin" type="radio"  name="' . $value['isi'] . '" value="4">  
            Excellent
            <br><br><br>';
            }
            ?>
            <textarea class="comment" name="comment" placeholder="Comment & Suggestions"></textarea><br><br>
            <input class="button button1" type="submit" value="Submit">
        </form>
    </div>

    <?php
    $cmt = request()->input('comment');
    if (isset($cmt)) {
        $comment =  $cmt;
    } else {
        $comment =  '';
    }


    $ig = request()->input('ig');
    if (isset($ig)) {
        $igs =  $ig;
    } else {
        $igs =  '';
    }

    $isi = array();
    foreach ($array as $key => $value) {
        $id = $value['isi'];
        $dt = request()->input($id);
        if (isset($dt)) {
            $isi[] =  array('isi' => $dt, 'judul' => $value['judul']);
        }
    }

    $data = array('data' => $isi, 'comment' => $comment, 'ig' => $igs);

    $hasil = json_encode($data, JSON_PRETTY_PRINT);


    if (file_exists('jsoncomment.json')) {
        $ambilfile = file_get_contents('jsoncomment.json');
    } else {
        $ambilfile = '';
    }
    if ($ambilfile) {
        if ($data['data']) {

            $json = json_decode($ambilfile, true);
            if (isset($json['data'])) {
                $a = '[' . $ambilfile . ',' . $hasil . ']';
                $buatfile = file_put_contents('jsoncomment.json', $a);
                if ($buatfile) {
                    echo "
                        <script>
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Berhasil Masuk',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        </script>";
                } else {
                    echo "
                        <script>
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: 'Terjadi kegagalan',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        </script>";
                }
            } else {

                $a = json_encode(array_merge($json, array($data)), JSON_PRETTY_PRINT);
                $buatfile = file_put_contents('jsoncomment.json', $a);
                if ($buatfile) {
                    echo "
                        <script>
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Berhasil Masuk',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        </script>";
                } else {
                    echo "
                        <script>
                            Swal.fire({
                                position: 'top-end',
                                icon: 'error',
                                title: 'Terjadi kegagalan',
                                showConfirmButton: false,
                                timer: 1500
                            })
                        </script>";
                }
            }
        }
    } else {
        if ($data['data']) {
            $dh = '[' . $hasil . ']';
            $buatfile = file_put_contents('jsoncomment.json', $dh);
            echo "
            <script>
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Berhasil Masuk',
                    showConfirmButton: false,
                    timer: 1500
                })
            </script>";
        }
    }



    ?>

    <style>
        .button {
            border: none;
            color: white;
            padding: 16px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            transition-duration: 0.4s;
            cursor: pointer;
        }

        .button1 {
            background-color: white;
            color: #074230;
            border: 2px solid #074230;
        }

        .button1:hover {
            background-color: #074230;
            color: white;
        }


        .comment {
            width: 70%;
            height: 100px;
        }

        .margin {
            margin-left: 20px;
            margin-right: 20px;
        }

        .judul {
            background-color: black;
            padding: 10px;
            color: white;
            font-weight: bolder;
        }

        .class {
            width: 100%;
            border-style: solid;
            border-color: black;
            text-align: center;
            padding-bottom: 20px;
        }
    </style>
</body>

</html>