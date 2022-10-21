<?php
?>

<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <link rel="icon" type="image/png" href="/logo.png" />
    <title>Menu Barcode System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Latest Sortable -->
    <script src="https://raw.githack.com/SortableJS/Sortable/master/Sortable.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" integrity="sha512-SfTiTlX6kk+qitfevl/7LibUOeJWlt9rbyDn92a1DqWOw9vWG2MFoays0sgObmWazO5BQPiFucnnEAjpAB+/Sw==" crossorigin="anonymous" referrerpolicy="no-referrer" />




</head>

<body>

    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif


    @if ($err = Session::get('error'))
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $err }}</strong>
    </div>
    @endif

    <br>
    <a href="{{ url('/datacomment') }}"><button class="btn btn-success">Data Comment</button></a>
    <br><br>

    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Gambar</th>
                <th scope="col">Ukuran</th>
                <th scope="col">Nama</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody id="listWithHandle">
            <?php

            $content = file_get_contents('datagambar.json');

            //mengubah data json menjadi data array asosiatif
            $result = array_unique(json_decode($content, true));

            if ($result) {

                $no = 1;

                function getPotongAngka($angka)
                {
                    $angka = (int) $angka;
                    $i_str = (string) $angka;
                    $angkax = substr($i_str, 0, -3);
                    return (int) $angkax;
                }

                foreach ($result as $value) {
                    $nama_file = $value['nama_file'];
                    $id = $value['id'];
                    if (file_exists('assets/pages/' . $nama_file)) {
                        $size = filesize('assets/pages/' . $nama_file);
                        echo "<tr data-id='$id' class='list-group-item'><th class='move' style='cursor: move;' scope='row' >  <span class='fa fa-arrows' aria-hidden='true'></span></th><td><a href='assets/pages/$nama_file' target='_blank'><img height='60px' src='assets/pages/$nama_file' ></a></td><td>" . getPotongAngka($size) . " Kb </td><td><a href='assets/pages/$nama_file' target='_blank'>$nama_file</a></td><td><form method='get'  id='myForm' action='" . url('/') . "/hapus'><input type='hidden' name='id' value='$id'><input type='hidden' name='url' value='assets/pages/$nama_file'><button type='submit'  id='btn-submit' class='btn btn-danger'>Hapus</button></form></td></tr>";
                        $no++;
                    } else {

                        $content = file_get_contents('datagambar.json');
                        $content = utf8_encode($content);
                        $result = json_decode($content, true);

                        $json = [];
                        if (
                            $id && $result
                        ) {
                            foreach ($result as $val) {
                                if ($id != $val['id']) {
                                    $json[] = [
                                        'id' => $val['id'],
                                        'nama_file' => $val['nama_file']
                                    ];
                                }
                            }
                        }

                        $json_data =  json_encode($json);
                        file_put_contents('datagambar.json', $json_data);
                    }
                }
            } else {
                $no = 1;
                $result = [];
            }

            if ($no == 1) {
                echo "<tr><td colspan='6'><center><b>Tidak ditemukan</b></center></td></tr>";
            }
            ?>
        </tbody>
    </table>
    <div style="
    position: relative;
    margin-top: -13px;
    padding: 10px;
    background: #fff2f2;
"><i class="fa fa-warning"></i> Drag & Drop untuk pengurutan</div>


    <br>
    <br>
    <form class="container mt-3" action="<?php echo url('/upload'); ?>" method="POST" enctype="multipart/form-data">
        @csrf
        <h1>
            <center>Upload File</center>
        </h1>
        <div class="custom-file">
            <input type="file" class="custom-file-input" accept="image/png, image/gif, image/jpeg" name='upload_file' id="customFile">
            <label class="custom-file-label" for="customFile">Choose file</label>
        </div>
        <br><br>
        <button type='submit' class='btn btn-primary'>Upload</button>
    </form>
    <br>
    <br>

    <br>

    <form class="container mt-3" action="{{ url('/pengaturan') }}" method="POST" enctype="multipart/form-data" id="idForm">

        <h1>
            <center>Create Barcode</center>
        </h1>
        <div class="container">
            <div class="row">
                <div class="col">
                    @csrf
                    <div class="form-group">
                        <label for="nama">Isi tulisan</label>
                        <input type="text" required class="form-control" id="nama" name="nama" placeholder="Isi tulisan">
                    </div>
                    <div class="form-group">
                        <label for="bgColor">Backgroud Color</label>
                        <input value="#FFFFFFFF" class="form-control" name="bgColor" required data-jscolor="{}">
                    </div>
                    <div class="form-group">
                        <label for="bodyColor">Foreground Color</label><br>
                        <input type="radio" checked onclick="gradient(this.value, 0)" value="true" name="singel">
                        Singel
                        <input type="radio" onclick="gradient(this.value, 0)" value="false" name="singel"> Gradient

                        <br><br>
                        <div id="result">
                            <input value="#000000" class="form-control" name="bodyColor" data-jscolor="{}">
                        </div>
                        <div id="result2" style="display: none;">
                            <input class="form-control" name="gradientColor1" data-jscolor="{}"><br>
                            <input class="form-control" name="gradientColor2" data-jscolor="{}"><br>
                            <input type="radio" checked value="linear" name="gradientType"> Liner
                            <input type="radio" value="radial" name="gradientType"> Radial

                        </div>
                        <br>
                        <input type="checkbox" onclick="eyecolor(this.value)" id="gradientOnEyes" name="gradientOnEyes" value="true"> Custom Eye Color

                        <div id="result3" style="display: none;">
                            <br>
                            <input class="form-control" name="eye1Color" data-jscolor="{}"><br>
                            <input class="form-control" name="eyeBall1Color" data-jscolor="{}"><br>

                        </div>
                    </div>

                    <label for="body">Body Shape</label>
                    <div class="cc-selector">
                        <input id="square" type="radio" checked name="body" value="square" />
                        <label class="drinkcard-cc square" for="square"></label>

                        <input id="mosaic" type="radio" name="body" value="mosaic" />
                        <label class="drinkcard-cc mosaic" for="mosaic"></label>

                        <input id="dot" type="radio" name="body" value="dot" />
                        <label class="drinkcard-cc dot" for="dot"></label>

                        <input id="circle" type="radio" name="body" value="circle" />
                        <label class="drinkcard-cc circle" for="circle"></label>

                        <input id="circle-zebra" type="radio" name="body" value="circle-zebra" />
                        <label class="drinkcard-cc circle-zebra" for="circle-zebra"></label>

                        <input id="circle-zebra-vertical" type="radio" name="body" value="circle-zebra-vertical" />
                        <label class="drinkcard-cc circle-zebra-vertical" for="circle-zebra-vertical"></label>

                        <input id="circular" type="radio" name="body" value="circular" />
                        <label class="drinkcard-cc circular" for="circular"></label>

                        <input id="edge-cut" type="radio" name="body" value="edge-cut" />
                        <label class="drinkcard-cc edge-cut" for="edge-cut"></label>

                        <input id="edge-cut-smooth" type="radio" name="body" value="edge-cut-smooth" />
                        <label class="drinkcard-cc edge-cut-smooth" for="edge-cut-smooth"></label>

                        <input id="japnese" type="radio" name="body" value="japnese" />
                        <label class="drinkcard-cc japnese" for="japnese"></label>

                        <input id="leaf" type="radio" name="body" value="leaf" />
                        <label class="drinkcard-cc leaf" for="leaf"></label>

                        <input id="pointed" type="radio" name="body" value="pointed" />
                        <label class="drinkcard-cc pointed" for="pointed"></label>

                        <input id="pointed-edge-cut" type="radio" name="body" value="pointed-edge-cut" />
                        <label class="drinkcard-cc pointed-edge-cut" for="pointed-edge-cut"></label>

                        <input id="pointed-in" type="radio" name="body" value="pointed-in" />
                        <label class="drinkcard-cc pointed-in" for="pointed-in"></label>

                        <input id="pointed-in-smooth" type="radio" name="body" value="pointed-in-smooth" />
                        <label class="drinkcard-cc pointed-in-smooth" for="pointed-in-smooth"></label>

                        <input id="pointed-smooth" type="radio" name="body" value="pointed-smooth" />
                        <label class="drinkcard-cc pointed-smooth" for="pointed-smooth"></label>

                        <input id="round" type="radio" name="body" value="round" />
                        <label class="drinkcard-cc round" for="round"></label>

                        <input id="rounded-in" type="radio" name="body" value="rounded-in" />
                        <label class="drinkcard-cc rounded-in" for="rounded-in"></label>

                        <input id="rounded-in-smooth" type="radio" name="body" value="rounded-in-smooth" />
                        <label class="drinkcard-cc rounded-in-smooth" for="rounded-in-smooth"></label>

                        <input id="rounded-pointed" type="radio" name="body" value="rounded-pointed" />
                        <label class="drinkcard-cc rounded-pointed" for="rounded-pointed"></label>

                        <input id="star" type="radio" name="body" value="star" />
                        <label class="drinkcard-cc star" for="star"></label>

                        <input id="diamond" type="radio" name="body" value="diamond" />
                        <label class="drinkcard-cc diamond" for="diamond"></label>

                    </div>


                    <label for="body">Eye Frame Shape</label>
                    <div class="cc-selector">
                        <input id="frame0" type="radio" checked name="eye" value="frame0" />
                        <label class="drinkcard-cc frame0" for="frame0"></label>

                        <input id="frame1" type="radio" name="eye" value="frame1" />
                        <label class="drinkcard-cc frame1" for="frame1"></label>

                        <input id="frame2" type="radio" name="eye" value="frame2" />
                        <label class="drinkcard-cc frame2" for="frame2"></label>

                        <input id="frame3" type="radio" name="eye" value="frame3" />
                        <label class="drinkcard-cc frame3" for="frame3"></label>

                        <input id="frame4" type="radio" name="eye" value="frame4" />
                        <label class="drinkcard-cc frame4" for="frame4"></label>

                        <input id="frame5" type="radio" name="eye" value="frame5" />
                        <label class="drinkcard-cc frame5" for="frame5"></label>

                        <input id="frame6" type="radio" name="eye" value="frame6" />
                        <label class="drinkcard-cc frame6" for="frame6"></label>

                        <input id="frame7" type="radio" name="eye" value="frame7" />
                        <label class="drinkcard-cc frame7" for="frame7"></label>

                        <input id="frame8" type="radio" name="eye" value="frame8" />
                        <label class="drinkcard-cc frame8" for="frame8"></label>

                        <input id="frame9" type="radio" name="eye" value="frame9" />
                        <label class="drinkcard-cc frame9" for="frame9"></label>

                        <input id="frame10" type="radio" name="eye" value="frame10" />
                        <label class="drinkcard-cc frame10" for="frame10"></label>

                        <input id="frame11" type="radio" name="eye" value="frame11" />
                        <label class="drinkcard-cc frame11" for="frame11"></label>

                        <input id="frame12" type="radio" name="eye" value="frame12" />
                        <label class="drinkcard-cc frame12" for="frame12"></label>

                        <input id="frame13" type="radio" name="eye" value="frame13" />
                        <label class="drinkcard-cc frame13" for="frame13"></label>

                        <input id="frame14" type="radio" name="eye" value="frame14" />
                        <label class="drinkcard-cc frame14" for="frame14"></label>

                        <input id="frame15" type="radio" name="eye" value="frame15" />
                        <label class="drinkcard-cc frame15" for="frame15"></label>

                        <input id="frame16" type="radio" name="eye" value="frame16" />
                        <label class="drinkcard-cc frame16" for="frame16"></label>

                    </div>



                    <label for="body">Eye Ball Shape</label>
                    <div class="cc-selector">
                        <input id="ball0" type="radio" checked name="eyeBall" value="ball0" />
                        <label class="drinkcard-cc ball0" for="ball0"></label>

                        <input id="ball1" type="radio" name="eyeBall" value="ball1" />
                        <label class="drinkcard-cc ball1" for="ball1"></label>

                        <input id="ball2" type="radio" name="eyeBall" value="ball2" />
                        <label class="drinkcard-cc ball2" for="ball2"></label>

                        <input id="ball3" type="radio" name="eyeBall" value="ball3" />
                        <label class="drinkcard-cc ball3" for="ball3"></label>

                        <input id="ball4" type="radio" name="eyeBall" value="ball4" />
                        <label class="drinkcard-cc ball4" for="ball4"></label>

                        <input id="ball5" type="radio" name="eyeBall" value="ball5" />
                        <label class="drinkcard-cc ball5" for="ball5"></label>

                        <input id="ball6" type="radio" name="eyeBall" value="ball6" />
                        <label class="drinkcard-cc ball6" for="ball6"></label>

                        <input id="ball7" type="radio" name="eyeBall" value="ball7" />
                        <label class="drinkcard-cc ball7" for="ball7"></label>

                        <input id="ball8" type="radio" name="eyeBall" value="ball8" />
                        <label class="drinkcard-cc ball8" for="ball8"></label>

                        <input id="ball9" type="radio" name="eyeBall" value="ball9" />
                        <label class="drinkcard-cc ball9" for="ball9"></label>

                        <input id="ball10" type="radio" name="eyeBall" value="ball10" />
                        <label class="drinkcard-cc ball10" for="ball10"></label>

                        <input id="ball11" type="radio" name="eyeBall" value="ball11" />
                        <label class="drinkcard-cc ball11" for="ball11"></label>

                        <input id="ball12" type="radio" name="eyeBall" value="ball12" />
                        <label class="drinkcard-cc ball12" for="ball12"></label>

                        <input id="ball13" type="radio" name="eyeBall" value="ball13" />
                        <label class="drinkcard-cc ball13" for="ball13"></label>

                        <input id="ball14" type="radio" name="eyeBall" value="ball14" />
                        <label class="drinkcard-cc ball14" for="ball14"></label>

                        <input id="ball15" type="radio" name="eyeBall" value="ball15" />
                        <label class="drinkcard-cc ball15" for="ball15"></label>

                        <input id="ball16" type="radio" name="eyeBall" value="ball16" />
                        <label class="drinkcard-cc ball16" for="ball16"></label>



                    </div>



                    <label for="nama">Logo</label>
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" accept="image/png, image/gif, image/jpeg" name='logo' id="customFile">
                        <label class="custom-file-label" for="customFile">Choose file</label>
                    </div>
                </div>
                <div class="col">
                    <center>
                        <div id="img">
                            <img src="https://www.qrcode-monkey.com/img/default-preview-qr.svg" class="img-fluid" width="300">

                        </div>
                        <button type='submit' class='btn btn-primary'>Buat QR Code</button><br>
                    </center>
                </div>
            </div>
        </div>
    </form>



    <script src="{{ url('/') }}/assets/js/jscolor.min.js"></script>
    <script>
        function gradient(val) {
            if (val == 'true') {
                document.getElementById("result2").style.display = "none";
                document.getElementById("result").style.display = "block";
            } else {
                document.getElementById("result").style.display = "none";
                document.getElementById("result2").style.display = "block";
            }

        }

        function eyecolor(val) {
            var x = document.getElementById("gradientOnEyes").checked;
            if (x) {
                document.getElementById("result3").style.display = "block";
            } else {
                document.getElementById("result3").style.display = "none";
            }

        }

        $("#idForm").submit(function(e) {

            e.preventDefault(); // avoid to execute the actual submit of the form.

            var form = $(this);
            var actionUrl = form.attr('action');
            var formData = new FormData(this);

            $.ajax({
                type: "POST",
                url: actionUrl,
                dataType: "json",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(data) {
                    console.log(data);
                    document.getElementById("img").innerHTML = '<img src="' + data.url +
                        '" class="img-fluid" width="300">';
                }
            });

        });
    </script>
    <style>
        .list-group-item {
            position: unset;
            display: revert;
        }

        .glyphicon-move {
            cursor: move;
            cursor: -webkit-grabbing;
        }

        label {
            font-weight: bolder;
        }

        .cc-selector input {
            margin: 0;
            padding: 0;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .square {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/square.png);
        }

        .mosaic {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/mosaic.png);
        }

        .dot {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/dot.png);
        }

        .circle {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/circle.png);
        }

        .circle-zebra {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/circle-zebra.png);
        }

        .circle-zebra-vertical {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/circle-zebra-vertical.png);
        }

        .circular {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/circular.png);
        }

        .edge-cut {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/edge-cut.png);
        }

        .edge-cut-smooth {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/edge-cut-smooth.png);
        }

        .japnese {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/japnese.png);
        }

        .leaf {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/leaf.png);
        }

        .pointed {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/pointed.png);
        }

        .pointed-in {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/pointed-in.png);
        }

        .pointed-in-smooth {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/pointed-in-smooth.png);
        }

        .pointed-edge-cut {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/pointed-edge-cut.png);
        }

        .pointed-smooth {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/pointed-smooth.png);
        }

        .round {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/round.png);
        }

        .rounded-in {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/rounded-in.png);
        }

        .rounded-in-smooth {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/rounded-in-smooth.png);
        }

        .rounded-pointed {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/rounded-pointed.png);
        }

        .star {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/star.png);
        }

        .diamond {
            background-image: url(https://www.qrcode-monkey.com/img/qr/body/diamond.png);
        }




        .frame0 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame0.png);
        }

        .frame1 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame1.png);
        }

        .frame2 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame2.png);
        }

        .frame3 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame3.png);
        }

        .frame4 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame4.png);
        }

        .frame5 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame5.png);
        }

        .frame6 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame6.png);
        }

        .frame7 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame7.png);
        }

        .frame8 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame8.png);
        }

        .frame9 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame9.png);
        }

        .frame10 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame10.png);
        }

        .frame11 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame11.png);
        }

        .frame12 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame12.png);
        }

        .frame13 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame13.png);
        }

        .frame14 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame14.png);
        }

        .frame15 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame15.png);
        }

        .frame16 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-frames/frame16.png);
        }




        .ball0 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball0.png);
        }

        .ball1 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball1.png);
        }

        .ball2 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball2.png);
        }

        .ball3 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball3.png);
        }

        .ball4 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball4.png);
        }

        .ball5 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball5.png);
        }

        .ball6 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball6.png);
        }

        .ball7 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball7.png);
        }

        .ball8 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball8.png);
        }

        .ball9 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball9.png);
        }

        .ball10 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball10.png);
        }

        .ball11 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball11.png);
        }

        .ball12 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball12.png);
        }

        .ball13 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball13.png);
        }

        .ball14 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball14.png);
        }

        .ball15 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball15.png);
        }

        .ball16 {
            background-image: url(https://www.qrcode-monkey.com/img/qr/eye-balls/ball16.png);
        }

        .cc-selector input:active+.drinkcard-cc {
            opacity: .9;
        }

        .cc-selector input:checked+.drinkcard-cc {
            -webkit-filter: none;
            -moz-filter: none;
            filter: none;
        }

        .drinkcard-cc {
            cursor: pointer;
            background-size: contain;
            background-repeat: no-repeat;
            display: inline-block;
            width: 100px;
            height: 70px;
            -webkit-transition: all 100ms ease-in;
            -moz-transition: all 100ms ease-in;
            transition: all 100ms ease-in;
            -webkit-filter: brightness(1.8) grayscale(1) opacity(.7);
            -moz-filter: brightness(1.8) grayscale(1) opacity(.7);
            filter: brightness(1.8) grayscale(1) opacity(.7);
        }

        .drinkcard-cc:hover {
            -webkit-filter: brightness(1.2) grayscale(.5) opacity(.9);
            -moz-filter: brightness(1.2) grayscale(.5) opacity(.9);
            filter: brightness(1.2) grayscale(.5) opacity(.9);
        }
    </style>
    <script>
        // Add the following code if you want the name of the file appear on select
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });

        var data = <?= json_encode($result) ?>;

        // List with handle
        Sortable.create(listWithHandle, {
            handle: '.move',
            animation: 150,
            group: "data",
            store: {
                get: function(sortable) {
                    // var order = localStorage.getItem(sortable.options.group.name);
                    // return order ? order.split('|') : [];
                },
                set: function(sortable) {
                    var order = sortable.toArray();
                    $.ajax({
                        url: "{{ url('/posisi') }}",
                        type: "POST",
                        data: {
                            "_token": "{{ csrf_token() }}",
                            data: order
                        },
                        success: function(response) {
                            if (response == 'success') {
                                location.reload();
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            console.log(textStatus, errorThrown);
                        }
                    });
                    // localStorage.setItem(sortable.options.group.name, order.join('|'));
                }
            }

        });
    </script>
    <a href="<?= url('/logout') ?>">
        <button style='position:fixed;top:0;right:0;border-radius: 0px 0px 0px 30px;width: 100px;' class='btn btn-danger'>Logout</button>
    </a>

</body>