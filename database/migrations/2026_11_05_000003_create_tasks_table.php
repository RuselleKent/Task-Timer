<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // TASKS: Dito naka-save ang lahat ng task na ginagawa o sinimulan ng user
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete(); // Kung sinong user ang may-ari ng task
            // Naka-nullable kasi kapag Non-production task, walang nakatalagang project
            $table->foreignId('project_id')->nullable()->constrained('projects')->restrictOnDelete();
            $table->string('title', 100);
            $table->enum('type', ['Production', 'Non-production']);
            $table->string('description', 500)->nullable(); // Optional na detalye tungkol sa task
            $table->enum('status', ['Running', 'Paused', 'Stopped'])->default('Running'); // Awtomatikong 'Running' sa simula

            $table->timestamp('created_on')->useCurrent();
            $table->timestamp('updated_on')->useCurrent()->useCurrentOnUpdate();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            // Para mabilis ma-check kung may active tracker pa ang user, at para bumilis din ang Tasks page
            $table->index(['user_id', 'status']);
        });

        // Validation sa DB level: Kapag Production dapat may project_id, kapag Non-production dapat walang project_id
        DB::statement("ALTER TABLE tasks ADD CONSTRAINT chk_tasks_type_project CHECK ((type = 'Production' AND project_id IS NOT NULL) OR (type = 'Non-production' AND project_id IS NULL))");
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
