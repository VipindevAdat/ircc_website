<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('program_schedule_settings', function (Blueprint $table) {
            $table->id();
            $table->string('conference_title')->nullable();
            $table->string('venue')->nullable();
            $table->string('event_dates')->nullable();
            $table->text('disclaimer')->nullable();
            $table->timestamps();
        });

        Schema::create('program_schedule_days', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('is_visible')->default(true);
            $table->boolean('is_virtual')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        Schema::create('program_schedule_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_schedule_day_id')->constrained()->cascadeOnDelete();
            $table->string('time_slot');
            $table->string('title');
            $table->text('details')->nullable();
            $table->string('venue')->nullable();
            $table->unsignedInteger('sort_order')->default(1);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        $now = now();

        DB::table('program_schedule_settings')->insert([
            'conference_title' => 'International Research Conference for Children-2026 (IRCC-2026)',
            'venue' => 'India Community Center, Milpitas, CA, USA.',
            'event_dates' => 'June 6-7, 2026',
            'disclaimer' => 'The Program Schedule is tentative and subject to change based on the availability and scheduling of Keynote Speakers. The final confirmed schedule will be published within 72 hours of the IRCC Event on June 6, 2026. Please check back for updates.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $days = [
            [
                'title' => 'Day I (Virtual Zoom) - June 6, 2026',
                'sort_order' => 1,
                'is_visible' => false,
                'is_virtual' => true,
                'items' => [
                    ['5:30 pm – 6:00 pm', 'Opening Remarks', null, 'VIRTUAL', 1],
                    ['6:00 pm – 7:00 pm', 'Keynote address I', '<div class="speaker-info"><div class="speaker-name">Speaker-1: Dr. Irimpan Mathews</div><div class="speaker-details">Stanford University, USA.</div><div class="speaker-title">Title: TBD</div><div class="chair-info"><strong>Chair:</strong> Adv. Dr. Renju Joseph (GYRA India)<br><strong>Co-chair:</strong> GYRA Scholar (India)</div></div>', 'VIRTUAL', 2],
                    ['7:00 pm – 10:00 pm', 'Students Presentations', null, 'VIRTUAL', 3],
                    ['10:00 pm – 10:15 pm', 'Opening Remarks', null, 'VIRTUAL', 4],
                ],
            ],
            [
                'title' => 'Saturday - June 6, 2026',
                'sort_order' => 2,
                'is_visible' => true,
                'is_virtual' => false,
                'items' => [
                    ['8:00 am – 8:30 am', 'Registration', null, 'Registration desk', 1],
                    ['8:30 am – 9:30 am', 'Inauguration', null, 'VENUE-I', 2],
                    ['9:30 am – 10:30 am', 'Keynote address II', '<div class="speaker-info"><div class="speaker-name">Speaker-2: Prof. Devendra K Agrawal, PhD (Biochem), PhD (Med Sciences), MBA, MS (ITM), FAAAAI, FAHA, FAPS, FIACS</div><div class="speaker-details">Western University of Health Sciences, Pomona, Los Angeles, USA.</div><div class="speaker-title">Title: Concept of Translational Research: Bench to Bedside</div><div class="chair-info"><strong>Chair:</strong> Dr. Vipindev Adat<br><strong>Co-chair:</strong> GYRA Scholar 1</div></div>', 'VENUE-I', 3],
                    ['10:30 am – 11:00 am', 'Refreshment', null, 'Lobby', 4],
                    ['11:00 am – 12:00 pm', 'Plenary Talk I', '<div class="speaker-info"><div class="speaker-name">Speaker-2:</div><div class="speaker-title">Title: TBD</div><div class="chair-info"><strong>Chair:</strong> Dr. Sini Sunny<br><strong>Co-chair:</strong> GYRA Scholar-2</div></div>', 'VENUE-I', 5],
                    ['12:00 pm – 1:00 pm', 'Lunch', null, 'Lobby', 6],
                    ['1:00 pm – 2:00 pm', 'Keynote address III', '<div class="speaker-info"><div class="speaker-name">Speaker-3: TBD</div><div class="speaker-details">David Geffen School of Medicine and UCLA Health</div><div class="speaker-title">Title: TBD</div><div class="chair-info"><strong>Chair:</strong> Dr. Irimpan Mathews<br><strong>Co-chair:</strong> GYRA Scholar-3</div></div>', 'VENUE-I', 7],
                    ['2:00 pm – 3:30 pm', 'Panel Discussion', '<div class="speaker-info"><div class="speaker-title"><strong>Theme:</strong> Failures: The Steppingstones for Scientists</div><div class="chair-info" style="margin-top: 10px;"><strong>Moderator:</strong> Ms. Devi Gireesh<br><br><strong>Panellists:</strong><br>• Prof. Devendra Agrawal<br>• GYRA Scholar<br>• Parent Representative<br>• Teacher Representative<br>• Dr. Vipindev Adat, MIT, USA (GYRA)</div></div>', 'VENUE-I', 8],
                    ['3:30 pm – 5:30 pm', "Students' Presentations and Refreshment", null, "Venue – I<br>Venue – II<br>Venue – III<br>Venue – IV", 9],
                    ['5:30 pm – 7:00 pm', 'Distinguished Keynote', '<div class="speaker-info"><div class="speaker-name">Speaker-4: Prof. Brian Kobilka, MD</div><div class="speaker-details">2012 Nobel Laureate in Chemistry<br>Stanford University School of Medicine</div><div class="speaker-title">Title: TBD</div><div class="chair-info"><strong>Chair:</strong> Prof. Devendra Agrawal<br><strong>Co-chair:</strong> GYRA Scholar-4</div></div>', 'VENUE-I', 10],
                    ['7:00 pm – 9:00 pm', 'Dinner + Cultural Events', null, 'VENUE-I', 11],
                ],
            ],
            [
                'title' => 'Sunday - June 7, 2026',
                'sort_order' => 3,
                'is_visible' => true,
                'is_virtual' => false,
                'items' => [
                    ['8:30 am – 10:00 am', 'Keynote address IV', '<div class="speaker-info"><div class="speaker-name">Speaker-5: Prof. Stephen Shore, PhD</div><div class="speaker-name" style="margin-top: 5px;">Speaker-6: Prof. Pavan Antony, PhD</div><div class="speaker-title">Title: TBD</div><div class="chair-info"><strong>Chair:</strong> Ms. Padmapriya<br><strong>Co-chair:</strong> GYRA Scholar-5</div></div>', 'VENUE-I', 1],
                    ['10:00 am – 10:30 am', 'Refreshment', null, 'Lobby', 2],
                    ['10:30 am – 11:30 am', 'Keynote address V', '<div class="speaker-info"><div class="speaker-name">Speaker-7: TBD</div><div class="speaker-title">Title: TBD</div><div class="chair-info"><strong>Chair:</strong> Ms. Divya<br><strong>Co-chair:</strong> GYRA Scholar-6</div></div>', 'VENUE-I', 3],
                    ['11:30 am – 12:00 pm', 'Refreshment', null, 'Lobby', 4],
                    ['12:00 pm – 1:00 pm', 'Lunch Break', null, 'Lobby', 5],
                    ['12:30 pm – 1:00 pm', 'Featured Talk I', '<div class="speaker-info"><div class="speaker-name">Speaker-8: Preethi Pinto</div><div class="speaker-details">Stem4Girls</div><div class="speaker-title">Title: TBD</div></div>', 'VENUE-I', 6],
                    ['2:00 pm – 3:00 pm', 'Plenary Talk II', '<div class="speaker-info"><div class="speaker-name">Speaker-9:</div><div class="speaker-title">Title: TBD</div><div class="chair-info"><strong>Chair:</strong> Dr. Resmi Rajalekshmi<br><strong>Co-chair:</strong> GYRA Scholar-7</div></div>', 'VENUE-I', 7],
                    ['3:00 pm – 4:00 pm', 'Validatory Function and Certificate distribution', null, 'VENUE-I', 8],
                    ['4:00 pm – 5:00 pm', 'Refreshment', null, 'Lobby', 9],
                ],
            ],
        ];

        foreach ($days as $day) {
            $dayId = DB::table('program_schedule_days')->insertGetId([
                'title' => $day['title'],
                'sort_order' => $day['sort_order'],
                'is_visible' => $day['is_visible'],
                'is_virtual' => $day['is_virtual'],
                'status' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            foreach ($day['items'] as $item) {
                DB::table('program_schedule_items')->insert([
                    'program_schedule_day_id' => $dayId,
                    'time_slot' => $item[0],
                    'title' => $item[1],
                    'details' => $item[2],
                    'venue' => $item[3],
                    'sort_order' => $item[4],
                    'status' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('program_schedule_items');
        Schema::dropIfExists('program_schedule_days');
        Schema::dropIfExists('program_schedule_settings');
    }
};
