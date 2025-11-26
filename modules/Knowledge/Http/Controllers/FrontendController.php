<?php

namespace Modules\Knowledge\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Modules\Knowledge\Models\KnowledgeArticle;
use Modules\Knowledge\Models\KnowledgeCategory;

class FrontendController extends Controller
{
    /**
     * Display the main knowledge base page with all categories
     *
     * @param  string  $company_alias
     * @return \Illuminate\View\View
     */
    public function index($company_alias)
    {
        $company = Company::where('subdomain', $company_alias)->with('whatsappWidget')->firstOrFail();

        // Get all active categories with their published articles count
        $categories = KnowledgeCategory::where('company_id', $company->id)
            ->active()
            ->withCount(['publishedArticles'])
            ->ordered()
            ->get();

        // Get featured articles
        $featuredArticles = KnowledgeArticle::where('company_id', $company->id)
            ->published()
            ->featured()
            ->with('category')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return view('knowledge::frontend.index', compact(
            'company',
            'categories',
            'featuredArticles'
        ));
    }

    /**
     * Display articles from a specific category
     *
     * @param  string  $company_alias
     * @param  string  $category_slug
     * @return \Illuminate\View\View
     */
    public function category($company_alias, $category_slug)
    {
        $company = Company::where('subdomain', $company_alias)->with('whatsappWidget')->firstOrFail();

        $category = KnowledgeCategory::where('company_id', $company->id)
            ->where('slug', $category_slug)
            ->active()
            ->firstOrFail();

        // Get articles in this category
        $articles = KnowledgeArticle::where('company_id', $company->id)
            ->where('category_id', $category->id)
            ->published()
            ->with('category')
            ->ordered()
            ->paginate(12);

        // Get all categories for navigation
        $categories = KnowledgeCategory::where('company_id', $company->id)
            ->active()
            ->withCount(['publishedArticles'])
            ->ordered()
            ->get();

        return view('knowledge::frontend.category', compact(
            'company',
            'category',
            'articles',
            'categories'
        ));
    }

    /**
     * Display a specific article
     *
     * @param  string  $company_alias
     * @param  string  $article_slug
     * @return \Illuminate\View\View
     */
    public function article($company_alias, $article_slug)
    {
        $company = Company::where('subdomain', $company_alias)->with('whatsappWidget')->firstOrFail();

        $article = KnowledgeArticle::where('company_id', $company->id)
            ->where('slug', $article_slug)
            ->published()
            ->with('category')
            ->firstOrFail();

        // Increment view count
        $article->incrementViews();

        // Get related articles from the same category
        $relatedArticles = KnowledgeArticle::where('company_id', $company->id)
            ->where('category_id', $article->category_id)
            ->where('id', '!=', $article->id)
            ->published()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Get all categories for navigation
        $categories = KnowledgeCategory::where('company_id', $company->id)
            ->active()
            ->withCount(['publishedArticles'])
            ->ordered()
            ->get();

        return view('knowledge::frontend.article', compact(
            'company',
            'article',
            'relatedArticles',
            'categories'
        ));
    }

    /**
     * Search articles
     *
     * @param  string  $company_alias
     * @return \Illuminate\View\View
     */
    public function search($company_alias, Request $request)
    {
        $company = Company::where('subdomain', $company_alias)->with('whatsappWidget')->firstOrFail();
        $query = $request->get('q', '');

        $articles = collect();

        if ($query) {
            $articles = KnowledgeArticle::where('company_id', $company->id)
                ->published()
                ->search($query)
                ->with('category')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
        }

        // Get all categories for navigation
        $categories = KnowledgeCategory::where('company_id', $company->id)
            ->active()
            ->withCount(['publishedArticles'])
            ->ordered()
            ->get();

        return view('knowledge::frontend.search', compact(
            'company',
            'articles',
            'categories',
            'query'
        ));
    }
}
