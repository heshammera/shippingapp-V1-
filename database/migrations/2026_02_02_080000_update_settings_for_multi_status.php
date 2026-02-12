<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // We will store arrays as JSON in the 'value' column of 'settings' table, 
        // so no schema change needed for settings table itself if it uses key-value standard.
        // But we to should update the seeding/default values.
        
        // However, looking at Setting model structure is safer first. 
        // Assuming key-value structure: key (string), value (text/json).
    }

    public function down(): void
    {
        //
    }
};
