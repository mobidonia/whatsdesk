<?php

namespace Modules\Knowledge\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Knowledge\Models\KnowledgeCategory;

class CategoryController extends Controller
{
    /**
     * Provider class.
     */
    private $provider = KnowledgeCategory::class;

    /**
     * Web RoutePath for the name of the routes.
     */
    private $webroute_path = 'knowledgebase.categories.';

    /**
     * View path.
     */
    private $view_path = 'knowledge::categories.';

    /**
     * Parameter name.
     */
    private $parameter_name = 'category';

    /**
     * Title of this crud.
     */
    private $title = 'Knowledge Category';

    /**
     * Title of this crud in plural.
     */
    private $titlePlural = 'Knowledge Categories';

    private function getFields($class = 'col-md-4')
    {
        $fields = [];

        // Basic category information
        $fields[] = ['class' => 'col-md-8', 'ftype' => 'input', 'name' => 'Name', 'id' => 'name', 'placeholder' => 'Category name', 'required' => true];
        $fields[] = ['class' => 'col-md-8', 'ftype' => 'input', 'name' => 'Slug', 'id' => 'slug', 'placeholder' => 'URL-friendly version of name', 'required' => true];
        $fields[] = ['class' => 'col-md-12', 'ftype' => 'textarea', 'name' => 'Description', 'id' => 'description', 'placeholder' => 'Brief description of this category', 'required' => false];
        $fields[] = ['class' => 'col-md-12', 'ftype' => 'iconselect', 'name' => 'Icon', 'id' => 'icon', 'placeholder' => 'Select an icon for this category', 'required' => false, 'help' => 'Icon will be displayed in the frontend'];

        // Settings
        $fields[] = ['class' => 'col-md-6', 'ftype' => 'input', 'name' => 'Sort Order', 'id' => 'sort_order', 'placeholder' => '0', 'required' => false, 'help' => 'Lower numbers appear first'];
        $fields[] = ['class' => 'col-md-6', 'ftype' => 'bool', 'name' => 'Is Active', 'id' => 'is_active', 'placeholder' => 'Show this category?', 'required' => false];

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

        $items = $this->provider::withCount(['articles', 'publishedArticles'])
            ->orderBy('sort_order')
            ->orderBy('name');
        $items = $items->paginate(config('settings.paginate'));

        $setup = [
            'usefilter' => null,
            'title' => __('Knowledge Categories'),
            'action_link2' => route($this->webroute_path.'create'),
            'action_name2' => __('Create Category'),
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
                'title' => __('crud.new_item', ['item' => __('Knowledge Category')]),
                'action_link' => route($this->webroute_path.'index'),
                'action_name' => __('crud.back'),
                'iscontent' => true,
                'action' => route($this->webroute_path.'store'),
            ],
            'fields' => $fields,
        ]);
    }

    private function handleCategoryData(Request $request, $item = null)
    {
        $data = [
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'icon' => $request->icon,
            'sort_order' => $request->sort_order ?: 0,
            'is_active' => $request->has('is_active'),
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

        $data = $this->handleCategoryData($request);

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
                'title' => __('crud.edit_item_name', ['item' => __($this->title), 'name' => $item->name]),
                'action_link' => route($this->webroute_path.'index'),
                'action_name' => __('crud.back'),
                'iscontent' => true,
                'isupdate' => true,
                'action' => route($this->webroute_path.'update', ['category' => $item->id]),
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

        $data = $this->handleCategoryData($request, $item);
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

        // Check if category has articles
        if ($item->articles()->count() > 0) {
            return redirect()->route($this->webroute_path.'index')
                ->withError(__('Cannot delete category with articles. Please move or delete articles first.'));
        }

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
     * Get all active categories for API
     *
     * @return Response
     */
    public function all(Request $request)
    {
        $items = $this->provider::active()
            ->withCount(['publishedArticles'])
            ->ordered()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $items,
        ]);
    }
}
