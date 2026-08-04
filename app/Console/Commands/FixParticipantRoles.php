<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Participant;
use App\Models\Registration;
use Illuminate\Support\Facades\DB;

class FixParticipantRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'participants:fix-roles {--registration-id= : Specific registration ID to fix} {--list : List all participants} {--set-adult= : Set a specific participant ID to adult role} {--fix-registered-users : Automatically set registered users as adults}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix participant roles based on type field. Use --list to see all participants, --set-adult=ID to set a participant as adult, --fix-registered-users to auto-fix registered users.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Handle --fix-registered-users option
        if ($this->option('fix-registered-users')) {
            $this->info("🔍 Finding and fixing registered users in participants table...\n");
            
            $participants = Participant::where('type', '<>', 2)->get();
            $registrations = Registration::all();
            
            $fixed = 0;
            $notFound = 0;
            
            foreach ($registrations as $registration) {
                $userFullName = strtolower(trim($registration->first_name . ' ' . $registration->last_name));
                $userEmail = strtolower(trim($registration->email ?? ''));
                
                $found = false;
                foreach ($participants as $participant) {
                    if ($participant->registration_id == $registration->id) {
                        $participantFullName = strtolower(trim($participant->first_name . ' ' . $participant->last_name));
                        $participantEmail = strtolower(trim($participant->email ?? ''));
                        
                        // Check if names match
                        if ($participantFullName === $userFullName || 
                            ($participantEmail && $userEmail && $participantEmail === $userEmail)) {
                            // This participant is the registered user - set to adult
                            if ($participant->role !== 'adult') {
                                try {
                                    $participant->update([
                                        'role' => 'adult',
                                        'type' => 1
                                    ]);
                                    $fixed++;
                                    $this->info("✅ Fixed: Participant ID {$participant->id} ({$participant->first_name} {$participant->last_name}) - Set to ADULT (Registered User: {$registration->first_name} {$registration->last_name})");
                                    $found = true;
                                } catch (\Exception $e) {
                                    $this->error("❌ Failed to update participant ID {$participant->id}: " . $e->getMessage());
                                }
                            } else {
                                $this->line("✓ Participant ID {$participant->id} ({$participant->first_name} {$participant->last_name}) already set as ADULT");
                                $found = true;
                            }
                        }
                    }
                }
                
                if (!$found) {
                    $notFound++;
                }
            }
            
            $this->info("\n📊 Summary:");
            $this->info("Fixed: {$fixed}");
            $this->info("Already correct: " . ($registrations->count() - $fixed - $notFound));
            $this->info("Registered users not in participants: {$notFound}");
            
            return 0;
        }
        
        // Handle --set-adult option
        if ($adultId = $this->option('set-adult')) {
            $participant = Participant::find($adultId);
            if (!$participant) {
                $this->error("Participant with ID {$adultId} not found!");
                return 1;
            }
            
            try {
                $participant->update([
                    'role' => 'adult',
                    'type' => 1
                ]);
                $this->info("✅ Successfully set participant ID {$adultId} ({$participant->first_name} {$participant->last_name}) to ADULT role.");
                return 0;
            } catch (\Exception $e) {
                $this->error("Failed to update participant: " . $e->getMessage());
                return 1;
            }
        }
        
        // Handle --list option
        if ($this->option('list')) {
            $registrationId = $this->option('registration-id');
            
            $query = Participant::where('type', '<>', 2)->with('registration:id,first_name,last_name,email');
            if ($registrationId) {
                $query->where('registration_id', $registrationId);
            }
            
            $participants = $query->orderBy('registration_id')->orderBy('id')->get();
            
            $this->info("\n📋 Participant List:\n");
            $this->table(
                ['ID', 'Registration ID', 'Name', 'Email', 'Age', 'Role', 'Type'],
                $participants->map(function($p) {
                    return [
                        $p->id,
                        $p->registration_id,
                        "{$p->first_name} {$p->last_name}",
                        $p->email ?? 'N/A',
                        $p->age ?? 'N/A',
                        $p->role ?? 'NULL',
                        $p->type ?? 'NULL'
                    ];
                })->toArray()
            );
            
            $this->info("\n💡 To set a participant as adult, run:");
            $this->info("   php artisan participants:fix-roles --set-adult=[ID]");
            
            return 0;
        }
        
        // Default behavior: fix roles
        $registrationId = $this->option('registration-id');
        
        $query = Participant::where('type', '<>', 2); // Exclude deleted
        if ($registrationId) {
            $query->where('registration_id', $registrationId);
        }
        
        $participants = $query->get();
        
        $this->info("Found {$participants->count()} participants to process.");
        
        $updated = 0;
        $skipped = 0;
        
        foreach ($participants as $participant) {
            if (empty($participant->role) || $participant->role === null) {
                // Determine role based on type field
                $role = ($participant->type == 1) ? 'adult' : 'student';
                
                try {
                    $participant->update(['role' => $role]);
                    $updated++;
                    $this->line("Updated participant ID {$participant->id} (Registration: {$participant->registration_id}) to role: {$role} (type was: {$participant->type})");
                } catch (\Exception $e) {
                    $skipped++;
                    $this->error("Failed to update participant ID {$participant->id}: " . $e->getMessage());
                }
            } else {
                $skipped++;
                $this->line("Skipped participant ID {$participant->id} - already has role: {$participant->role}");
            }
        }
        
        $this->info("\nSummary:");
        $this->info("Updated: {$updated}");
        $this->info("Skipped: {$skipped}");
        
        // Show statistics
        $stats = DB::table('participants')
            ->where('type', '<>', 2);
        
        if ($registrationId) {
            $stats->where('registration_id', $registrationId);
        }
        
        $stats = $stats->selectRaw('COUNT(*) as total, SUM(CASE WHEN role = "student" THEN 1 ELSE 0 END) as students, SUM(CASE WHEN role = "adult" THEN 1 ELSE 0 END) as adults, SUM(CASE WHEN role IS NULL OR role = "" THEN 1 ELSE 0 END) as no_role')
            ->first();
        
        $this->info("\nCurrent Statistics:");
        $this->info("Total participants: {$stats->total}");
        $this->info("Students: {$stats->students}");
        $this->info("Adults: {$stats->adults}");
        $this->info("No role set: {$stats->no_role}");
        
        if ($stats->adults == 0 && $stats->total > 0) {
            $this->warn("\n⚠️  WARNING: No adults found!");
            $this->info("\n💡 To see all participants, run:");
            $this->info("   php artisan participants:fix-roles --list");
            $this->info("\n💡 To set a participant as adult, run:");
            $this->info("   php artisan participants:fix-roles --set-adult=[ID]");
            $this->info("\n   Or edit via dashboard: Go to 'Add Participants' page and click edit icon.");
        }
        
        return 0;
    }
}
