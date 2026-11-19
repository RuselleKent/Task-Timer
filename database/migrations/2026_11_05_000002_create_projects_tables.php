<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Pinagsama ko na rito yung projects at project_users dahil magkaugnay naman sila sa iisang flow
    public function up(): void
    {
        // PROJECTS: Listahan ng lahat ng mga assigned projects
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->string('name', 100)->unique(); // Max 100 characters at bawal ang magkapareho (case-insensitive sa MySQL)
            $table->string('code', 20)->nullable(); // Optional muna kasi wala namang code field sa Add a project form
            $table->foreignId('manager_id')->constrained('users')->restrictOnDelete(); //main manager na may hawak ng project

            $table->timestamp('created_at')->useCurrent();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete(); // Kung sinong manager ang gumawa ng project
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
        });

        // PROJECT_USERS: Mapping table para malaman kung sinong mga user ang kasama sa isang partikular na project
        Schema::create('project_users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreignId('project_id')->constrained('projects')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();
            $table->boolean('manager')->default(false); // Magiging true kapag isa rin siyang manager o lead sa project na ito
            $table->foreignId('removed_by')->nullable()->constrained('users')->nullOnDelete(); // Naka-null muna habang active member pa
            $table->timestamp('removed_at')->nullable(); // Naka-null kapag active; hindi binubura ang row para may historical record pa rin

            $table->timestamp('created_at')->useCurrent(); // Kailan ginawa ang row (kasabay ng pag-add sa user)
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete(); // Kung sinong manager ang nag-assign sa kaniya

            // Para mabilis ma-filter kung sinu-sino ang mga active members, at kung anong projects ang active sa isang user
            $table->index(['project_id', 'removed_at']);
            $table->index(['user_id', 'removed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_users');
        Schema::dropIfExists('projects');
    }
};