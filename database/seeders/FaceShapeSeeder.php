<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FaceShape;

class FaceShapeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faceShapes = [
            [
                'name' => 'Bulat',
                'deskripsi' => "Wajah bulat memiliki panjang dan lebar yang hampir sama, dengan garis rahang yang halus dan pipi yang lebih penuh.\n\nGaya rambut yang cocok:\n- Tambahkan volume di bagian atas untuk memberi kesan wajah lebih panjang.\n- Potong bagian samping lebih pendek untuk menciptakan ilusi wajah lebih tirus.\n- Gunakan tekstur atau gaya rambut berdiri untuk menambah dimensi."
            ],
            [
                'name' => 'Oval',
                'deskripsi' => "Wajah oval memiliki bentuk yang seimbang dengan dahi sedikit lebih lebar dibanding dagu dan rahang yang membulat.\n\nGaya rambut yang cocok:\n- Bisa menggunakan berbagai gaya karena bentuk wajah yang proporsional.\n- Mempertahankan keseimbangan tanpa terlalu banyak volume di atas atau samping.\n- Gaya yang lebih natural atau bertekstur akan tetap terlihat bagus."
            ],
            [
                'name' => 'Oblong',
                'deskripsi' => "Wajah oblong lebih panjang dibanding lebarnya, dengan dahi, pipi, dan rahang yang memiliki ukuran hampir sama.\n\nGaya rambut yang cocok:\n- Hindari rambut terlalu tinggi di atas agar wajah tidak terlihat semakin panjang.\n- Berikan sedikit volume di samping untuk menciptakan keseimbangan.\n- Bisa menggunakan poni atau gaya rambut yang lebih bervolume di bagian depan."
            ],
            [
                'name' => 'Hati',
                'deskripsi' => "Dahi lebih lebar dibanding rahang dan dagu yang meruncing, dengan tulang pipi yang sering menonjol.\n\nGaya rambut yang cocok:\n- Menyeimbangkan dahi yang lebar dengan volume lebih di bagian bawah atau samping.\n- Hindari potongan yang terlalu tinggi atau bervolume di atas agar dahi tidak terlalu dominan.\n- Gunakan gaya yang lebih natural dan bertekstur untuk menciptakan kesan proporsional."
            ],
            [
                'name' => 'Kotak',
                'deskripsi' => "Memiliki garis rahang yang tegas dengan dahi, pipi, dan rahang yang hampir sama lebarnya.\n\nGaya rambut yang cocok:\n- Pilih gaya yang bisa sedikit melembutkan bentuk wajah yang kaku.\n- Hindari potongan yang terlalu pendek dan rata karena bisa membuat wajah terlihat lebih keras.\n- Gunakan tekstur atau layer untuk menciptakan kesan yang lebih natural."
            ]
        ];

        foreach ($faceShapes as $faceShape) {
            FaceShape::create($faceShape);
        }
    }
}