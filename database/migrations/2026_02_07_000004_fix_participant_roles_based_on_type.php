<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Participant;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix participant roles based on type field
        // Based on the old system:
        // - type = 0 or null = Student Participant
        // - type = 1 = Guest/Adult Attendee (in old system, but might have been used for both)
        // - type = 2 = Deleted
        
        // Update participants that don't have a role set
        // If type = 1 and role is null, set role = 'adult' (assuming type 1 was used for guests/adults)
        // If type = 0 or null and role is null, set role = 'student'
        
        // First, update participants with type = 1 to be adults (if they don't have role set)
        Participant::whereNull('role')
            ->where('type', 1)
            ->where('type', '<>', 2) // Not deleted
            ->update(['role' => 'adult']);
        
        // Then, update participants with type = 0 or null to be students (if they don't have role set)
        Participant::whereNull('role')
            ->where(function($query) {
                $query->where('type', 0)
                      ->orWhereNull('type');
            })
            ->where('type', '<>', 2) // Not deleted
            ->update(['role' => 'student']);
        
        // Also update any participants that have role = null but type is set
        // This is a catch-all for any edge cases
        Participant::whereNull('role')
            ->where('type', '<>', 2) // Not deleted
            ->update(['role' => 'student']); // Default to student if unclear
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration doesn't need a down method as it's just fixing data
        // We don't want to remove the role field
    }
};
