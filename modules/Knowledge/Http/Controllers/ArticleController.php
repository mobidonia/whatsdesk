<?php

namespace Modules\Knowledge\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Knowledge\Models\KnowledgeArticle;
use Modules\Knowledge\Models\KnowledgeCategory;

class ArticleController extends Controller
{
    /**
     * Provider class.
     */
    private $provider = KnowledgeArticle::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'knowledgebase.articles.';

    /**
     * View path.
     */
    private $view_path = 'knowledge::articles.';

    /**
     * Parameter name.
     */
    private $parameter_name = 'article';

    /**
     * Title of this crud.
     */
    private $title = 'Knowledge Article';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'Knowledge Articles';

    private function getFields($class = 'col-md-4')
    {
        $fields = [];

        // Get categories for dropdown
        $categories = KnowledgeCategory::active()->ordered()->pluck('name', 'id')->toArray();

        // Main content section
        $fields[] = ['class' => 'col-md-8', 'ftype' => 'input', 'name' => 'Title', 'id' => 'title', 'placeholder' => 'Article title', 'required' => true];
        $fields[] = ['class' => 'col-md-8', 'ftype' => 'input', 'name' => 'Slug', 'id' => 'slug', 'placeholder' => 'URL-friendly version of title', 'required' => true];
        $fields[] = ['class' => 'col-md-12', 'ftype' => 'textarea', 'name' => 'Content', 'id' => 'content', 'placeholder' => 'Start writing your article...', 'required' => true];

        // Category and settings
        $fields[] = ['class' => 'col-md-12', 'ftype' => 'select', 'name' => 'Category', 'id' => 'category_id', 'placeholder' => 'Select category', 'data' => $categories, 'required' => true];

        $fields[] = ['class' => 'col-md-12', 'ftype' => 'select', 'name' => 'Status', 'id' => 'status', 'placeholder' => 'Select status', 'data' => [
            'draft' => 'Draft',
            'published' => 'Published',
        ], 'required' => true];

        $fields[] = ['class' => 'col-md-6', 'ftype' => 'input', 'name' => 'Sort Order', 'id' => 'sort_order', 'placeholder' => '0', 'required' => false, 'help' => 'Lower numbers appear first'];
        $fields[] = ['class' => 'col-md-6', 'ftype' => 'bool', 'name' => 'Is Featured', 'id' => 'is_featured', 'placeholder' => 'Feature this article?', 'required' => false];

        // Excerpt
        $fields[] = ['class' => 'col-md-12', 'ftype' => 'textarea', 'name' => 'Excerpt', 'id' => 'excerpt', 'placeholder' => 'Brief summary (optional)', 'required' => false, 'help' => 'Brief summary of the article'];

        return $fields;
    }

    private function getFilterFields()
    {
        $fields = $this->getFields('col-md-3');

        return $fields;
    }

    /**
     * Auth checker function for the crud.
     */
    private function authChecker()
    {
        $this->ownerAndStaffOnly();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $this->authChecker();

        $items = $this->provider::with('category')
            ->orderBy('created_at', 'desc');
        $items = $items->paginate(config('settings.paginate'));

        $setup = [
            'usefilter' => null,
            'title' => __('Knowledge Articles'),
            'action_link2' => route($this->webroute_path.'create'),
            'action_name2' => __('Create Article'),
            'items' => $items,
            'item_names' => $this->titlePlural,
            'webroute_path' => $this->webroute_path,
            'fields' => $this->getFields('col-md-3'),
            'filterFields' => $this->getFilterFields(),
            'custom_table' => true,
            'parameter_name' => $this->parameter_name,
            'parameters' => count($_GET) != 0,
            'hidePaging' => false,
        ];

        return view($this->view_path.'index', ['setup' => $setup]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $this->authChecker();

        $fields = $this->getFields('col-md-6');

        return view($this->view_path.'edit', [
            'setup' => [
                'title' => __('crud.new_item', ['item' => __('Knowledge Article')]),
                'action_link' => route($this->webroute_path.'index'),
                'action_name' => __('crud.back'),
                'iscontent' => true,
                'action' => route($this->webroute_path.'store'),
            ],
            'fields' => $fields,
        ]);
    }

    private function handleArticleData(Request $request, $item = null)
    {
        $data = [
            'title' => $request->title,
            'slug' => $request->slug,
            'content' => $request->content_hidden ?: $request->content,
            'excerpt' => $request->excerpt,
            'category_id' => $request->category_id,
            'status' => $request->status,
            'sort_order' => $request->sort_order ?: 0,
            'is_featured' => $request->has('is_featured'),
        ];

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $this->authChecker();

        $data = $this->handleArticleData($request);

        $item = $this->provider::create($data);

        return redirect()->route($this->webroute_path.'index')
            ->withStatus(__('crud.item_has_been_added', ['item' => __($this->title)]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);

        $fields = $this->getFields('col-md-6');

        // Set values for all fields from the item
        foreach ($fields as $key => $field) {
            if (isset($field['id']) && isset($item->{$field['id']})) {
                $fields[$key]['value'] = $item->{$field['id']};
            }
        }

        return view($this->view_path.'edit', [
            'setup' => [
                'title' => __('crud.edit_item_name', ['item' => __($this->title), 'name' => $item->title]),
                'action_link' => route($this->webroute_path.'index'),
                'action_name' => __('crud.back'),
                'iscontent' => true,
                'isupdate' => true,
                'action' => route($this->webroute_path.'update', ['article' => $item->id]),
            ],
            'fields' => $fields,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);

        $data = $this->handleArticleData($request, $item);
        $item->update($data);

        return redirect()->route($this->webroute_path.'index')
            ->withStatus(__('crud.item_has_been_updated', ['item' => __($this->title)]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->delete();

        return redirect()->route($this->webroute_path.'index')
            ->withStatus(__('crud.item_has_been_removed', ['item' => __($this->title)]));
    }

    public function clone($id)
    {
        $this->authChecker();
        $item = $this->provider::findOrFail($id);
        $item->clone();

        return redirect()->route($this->webroute_path.'index')
            ->withStatus(__('crud.item_has_been_cloned', ['item' => __($this->title)]));
    }

    //ADDITIONAL NEEDED FUNCTIONS

    /**
     * Get all published articles with pagination
     *
     * @return Response
     */
    public function all(Request $request)
    {
        $limit = $request->get('limit', 10);
        $categoryId = $request->get('category_id');
        $search = $request->get('search');

        $query = $this->provider::with('category')
            ->published()
            ->select(['id', 'title', 'slug', 'excerpt', 'category_id', 'created_at', 'updated_at', 'read_time', 'views_count'])
            ->orderBy('created_at', 'desc');

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($search) {
            $query->search($search);
        }

        $items = $query->paginate($limit);
        $data = $items->items();

        return response()->json([
            'status' => true,
            'data' => $data,
            'pagination' => [
                'current_page' => $items->currentPage(),
                'last_page' => $items->lastPage(),
                'per_page' => $limit,
                'total' => $items->total(),
            ],
        ]);
    }

    /**
     * Get single article by slug
     *
     * @param  string  $slug
     * @return Response
     */
    public function single($slug)
    {
        $item = $this->provider::with('category')
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment views
        $item->incrementViews();

        return response()->json([
            'status' => true,
            'data' => $item,
        ]);
    }
}
