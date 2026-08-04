<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Registration;

class UpdateRegistrationNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'registrations:update-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update existing IRCC25 registration numbers to IRCC26 format';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating registration numbers from IRCC25 to IRCC26...');
        
        $registrations = Registration::where('registration_number', 'LIKE', 'IRCC25%')->get();
        
        if ($registrations->isEmpty()) {
            $this->info('No registrations with IRCC25 prefix found.');
            return 0;
        }
        
        $updated = 0;
        
        foreach ($registrations as $registration) {
            $oldNumber = $registration->registration_number;
            $newNumber = str_replace('IRCC25', 'IRCC26', $oldNumber);
            
            $registration->update(['registration_number' => $newNumber]);
            $updated++;
            
            $this->line("Updated: {$oldNumber} -> {$newNumber}");
        }
        
        $this->info("Successfully updated {$updated} registration number(s).");
        
        return 0;
    }
}
