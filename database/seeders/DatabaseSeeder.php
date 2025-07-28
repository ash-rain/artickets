<?php

namespace Database\Seeders;

use App\Models\Venue;
use App\Models\Section;
use App\Models\Seat;
use App\Models\Event;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create venues
        $symphony = Venue::create([
            'name' => 'Symphony Hall',
            'address' => '123 Music Street',
        ]);

        // Create sections for Symphony Hall
        $orchestra = Section::create([
            'venue_id' => $symphony->id,
            'name' => 'Front Orchestra',
            'price' => 100,
        ]);

        $standing = Section::create([
            'venue_id' => $symphony->id,
            'name' => 'Standing',
            'price' => 60,
        ]);

        $balcony = Section::create([
            'venue_id' => $symphony->id,
            'name' => 'Balcony',
            'price' => 75,
        ]);

        // Create seats for Front Orchestra section
        foreach (range(1, 3) as $row) {
            foreach (range(1, 10) as $column) {
                Seat::create([
                    'section_id' => $orchestra->id,
                    'row' => $row,
                    'column' => $column,
                ]);
            }
        }


        // Create seats for Standing section
        foreach (range(4, 7) as $row) {
            foreach (range(1, 10) as $column) {
                Seat::create([
                    'section_id' => $standing->id,
                    'row' => $row,
                    'column' => $column,
                ]);
            }
        }

        // Create seats for Balcony section
        foreach (range(8, 10) as $row) {
            foreach (range(1, 10) as $column) {
                Seat::create([
                    'section_id' => $balcony->id,
                    'row' => $row,
                    'column' => $column,
                ]);
            }
        }

        // Create events
        Event::create([
            'venue_id' => $symphony->id,
            'title' => 'Ozric Tentacles Live',
            'description' => 'Featuring contemporary artists',
            'image' => '/storage/tickets/ticket003.jpg',
            'date' => now()->addDays(7), // 7 days from now
        ]);
    }
}
