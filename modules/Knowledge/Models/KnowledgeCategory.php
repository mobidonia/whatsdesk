<?php

namespace Modules\Knowledge\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'knowledge_categories';

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
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function ($category) {
            if (! $category->company_id) {
                $category->company_id = session('company_id');
            }
        });
    }

    /**
     * Get the company that owns the category.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * Get the articles for the category.
     */
    public function articles(): HasMany
    {
        return $this->hasMany(KnowledgeArticle::class, 'category_id');
    }

    /**
     * Get published articles for the category.
     */
    public function publishedArticles(): HasMany
    {
        return $this->hasMany(KnowledgeArticle::class, 'category_id')
             ->where('status', 'published');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Clone the category.
     */
    public function clone()
    {
        $clone = $this->replicate();
        $clone->name = $this->name.' - '.__('Copy');
        $clone->slug = $this->slug.'_'.__('copy');
        $clone->save();

        return $clone;
    }

    /**
     * Get articles count for this category.
     */
    public function getArticlesCountAttribute()
    {
        return $this->articles()->count();
    }

    /**
     * Get published articles count for this category.
     */
    public function getPublishedArticlesCountAttribute()
    {
        return $this->publishedArticles()->count();
    }
}
