<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\CareerPath;
class CareerPathSeeder extends Seeder {
    public function run(): void {
        CareerPath::truncate();
        $data = json_decode(file_get_contents(__DIR__ . '/careers_data.json'), true);
        foreach ($data as $c) { CareerPath::create($c); }
    }
}
