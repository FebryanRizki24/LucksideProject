<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hairstyle;

class HairstyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hairstyles = [
            [
                'name' => 'Pompadour',
                'deskripsi' => 'Gaya rambut dengan volume tinggi di bagian atas, memberikan kesan wajah lebih panjang dan elegan.'
            ],
            [
                'name' => 'Undercut',
                'deskripsi' => 'Sisi dan belakang dipotong pendek atau dicukur, sementara bagian atas dibiarkan panjang. Cocok untuk tampilan modern dan tegas.'
            ],
            [
                'name' => 'Buzz Cut',
                'deskripsi' => 'Potongan rambut sangat pendek yang memberikan tampilan maskulin dan rapi, cocok untuk wajah dengan fitur tegas.'
            ],
            [
                'name' => 'Crew Cut',
                'deskripsi' => 'Gaya rambut pendek dengan bagian atas sedikit lebih panjang dari sisi, memberikan kesan bersih dan profesional.'
            ],
            [
                'name' => 'Quiff',
                'deskripsi' => 'Mirip dengan pompadour tetapi lebih santai dan bertekstur, cocok untuk menambah dimensi pada wajah bulat atau kotak.'
            ],
            [
                'name' => 'Side Part',
                'deskripsi' => 'Potongan klasik dengan belahan samping, memberikan tampilan elegan dan cocok untuk hampir semua bentuk wajah.'
            ],
            [
                'name' => 'French Crop',
                'deskripsi' => 'Bagian atas bertekstur dengan poni pendek, memberikan tampilan kasual dan mudah dirawat.'
            ],
            [
                'name' => 'Textured Fringe',
                'deskripsi' => 'Poni yang lebih panjang dengan tekstur alami, cocok untuk menyeimbangkan wajah panjang atau hati.'
            ],
            [
                'name' => 'Slick Back',
                'deskripsi' => 'Rambut ditata ke belakang dengan pomade atau gel, menciptakan tampilan klasik yang rapi dan profesional.'
            ],
        ];

        foreach ($hairstyles as $hairstyle) {
            Hairstyle::create($hairstyle);
        }
    }
}
