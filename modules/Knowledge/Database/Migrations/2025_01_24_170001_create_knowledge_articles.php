<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnowledgeArticles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('knowledge_articles', function (Blueprint $table) {
            $table->bigIncrements('id');

            // Company relationship
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');

            // Category relationship
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('knowledge_categories')->onDelete('cascade');

            // Article fields
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->text('excerpt')->nullable();

            // Read time calculation
            $table->integer('read_time')->default(1);

            // Status and visibility
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->boolean('is_featured')->default(false);

            // Ordering
            $table->integer('sort_order')->default(0);

            // Analytics
            $table->unsignedInteger('views_count')->default(0);
            $table->boolean('is_helpful')->default(true); // For user feedback

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index(['company_id', 'status']);
            $table->index(['company_id', 'category_id']);
            $table->index(['company_id', 'is_featured']);
            $table->index(['company_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('knowledge_articles');
    }
}
