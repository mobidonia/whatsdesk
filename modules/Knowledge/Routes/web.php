<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([
    'middleware' => ['web', 'impersonate'],
    'namespace' => 'Modules\Knowledge\Http\Controllers',
], function () {
    Route::group([
        'middleware' => ['verified', 'web', 'auth', 'impersonate', 'isOwnerOnPro'],
    ], function () {
        // Knowledge Categories Management
        Route::resource('knowledge/categories', 'CategoryController')->parameters(['categories' => 'category'])->names([
            'index' => 'knowledgebase.categories.index',
            'create' => 'knowledgebase.categories.create',
            'store' => 'knowledgebase.categories.store',
            'show' => 'knowledgebase.categories.show',
            'edit' => 'knowledgebase.categories.edit',
            'update' => 'knowledgebase.categories.update',
            'destroy' => 'knowledgebase.categories.destroy',
        ]);
        Route::get('knowledge/categories/{category}/delete', 'CategoryController@destroy')->name('knowledgebase.categories.delete');
        Route::get('knowledge/categories/{category}/clone', 'CategoryController@clone')->name('knowledgebase.categories.clone');

        // Knowledge Articles Management
        Route::resource('knowledge/articles', 'ArticleController')->parameters(['articles' => 'article'])->names([
            'index' => 'knowledgebase.articles.index',
            'create' => 'knowledgebase.articles.create',
            'store' => 'knowledgebase.articles.store',
            'show' => 'knowledgebase.articles.show',
            'edit' => 'knowledgebase.articles.edit',
            'update' => 'knowledgebase.articles.update',
            'destroy' => 'knowledgebase.articles.destroy',
        ]);
        Route::get('knowledge/articles/{article}/delete', 'ArticleController@destroy')->name('knowledgebase.articles.delete');
        Route::get('knowledge/articles/{article}/clone', 'ArticleController@clone')->name('knowledgebase.articles.clone');
    });
});

Route::group([
    'middleware' => ['web', 'impersonate'],
    'namespace' => 'Modules\Knowledge\Http\Controllers',
], function () {
    // API Routes for frontend consumption
    Route::get('/api/knowledge/categories', 'CategoryController@all')->name('knowledge.categories.all');
    Route::get('/api/knowledge/articles', 'ArticleController@all')->name('knowledge.articles.all');
    Route::get('/api/knowledge/articles/{slug}', 'ArticleController@single')->name('knowledge.articles.single');

    // Frontend Knowledge Base Routes
    Route::get('/knowledge/{company_alias}', 'FrontendController@index')->name('knowledge.frontend.index');
    Route::get('/knowledge/{company_alias}/category/{category_slug}', 'FrontendController@category')->name('knowledge.frontend.category');
    Route::get('/knowledge/{company_alias}/article/{article_slug}', 'FrontendController@article')->name('knowledge.frontend.article');
    Route::get('/knowledge/{company_alias}/search', 'FrontendController@search')->name('knowledge.frontend.search');
});
