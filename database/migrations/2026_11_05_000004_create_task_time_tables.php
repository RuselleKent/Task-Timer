<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Pinagsama ko na rito yung task_times at history kasi iisa lang naman ang flow ng sync nila
    public function up(): void
    {
        // TASK_TIMES: Dito lang naka-save yung current/active time ng task.
        // Pwedeng maraming rows per task depende kung ilang beses nag-pause at resume.
        Schema::create('task_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreignId('task_id')->constrained('tasks')->restrictOnDelete();
            $table->dateTime('start_time')->useCurrent(); // dateTime ang gamit sa mga oras ng task para hindi mag-auto update ang column sa MariaDB
            $table->dateTime('end_time')->nullable(); // Naka-null hangga't tumatakbo pa ang timer
            $table->enum('end_reason', ['Paused', 'Stopped'])->nullable(); // Null din hangga't hindi pa pina-pause o stop
            $table->boolean('altered')->default(false); // Magiging true kapag may binago o inedit sa time record
            $table->timestamp('created_on')->useCurrent();

            // Para mapabilis ang loading ng reports (filtering per task at per araw)
            $table->index(['task_id', 'start_time']);
        });

        // Validation sa DB level:
        // 1. Dapat laging mas huli ang end_time kaysa sa start_time.
        // 2. Dapat sabay na meron o sabay na null ang end_time at end_reason.
        // Note: Yung checking para sa magkaka-overlap na oras, sa backend code na lang iyon ihahandle.
        DB::statement('ALTER TABLE task_times ADD CONSTRAINT chk_task_times_end_after_start CHECK (end_time IS NULL OR end_time > start_time)');
        DB::statement('ALTER TABLE task_times ADD CONSTRAINT chk_task_times_end_pair CHECK ((end_time IS NULL) = (end_reason IS NULL))');

        // TASK_TIME_HISTORY: Para lang sa mga lumang time logs na pinalitan.
        // Laging naiiwan sa task_times yung pinakabagong record para iwas conflict sa tracking.
        Schema::create('task_time_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index();
            $table->foreignId('task_time_id')->constrained('task_times')->restrictOnDelete(); // Reference kung anong record ang inedit
            $table->dateTime('old_start_time'); // Required; mga tapos na time records lang ang pwedeng ma-edit dito
            $table->dateTime('old_end_time');
            $table->foreignId('updated_by')->constrained('users')->restrictOnDelete(); // Kung sinong user ang nagbago
            $table->timestamp('change_at')->useCurrent(); // Oras kung kailan binago. Hindi na ine-edit ang row na 'to kaya walang created_on at updated_on

            // Para mabilis ma-load ang historical change logs ng isang time record nang sunod-sunod sa petsa
            $table->index(['task_time_id', 'change_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_time_history');
        Schema::dropIfExists('task_times');
    }
};