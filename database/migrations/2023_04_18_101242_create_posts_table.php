<?php

use App\Enums\Post\PostStatus;
use App\Enums\PriorityStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('meta_title');
            $table->text('image');
            $table->tinyInteger('is_featured')->default(1);
            $table->tinyInteger('status')->default(PostStatus::Draft->value);
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->tinyInteger('priority')->default(PriorityStatus::NotPriority->value);
            $table->timestamp('posted_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
};
