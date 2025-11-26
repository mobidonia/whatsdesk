<?php

namespace App\Http\Controllers\CRUD;

use App\Http\Controllers\Controller;
use App\Models\Posts;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PostsController extends Controller
{
    protected $imagePath = 'uploads/companies/';

    private function validateAccess()
    {
        if (! auth()->user()->hasRole('admin')) {
            abort(404);
        }
    }

    /**
     * Get Fields
     */
    private function getFields($type, ?Posts $post = null)
    {
        $dataToReturn = [];
        array_push($dataToReturn, ['ftype' => 'image', 'name' => __('Image'), 'id' => 'image']);
        array_push($dataToReturn, ['ftype' => 'input', 'name' => 'Title', 'id' => 'title', 'placeholder' => __('Enter title'), 'required' => true]);
        array_push($dataToReturn, ['ftype' => 'input', 'name' => 'Description', 'id' => 'description', 'placeholder' => __('Enter description'), 'required' => true]);
        array_push($dataToReturn, ['ftype' => 'input', 'name' => 'Subtitle', 'id' => 'subtitle', 'placeholder' => __('Enter subtitle'), 'required' => true]);
        array_push($dataToReturn, ['ftype' => 'input', 'name' => 'Link Name', 'id' => 'link_name', 'placeholder' => __('Enter link name'), 'required' => false]);
        array_push($dataToReturn, ['ftype' => 'input', 'name' => 'Link ', 'id' => 'link', 'placeholder' => __('Enter link URL'), 'required' => false]);
        array_push($dataToReturn, ['ftype' => 'input', 'type' => 'hidden', 'name' => 'Type', 'id' => 'type', 'placeholder' => __('Enter type'), 'required' => true, 'value' => $type]);

        if ($post) {
            $dataToReturn[0]['value'] = strlen($post->image) > 3 ? $post->image_link : null;
            $dataToReturn[1]['value'] = $post->title;
            $dataToReturn[2]['value'] = $post->description;
            $dataToReturn[3]['value'] = $post->subtitle;
            $dataToReturn[4]['value'] = $post->link_name;
            $dataToReturn[5]['value'] = $post->link;
        }

        //Features
        if ($type == 'feature') {
            unset($dataToReturn[4]);
            unset($dataToReturn[5]);
        } elseif ($type == 'mainfeature') {
            unset($dataToReturn[3]);
            unset($dataToReturn[4]);
            unset($dataToReturn[5]);
        } elseif ($type == 'showcase') {
            unset($dataToReturn[2]);
            unset($dataToReturn[3]);
            unset($dataToReturn[4]);
        } elseif ($type == 'testimonial') {
            unset($dataToReturn[4]);
            unset($dataToReturn[5]);
        } elseif ($type == 'process') {
            unset($dataToReturn[3]);
            unset($dataToReturn[5]);
        } elseif ($type == 'faq') {
            unset($dataToReturn[0]);
            unset($dataToReturn[3]);
            unset($dataToReturn[4]);
            unset($dataToReturn[5]);
        } elseif ($type == 'allergen') {
            unset($dataToReturn[2]);
            unset($dataToReturn[3]);
            unset($dataToReturn[4]);
            unset($dataToReturn[5]);
        } elseif ($type == 'driver') {
            unset($dataToReturn[2]);
            unset($dataToReturn[3]);
            unset($dataToReturn[4]);
            unset($dataToReturn[5]);
        } elseif ($type == 'notestatus') {
            unset($dataToReturn[0]);
            unset($dataToReturn[2]);
            unset($dataToReturn[3]);
            unset($dataToReturn[4]);
            unset($dataToReturn[5]);
        } elseif ($type == 'notetype') {
            unset($dataToReturn[2]);
            unset($dataToReturn[3]);
            unset($dataToReturn[4]);
            unset($dataToReturn[5]);
        }

        return $dataToReturn;

    }

    private function getTitle($type)
    {
        $titles = [
            'allergen' => __('Allergens'),
            'driver' => __('Driver Categories'),
            'feature' => __('Features'),
            'mainfeature' => __('Main Features'),
            'testimonial' => __('Testimonials'),
            'process' => __('Processes'),
            'faq' => __('FAQs'),
            'blog' => __('Blog links'),
            'showcase' => __('Showcase links'),
            'notestatus' => __('Note statuses'),
            'notetype' => __('Note types'),
        ];

        return $titles[$type];
    }

    private function getItemNames($type)
    {
        $titles = [
            'allergen' => __('allergen'),
            'driver' => __('driver'),
            'feature' => __('feature'),
            'mainfeature' => __('mainfeature'),
            'testimonial' => __('testimonial'),
            'process' => __('process'),
            'faq' => __('faq'),
            'blog' => __('blog'),
            'showcase' => __('showcase'),
            'notestatus' => __('notestatus'),
            'notetype' => __('notetype'),
        ];

        return $titles[$type];
    }

    private function getCreateRules($type)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
        ];

        return $rules;
    }

    private function getUpdateCreateRules($type, $id)
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
        ];

        return $rules;
    }

    private function getImageDimensions($type)
    {
        $dimensions = [
            ['name' => 'large'],
        ];
        if ($type == 'showcase') {
            $dimensions[0]['type'] = 'png';
        }

        return $dimensions;
    }

    public function index($type): View
    {
        $this->validateAccess();
        $title = $this->getTitle($type);

        return view('crud.posts.index', ['setup' => [
            'iscontent' => true,
            'title' => $title,
            'action_link' => route('admin.landing.posts.create', ['type' => $type]),
            'action_name' => __('Add new'),
            'items' => Posts::where('post_type', $type)->get(),
            'item_names' => $title,
            'breadcrumbs' => [
                [$title, route('admin.landing.posts', ['type' => $type])],
            ],
        ]]);
    }

    public function create($type): View
    {
        $this->validateAccess();
        $itemName = $this->getItemNames($type);
        $title = $this->getTitle($type);

        return view('general.form', ['setup' => [
            'title' => __('crud.add_new_item', ['item' => __($itemName)]),
            'action_link' => route('admin.landing.posts', ['type' => $type]),
            'action_name' => __('Back'),
            'iscontent' => true,
            'action' => route('admin.landing.posts.store', ['type' => $type]),
            'breadcrumbs' => [
                [$title, route('admin.landing.posts', ['type' => $type])],
                [__('New'), null],
            ],
        ],
            'fields' => $this->getFields($type),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->validateAccess();
        //Validate first
        $request->validate($this->getCreateRules($request->type));

        $itemName = $this->getItemNames($request->type);

        $post = Posts::create([
            'post_type' => $request->type,
            'title' => $request->title,
            'description' => $request->description,
            'image' => '',
            'subtitle' => $request->subtitle,
            'link' => $request->link,
            'link_name' => $request->link_name,
        ]);

        $post->save();

        if ($request->hasFile('image')) {
            $post->image = $this->saveImageVersions(
                $this->imagePath,
                $request->image,
                $this->getImageDimensions($request->type)
            );
            $post->update();
        }

        return redirect()->route('admin.landing.posts', ['type' => $request->type])->withStatus(__('crud.item_has_been_added', ['item' => $itemName]));
    }

    public function edit(Posts $post): View
    {

        $this->validateAccess();
        $fields = $this->getFields($post->post_type, $post);
        $itemName = $this->getItemNames($post->post_type);
        $title = $this->getTitle($post->post_type);

        return view('general.form', ['setup' => [
            'title' => __('crud.edit_item_name', ['item' => $itemName, 'name' => $post->title]),
            'action_link' => route('admin.landing.posts', ['type' => $post->post_type]),
            'action_name' => __('Back'),
            'iscontent' => true,
            'isupdate' => true,
            'action' => route('admin.landing.posts.update', ['post' => $post->id]),
            'breadcrumbs' => [
                [$title, route('admin.landing.posts', ['type' => $post->post_type])],
                [$post->title, null],
            ],
        ],
            'fields' => $fields, ]);
    }

    public function destroy(Posts $post): RedirectResponse
    {
        $this->validateAccess();
        $type = $post->post_type;
        $post->delete();

        return redirect()->route('admin.landing.posts', ['type' => $type])->withStatus(__('Item was deleted.'));
    }

    public function update(Request $request, Posts $post): RedirectResponse
    {
        $this->validateAccess();

        $post->title = $request->title;
        if ($request->has('subtitle')) {
            $post->subtitle = $request->subtitle;
        }
        if ($request->has('description')) {
            $post->description = $request->description;
        }
        if ($request->has('link')) {
            $post->link = $request->link;
        }
        if ($request->has('link_name')) {
            $post->link_name = $request->link_name;
        }

        if ($request->hasFile('image')) {
            $post->image = $this->saveImageVersions(
                $this->imagePath,
                $request->image,
                $this->getImageDimensions($post->post_type)
            );
        }

        $post->update();

        return redirect()->route('admin.landing.posts', ['type' => $post->post_type])->withStatus(__('Item was updated'));
    }
}
