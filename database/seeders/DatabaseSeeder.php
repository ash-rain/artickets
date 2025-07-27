<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Venue;
use App\Models\Section;
use App\Models\Seat;
use App\Models\Event;
use App\Models\Payment;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $venue = Venue::create(['name' => 'Concert Hall']);

        $sections = [];
        for ($i = 1; $i <= 3; $i++) {
            $sections[] = Section::create([
                'venue_id' => $venue->id,
                'name' => "Section $i",
                'price' => 100 - ($i * 20) // Decreasing prices: 80, 60, 40
            ]);
        }

        $event = Event::create([
            'venue_id' => $venue->id,
            'title' => 'Summer Concert'
        ]);

        foreach ($sections as $section) {
            for ($row = 1; $row <= 2; $row++) {
                for ($col = 1; $col <= 10; $col++) {
                    Seat::create([
                        'section_id' => $section->id,
                        'row' => $row,
                        'column' => $col
                    ]);
                }
            }
        }

        // Create a sample payment for demonstration
        $seat = Seat::first();
        Payment::create([
            'seat_id' => $seat->id,
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890'
        ]);
    }
}
