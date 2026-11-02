<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('members', function(Blueprint $t) { $t->id(); $t->string('name',120); $t->unsignedBigInteger('amount_paisa'); $t->uuid('submission_id')->unique(); $t->timestamps(); });
  Schema::create('categories', function(Blueprint $t) { $t->id(); $t->string('name',80)->unique(); $t->timestamps(); });
  Schema::create('expenses', function(Blueprint $t) { $t->id(); $t->foreignId('category_id')->constrained()->restrictOnDelete(); $t->string('title',180); $t->unsignedBigInteger('amount_paisa'); $t->date('spent_on'); $t->uuid('submission_id')->unique(); $t->timestamps(); });
 }
 public function down(): void { Schema::dropIfExists('expenses'); Schema::dropIfExists('categories'); Schema::dropIfExists('members'); }
};
