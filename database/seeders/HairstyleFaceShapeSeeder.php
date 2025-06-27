<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hairstyle;
use App\Models\FaceShape;

class HairstyleFaceShapeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mappings = [
            'Bulat' => ['Pompadour', 'Undercut', 'Quiff', 'Side Part'],
            'Oval' => ['Buzz Cut', 'Crew Cut', 'Side Part', 'French Crop', 'Quiff'],
            'Oblong' => ['French Crop', 'Textured Fringe', 'Side Part'],
            'Hati' => ['Slick Back', 'Side Part', 'Crew Cut'],
            'Kotak' => ['Pompadour', 'Quiff', 'Textured Fringe'],
        ];

        foreach ($mappings as $faceShapeName => $hairstyleNames) {
            $faceShape = FaceShape::where('name', $faceShapeName)->first();
            if (!$faceShape) continue;

            foreach ($hairstyleNames as $hairstyleName) {
                $hairstyle = Hairstyle::where('name', $hairstyleName)->first();
                if (!$hairstyle) continue;

                $faceShape->hairstyles()->attach($hairstyle->id);
            }
        }
    }
}
