<!DOCTYPE html>
<html>

<head>
    <link rel="icon" type="image/png" href="logo.png" />
    <title>Data Comment</title>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <?php

    if (file_exists('jsoncomment.json')) { ?>
        <div class="class">
            <img src="logo.png" width="100px"><br>
            <div class="judul">
                Survey Result
            </div>
            <br>
            <center>
                <table id="customers">
                    <?php
                    $ambilfile = file_get_contents('jsoncomment.json');
                    $data = json_decode($ambilfile, true);
                    // print_r($data);


                    $judul = array();
                    $responden = 0;
                    foreach ($data as $key => $value) {
                        $responden += 1;
                        foreach ($value['data'] as $key => $v) {
                            $judul[] = $v['judul'];
                        }
                        $judul[] = 'Instagram';
                    }

                    ?>
                    <tr>
                        <?php
                        foreach (array_unique($judul) as $va) {
                            echo '<td>' . $va . '</td>';
                            $$va = 0;
                        } ?>
                    </tr>
                    <?php

                    if (!isset($_GET['ringkas'])) {
                        foreach ($data as $key1 => $value1) {
                            echo '<tr>';

                            foreach ($value1['data'] as $key1 => $v1) {
                                echo '<td>' . $v1['isi'] . '</td>';

                                foreach (array_unique($judul) as $va) {
                                    if ($va == $v1['judul']) {
                                        $$va += $v1['isi'];
                                    }
                                }
                            }
                            echo '<td>' . $value1['ig'] . '</td>';
                            echo '</tr>';
                        }
                    } else {

                        foreach ($data as $key1 => $value1) {
                            echo '<tr style="display:none">';
                            echo '<td>' . $value1['ig'] . '</td>';
                            foreach ($value1['data'] as $key1 => $v1) {
                                echo '<td>' . $v1['isi'] . '</td>';

                                foreach (array_unique($judul) as $va) {
                                    if ($va == $v1['judul']) {
                                        $$va += $v1['isi'];
                                    }
                                }
                            }
                            echo '</tr>';
                        }
                    }

                    echo '<tr>';
                    echo '<td Colspan="5"><center>Total</center></td>';
                    echo '</tr>';

                    echo '<tr>';
                    foreach (array_unique($judul) as $va) {
                        if ($$va) {
                            echo '<td>' . $$va . '</td>';
                        }
                    }
                    echo '</tr>';


                    echo '<tr>';
                    echo '<td Colspan="5"><center>Persentase</center></td>';
                    echo '</tr>';

                    echo '<tr>';
                    foreach (array_unique($judul) as $va) {
                        $jml = $responden * 4;
                        if ($$va) {
                            echo '<td>' . round($$va / $jml * 100) . '%</td>';
                        }
                    }
                    echo '</tr>';
                    ?>

                </table>
                <br>
            </center>
            <div>
                Total Responden : <?= $responden ?><br>
                Nilai Min : <?= $responden * 1 ?><br>
                Nilai Max : <?= $responden * 4 ?>
            </div>
            <br>
            <br>
            <div style="text-align: left;padding:10px;">
                Guest Comment :<br>
                <?php
                $no = 1;
                foreach ($data as $key => $value) {
                    if ($value['comment']) {
                        echo $no++ . ') ' . $value['comment'] . '<br>';
                    }
                }
                ?>
            </div>
            <br><button class="button button1" onclick="reset()">Reset</button>
            <?php
            if (!isset($_GET['ringkas'])) { ?>
                <button class="button button1" onclick="ringkas()">Ringkas</button>
            <?php } else { ?><button class="button button1" onclick="lengkap()">Lengkap</button>

            <?php } ?>
        </div>
    <?php } else {
        echo 'data tidak ada';
    } ?>

    <style>
        #customers {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        #customers td,
        #customers th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #customers tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #customers tr:hover {
            background-color: #ddd;
        }

        #customers th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }

        .button {
            border: none;
            color: white;
            padding: 10px 32px;
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
            color: black;
            border: 2px solid black;
        }

        .button1:hover {
            background-color: black;
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

    <script>
        function reset() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Data akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "datacomment?hapus=12345";
                }
            })
        }

        function ringkas() {
            window.location.href = "datacomment/?ringkas=5566";
        }

        function lengkap() {
            window.location.href = "datacomment/";
        }

        <?php
        if (isset($_GET['hapus'])) {
            $hapus = $_GET['hapus'];

            if ($hapus == 12345) {
                unlink('jsoncomment.json');
                echo "
                Swal.fire({
                    position: 'top-end',
                    icon: 'success',
                    title: 'Berhasil Terhapus',
                    showConfirmButton: false,
                    timer: 1500
                })";
                echo '
                window.location.href = "datacomment";';
            }
        } ?>
    </script>
</body>

</html>