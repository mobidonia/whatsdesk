<?php

namespace Modules\Knowledge\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeArticle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'knowledge_articles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'is_helpful' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($article) {
            if (! $article->company_id) {
                $article->company_id = session('company_id');
            }
        });

        static::saving(function ($article) {
            // Calculate read time before saving
            $article->read_time = static::calculateReadTime($article->content);
        });

        static::updating(function ($article) {
            // Calculate read time before updating
            $article->read_time = static::calculateReadTime($article->content);
        });
    }

    /**
     * Get the company that owns the article.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * Get the category that owns the article.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'category_id');
    }

    /**
     * Scope a query to only include published articles.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope a query to only include featured articles.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('title');
    }

    /**
     * Scope a query to search articles by title or content.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
                ->orWhere('content', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%");
        });
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Clone the article.
     */
    public function clone()
    {
        $clone = $this->replicate();
        $clone->title = $this->title.' - '.__('Copy');
        $clone->slug = $this->slug.'_'.__('copy');
        $clone->status = 'draft'; // Always clone as draft
        $clone->save();

        return $clone;
    }

    /**
     * Increment views count.
     */
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    /**
     * Calculate estimated reading time in minutes
     * Average adult reads 200-250 words per minute
     * We'll use 200 words per minute as average
     */
    protected static function calculateReadTime($content)
    {
        // Strip HTML tags and count words
        $wordCount = str_word_count(strip_tags($content));

        // Calculate minutes rounded up
        $minutes = ceil($wordCount / 200);

        // Ensure minimum 1 minute
        return max(1, $minutes);
    }

    /**
     * Get the formatted read time.
     */
    public function getFormattedReadTimeAttribute()
    {
        if ($this->read_time == 1) {
            return '1 '.__('minute read');
        }

        return $this->read_time.' '.__('minutes read');
    }
}
