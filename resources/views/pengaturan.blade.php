<?php ?>

<head>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <link rel="icon" type="image/png" href="/logo.png" />
    <title>Menu Barcode System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>


    @if ($message = Session::get('success'))
    <div class="alert alert-success alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        <strong>{{ $message }}</strong>
    </div>
    @endif


    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Gambar</th>
                <th scope="col">Ukuran</th>
                <th scope="col">Nama</th>
                <th scope="col">Enter Untuk Ubah Nama</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php

            $content = file_get_contents("datagambar.json");
            //mengubah standar encoding
            $content = utf8_encode($content);

            //mengubah data json menjadi data array asosiatif
            $result = json_decode($content, true);
            sort($result);
            $no = 1;

            function getPotongAngka($angka)
            {
                $angka = (int)$angka;
                $i_str = (string)$angka;
                $angkax = substr($i_str, 0, -3);
                return (int)$angkax;
            }

            foreach ($result as $value) {
                $nama_file = $value['nama_file'];
                $size = filesize("assets/pages/" . $nama_file);
                echo "
   							 <tr>
      						<th scope='row'> $no
                  </th>
	  						<td><a href='assets/pages/$nama_file' target='_blank'><img height='60px' src='assets/pages/$nama_file' ></a></td>
							  <td>
                " . getPotongAngka($size) . " Kb <a href='/compres/$nama_file'>compres</a>
                </td>
                <td><a href='assets/pages/$nama_file' target='_blank'>$nama_file</a></td>
	  						<td>
							   <form method='get' id='myForm1' action='" . url('/') . "/rename'>
							   <input class='form-control' type='text' placeholder='Enter untuk ubah' value='$nama_file' name='nama_file_ubah'>
							 <input type='hidden' name='folder' value='assets/pages/'> 
							 <input type='hidden' name='nama_file' value='$nama_file'> 
							  <button type='submit' id='btn-submit1' style='display:none;' class='btn btn-primary'>Ubah</button>
							  </form></td>
	  						<td>
							  
							 <form method='get'  id='myForm' action='" . url('/') . "/hapus'>
							 <input type='hidden' name='url' value='assets/pages/$nama_file'> 
							  <button type='submit'  id='btn-submit' class='btn btn-danger'>Hapus</button>
							  </form>

							  </td>
    						</tr>";
                $no++;
            }

            ?>
        </tbody>
    </table>



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
    <script>
        // Add the following code if you want the name of the file appear on select
        $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
        });
    </script>
    <a href="<?= url('/logout') ?>">
        <button style='position:fixed;top:0;right:0;border-radius: 0px 0px 0px 30px;width: 100px;' class='btn btn-danger'>Logout</button>
    </a>
</body>