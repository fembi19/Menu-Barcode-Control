<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use nguyenary\QRCodeMonkey\QRCode;

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
    $gradientColor1 = request()->input('gradientColor1');
    $gradientColor2 = request()->input('gradientColor2');

    if ($gradientColor1) {
        $gradientOnEyes = true;
    } else {
        $gradientOnEyes = false;
    }
    $gradientType = request()->input('gradientType');


    $eyeBall = request()->input('eyeBall');
    $qrcode = new QRCode("$nama");

    $qrcode->setConfig([
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
        'eye1Color' => '#000000',
        'eye2Color' => '#000000',
        'eye3Color' => '#000000',
        'eyeBall1Color' => '#000000',
        'eyeBall2Color' => '#000000',
        'eyeBall3Color' => '#000000',

        'bodyColor' => $bodyColor,
        'gradientColor1' => $gradientColor1,
        'gradientColor2' => $gradientColor2,
        'gradientOnEyes' => $gradientOnEyes,
        'gradientType' => 'linear',
    ]);

    // $qrcode->setLogo('http://localhost/menu_laravel/system/public/assets/pages/d.jpg', "clean");

    $qrcode->setFileType('png');
    $data['url'] = $qrcode->create();
    return view('pengaturan', $data);
});



Route::get('/hapus', function () {

    $file = request()->input('url');
    if ($file) {
        if (!unlink($file)) {
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
                ->with('success', 'Berhasil Terhapus');
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

    if ($request->hasFile('upload_file')) {
        $logoImage = $request->file('upload_file');
        $name = $logoImage->getClientOriginalName();
    } else {
        return back()
            ->with('success', 'You have successfully upload image.');
    }


    if ($request->hasFile('upload_file')) {

        $img = $request->file('upload_file');
        $name = $img->getClientOriginalName();

        $path = public_path('assets/pages/');

        $fileName = uniqid() . '_' . trim($name);

        $img->move($path, $fileName);


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
            ->with('success', 'Gambar Berhasil Terupload');
    } else {
        return ('pengaturan');
    }
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
