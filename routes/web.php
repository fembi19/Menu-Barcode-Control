<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use nguyenary\QRCodeMonkey\QRCode;
use Intervention\Image\Facades\Image;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/atur', function () {
    $usr = request()->input('username');
    $pass = request()->input('password');


    $sesusr = request()->session()->get('username');
    $sespass = request()->session()->get('password');

    if ($sesusr && $sespass) {
        return redirect('pengaturan');
    }

    if ($usr == "menuprs" && $pass == "menuprs") {

        request()->session()->put('username', $usr);
        request()->session()->put('password', $pass);

        return redirect('pengaturan');
    } else {
        if ($usr && $pass) {
            $data = [
                'errors' => "Username & Password Salah"
            ];
            return view('login', $data);
        } else {
            $data = [
                'errors' => ""
            ];
            return view('login', $data);
        }
    }
});


Route::get('/logout', function () {

    $sesusr = request()->session()->forget('username');
    $sespass = request()->session()->forget('password');

    return redirect('/atur');
});


Route::get('pengaturan', function () {


    $sesusr = request()->session()->get('username');
    $sespass = request()->session()->get('password');

    if (!$sesusr && !$sespass) {
        return redirect('atur');
    }

    return view('pengaturan');
});
Route::post('/pengaturan', function () {

    $nama = request()->input('nama');
    $bgColor = request()->input('bgColor');
    $bodyColor = request()->input('bodyColor');
    $body = request()->input('body');
    $eye = request()->input('eye');
    $singel = request()->input('singel');

    $gradientOnEyes = request()->input('gradientOnEyes');

    if ($singel == 'false') {
        $gradientColor1 = request()->input('gradientColor1');
        $gradientColor2 = request()->input('gradientColor2');
    } else {
        $gradientColor1 = '';
        $gradientColor2 = '';
    }

    if ($gradientOnEyes == 'true') {
        $goe = 'false';
        $eye1Color = request()->input('eye1Color');
        $eyeBall1Color = request()->input('eyeBall1Color');
    } else {
        if ($singel == 'true') {
            $goe = 'true';
            $eye1Color = $bodyColor;
            $eyeBall1Color = $bodyColor;
        } else {
            $goe = 'false';
            $eye1Color = '';
            $eyeBall1Color = '';
        }
    }

    $gradientType = request()->input('gradientType');


    $eyeBall = request()->input('eyeBall');
    $qrcode = new QRCode("$nama");

    $json = [
        'bgColor' => $bgColor,
        'body' => $body,

        'brf1' => [],
        'brf2' => [],
        'brf3' => [],

        'erf1' => [],
        'erf2' => [],
        'erf3' => [],

        'eye' => $eye,
        'eyeBall' => $eyeBall,

        'bodyColor' => $bodyColor,
        'gradientColor1' => $gradientColor1,
        'gradientColor2' => $gradientColor2,
        'gradientOnEyes' => $goe,
        'gradientType' => $gradientType,

        'eye1Color' => $eye1Color,
        'eye2Color' => $eye1Color,
        'eye3Color' => $eye1Color,
        'eyeBall1Color' => $eyeBall1Color,
        'eyeBall2Color' => $eyeBall1Color,
        'eyeBall3Color' => $eyeBall1Color
    ];

    if (request()->hasFile('logo')) {
        $img = request()->file('logo');
        $name = $img->getClientOriginalName();

        $path = public_path('assets/temp');
        $fileName = uniqid() . '_' . trim($name);
        $img->move($path, $fileName);
        $url = $path . '/' . $fileName;
        $qrcode->setLogo($url, 'clean');
        $qrcode->setFileType('png');
    }
    $qrcode->setConfig($json);
    $data['url'] = $qrcode->create();
    $data['log'] = $json;

    echo json_encode($data);
});



Route::get('/hapus', function () {

    $file = request()->input('url');
    if ($file) {
        if (!unlink($file)) {
            echo ("Error deleting ");
        } else {
            $id = request()->input('id');

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
            if (file_put_contents('datagambar.json', $json_data)) {
                return back()
                    ->with('success', 'Berhasil Terhapus');
            };
        }
    }
});



Route::get('/rename', function () {

    $folder = request()->input('folder');
    $nama_file = request()->input('nama_file');


    $nama_file_ubah = request()->input('nama_file_ubah');

    $fileAwal = $folder . $nama_file;
    $fileBaru = $folder . $nama_file_ubah;


    if ($fileAwal && $fileBaru) {
        if (!rename($fileAwal, $fileBaru)) {
            echo ("Error deleting ");
        } else {

            $folder = "assets/pages/"; //Sesuaikan Folder nya
            if (!($buka_folder = opendir($folder))) die("eRorr... Tidak bisa membuka Folder");

            $file_array = array();
            while ($baca_folder = readdir($buka_folder)) {
                $file_array[] = array('nama' => $baca_folder);
            }

            $posts = array();

            $jumlah_array = count($file_array);
            for ($i = 2; $i < $jumlah_array; $i++) {
                $nama_file = $file_array[$i]['nama'];
                $posts[] = array('nama_file' => "$nama_file");
            }

            $json_data =  json_encode($posts);
            file_put_contents('datagambar.json', $json_data);

            return back()
                ->with('success', 'Berhasil Merubah Nama');
        }
    }
});


Route::post('/upload', function (Request $request) {

    $img = $request->file('upload_file');
    $ext = $img->getClientOriginalExtension();

    $path = 'assets/pages/';
    // $path = public_path('assets/pages/');

    $fileName = uniqid() . '.' . $ext;

    // $img->move($path, $fileName);

    Image::make($request->file('upload_file'))->resize(null, 2000, function ($constraint) {
        $constraint->aspectRatio();
    })->save($path . $fileName);

    app(Spatie\ImageOptimizer\OptimizerChain::class)->optimize($path . $fileName);

    $content = file_get_contents('datagambar.json');
    $content = utf8_encode($content);
    $result = json_decode($content, true);

    $json = [];
    if ($result) {
        $id = count($result) + 1;
        foreach ($result as $val) {
            $json[] = [
                'id' => $val['id'],
                'nama_file' => $val['nama_file']
            ];
        }
    } else {
        $id = 1;
    }

    $json[] = [
        'id' => $id,
        'nama_file' => $fileName
    ];

    $json_data =  json_encode($json);
    if (file_put_contents('datagambar.json', $json_data)) {
        return back()
            ->with('success', 'Gambar Berhasil Terupload');
    };
});


Route::post('/posisi', function (Request $request) {
    $data = $request->input('data');

    $content = file_get_contents('datagambar.json');
    $content = utf8_encode($content);
    $result = json_decode($content, true);

    $json = [];
    if ($data && $result) {
        foreach ($data as $key => $v) {

            foreach ($result as $val) {
                if ($v == $val['id']) {
                    $json[] = [
                        'id' => $key + 1,
                        'nama_file' => $val['nama_file']
                    ];
                }
            }
        }
    }

    $json_data =  json_encode($json);
    if (file_put_contents('datagambar.json', $json_data)) {
        echo 'success';
    };
});

Route::get('/mycomment', function () {

    return view('mycomment');
});
Route::post('/mycomment', function () {

    return view('mycomment');
});

Route::get('/datacomment', function () {

    return view('datacomment');
});

	// public function compres($id)
	// {
	// 	$image = \Config\Services::image()
	// 		->withFile('assets/pages/' . $id)
	// 		->save('assets/pages/' . $id, 10);
	// 	echo $image;
	// 	if ($image) {
	// 		return redirect()->to('menu');
	// 	} else {
	// 		return redirect()->to('menu');
	// 	}
	// }
