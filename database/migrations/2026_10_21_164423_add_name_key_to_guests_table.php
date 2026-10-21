<?php

use App\Models\Guest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->string('name_key', 125)->nullable()->after('last_name');
        });

        Guest::query()->orderBy('id')->each(function (Guest $guest): void {
            $guest->updateQuietly(['name_key' => Guest::nameKey($guest->first_name, $guest->last_name)]);
        });

        Schema::table('guests', function (Blueprint $table) {
            $table->unique('name_key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table) {
            $table->dropUnique(['name_key']);
            $table->dropColumn('name_key');
        });
    }
};
