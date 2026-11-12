<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Kapag nag-run ng "php artisan migrate", ito dapat ang maunang tumakbo kasi naka-depende rito ang ibang tables
    public function up(): void
    {
        // USERS: Listahan ng lahat ng may access sa system (mga User at Manager)
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->index(); // Kung saang company o organisasyon kabilang ang user
            $table->string('username')->unique(); // Unique identifier na gagamitin sa pag-sign in
            $table->string('password'); // Naka-hash, hindi plain text ang laman nito
            $table->string('authentication')->nullable();
            $table->string('authentication_key')->nullable(); // Token o key na nagkukumpirma ng valid sign in
            $table->enum('status', ['Active', 'Inactive'])->default('Active'); // Kapag 'Inactive', awtomatikong hindi na makaka-login ang user
            $table->string('first_name');
            $table->string('last_name'); // Pagsasamahin na lang bilang full name kapag ipapakita na sa interface
            $table->enum('system_role', ['User', 'Manager']);
            $table->string('position')->nullable();

            // Audit fields: Standard configuration para sa pagsubaybay ng records
            $table->timestamp('created_on')->useCurrent();
            $table->timestamp('updated_on')->useCurrent()->useCurrentOnUpdate(); // Automatic nag-a-update tuwing may pagbabago sa row
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete(); // Kung sinong user ang huling nag-edit nito
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
