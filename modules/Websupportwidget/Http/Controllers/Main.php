<?php

namespace Modules\Websupportwidget\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\Knowledge\Models\KnowledgeArticle;
use Modules\Knowledge\Models\KnowledgeCategory;
use Modules\Websupportwidget\Models\WebsupportWidget;

class Main extends Controller
{
    /**
     * Render the widget JavaScript for embedding
     */
    public function widget(Request $request)
    {
        $widget = WebsupportWidget::where('id', $request->id)->first();

        if (! $widget) {
            return response('console.error("Web Support Widget not found");', 200)
                ->header('Content-Type', 'application/javascript');
        }

        $imageLink = $widget->getImageLinkAttribute();
        $widgetData = $widget->toArray();
        $widgetData['logo'] = (str_starts_with($imageLink, 'http'))
            ? $imageLink
            : config('app.url').$imageLink;
        $widgetData['widget_url'] = config('app.url').'/websupport/assets/';

        // If using S3 storage
        if (config('settings.use_s3_as_storage', false)) {
            $widgetData['logo'] = $imageLink;
        }

        // Add WhatsApp URL
        $widgetData['whatsapp_url'] = $widget->whatsapp_url;
        $widgetData['call_enabled'] = $widget->call_enabled;
        $widgetData['call_info_message'] = $widget->call_info_message;
        $widgetData['call_link_url'] = $widget->call_link_url;
        $widgetData['is_online'] = $widget->is_online;

        return response()
            ->view('websupportwidget::widget_js', $widgetData)
            ->header('Content-Type', 'application/javascript');
    }

    /**
     * Get knowledge base articles for help tab
     */
    public function getKnowledgeArticles(Request $request)
    {
        $widget = WebsupportWidget::where('id', $request->widget_id)->first();

        if (! $widget) {
            return response()->json(['error' => 'Widget not found'], 404);
        }

        $search = $request->get('search', '');
        $category = $request->get('category', '');
        $limit = min($request->get('limit', $widget->help_articles_limit), 10);

        // Check if Knowledge module is available
        if (! class_exists(KnowledgeArticle::class)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Knowledge base module not available',
            ]);
        }

        try {
            $query = KnowledgeArticle::where('company_id', $widget->company_id)
                ->published()
                ->with('category')
                ->select(['id', 'title', 'slug', 'excerpt', 'category_id', 'read_time'])
                ->orderBy('is_featured', 'desc')
                ->orderBy('views_count', 'desc');

            if ($search) {
                $query->search($search);
            }

            // Filter by category slug if provided
            if ($category) {
                $query->whereHas('category', function ($q) use ($category) {
                    $q->where('slug', $category);
                });
            }

            $articles = $query->limit($limit)->get();

            return response()->json([
                'status' => 'success',
                'articles' => $articles,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load articles',
            ]);
        }
    }

    /**
     * Get knowledge base categories
     */
    public function getKnowledgeCategories(Request $request)
    {
        $widget = WebsupportWidget::where('id', $request->widget_id)->first();

        if (! $widget) {
            return response()->json(['error' => 'Widget not found'], 404);
        }

        // Check if Knowledge module is available
        if (! class_exists(KnowledgeCategory::class)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Knowledge base module not available',
            ]);
        }

        try {
            $categories = KnowledgeCategory::where('company_id', $widget->company_id)
                ->active()
                ->select(['id', 'name', 'slug', 'description', 'icon'])
                ->withCount('publishedArticles')
                ->ordered()
                ->limit(8)
                ->get();

            return response()->json([
                'status' => 'success',
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load categories',
            ]);
        }
    }

    /**
     * Handle email form submission
     */
    public function sendEmail(Request $request)
    {
        $widget = WebsupportWidget::where('id', $request->widget_id)->first();

        if (! $widget) {
            return response()->json(['error' => 'Widget not found'], 404);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|max:2000',
        ]);

        try {
            // Send email using Laravel Mail
            Mail::send('websupportwidget::emails.contact', [
                'customerName' => $validatedData['name'],
                'customerEmail' => $validatedData['email'],
                'customerMessage' => $validatedData['message'],
                'companyName' => $widget->company_name,
            ], function ($message) use ($widget, $validatedData) {
                $message->to($widget->email_recipient)
                    ->subject($widget->email_subject_prefix.': '.$validatedData['name'])
                    ->replyTo($validatedData['email'], $validatedData['name']);
            });

            return response()->json([
                'status' => 'success',
                'message' => $widget->email_success_message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send email. Please try again.',
            ], 500);
        }
    }

    /**
     * Show widget configuration form
     */
    public function edit()
    {
        $widget = WebsupportWidget::where('company_id', $this->getCompany()->id)->first();

        if (! $widget) {
            $widget = $this->getDefaultWidgetData();
        } else {
            $id = $widget->getAttributes()['id'];
            $imageLink = $widget->getImageLinkAttribute();
            $widget = $widget->toArray();
            $widget['logo'] = $imageLink;
            $widget['embed_url'] = config('app.url').'/websupport/widget?id='.$id;
        }

        return view('websupportwidget::edit', ['widget' => $widget]);
    }

    /**
     * Show widget demo page
     */
    public function demo()
    {
        $widget = WebsupportWidget::where('company_id', $this->getCompany()->id)->first();

        if (! $widget) {
            return redirect()->route('websupportwidget.edit')
                ->with('error', 'Please configure your widget first.');
        }

        $widgetData = $widget->toArray();
        $widgetData['id'] = $widget->getAttributes()['id'];

        return view('websupportwidget::demo', ['widget' => $widgetData]);
    }

    /**
     * Store widget configuration
     */
    public function store(Request $request)
    {
        \Log::info('WebsupportWidget store method called', [
            'request_data' => $request->all(),
            'user_id' => auth()->user()->id,
            'company_id' => $this->getCompany()->id,
        ]);

        try {
            $validatedData = $request->validate([
                'company_name' => 'required|string|max:255',
                'welcome_message' => 'required|string|max:500',
                'primary_color' => 'required|string|max:7',
                'secondary_color' => 'required|string|max:7',
                'position' => 'required|in:bottom-right,bottom-left',

                // Chat settings
                'chat_enabled' => 'nullable|in:on',
                'whatsapp_number' => 'nullable|string|max:20',
                'chat_welcome_message' => 'nullable|string|max:500',
                'chat_button_text' => 'nullable|string|max:50',

                // Email settings
                'email_enabled' => 'nullable|in:on',
                'email_recipient' => 'nullable|email|max:255',
                'email_subject_prefix' => 'nullable|string|max:100',
                'email_welcome_message' => 'nullable|string|max:500',
                'email_success_message' => 'nullable|string|max:500',

                // Help settings
                'help_enabled' => 'nullable|in:on',
                'help_welcome_message' => 'nullable|string|max:500',
                'help_articles_limit' => 'nullable|integer|min:1|max:10',
                'help_show_search' => 'nullable|in:on',

                // Call settings
                'call_enabled' => 'nullable|in:on',
                'call_info_message' => 'nullable|string|max:500',
                'call_link_url' => 'nullable|url|max:255',

                // Advanced settings
                'show_company_logo' => 'nullable|in:on',
                'show_agent_status' => 'nullable|in:on',
                'offline_message' => 'nullable|string|max:500',
                'timezone' => 'required|string|max:50',
            ]);

            // Check for existing widget
            $widget = WebsupportWidget::where('company_id', $this->getCompany()->id)->first();

            if (! $widget) {
                $widget = new WebsupportWidget();
                $widget->id = $this->generateRandomString(10);
                $widget->company_id = $this->getCompany()->id;
            }

            // Handle boolean fields properly (checkboxes send "on" when checked, nothing when unchecked)
            $widget->chat_enabled = $request->input('chat_enabled') === 'on';
            $widget->email_enabled = $request->input('email_enabled') === 'on';
            $widget->help_enabled = $request->input('help_enabled') === 'on';
            $widget->help_show_search = $request->input('help_show_search') === 'on';
            $widget->call_enabled = $request->input('call_enabled') === 'on';
            $widget->show_company_logo = $request->input('show_company_logo') === 'on';
            $widget->show_agent_status = $request->input('show_agent_status') === 'on';

            // Assign other validated data
            foreach ($validatedData as $key => $value) {
                if (! in_array($key, ['chat_enabled', 'email_enabled', 'help_enabled', 'help_show_search', 'call_enabled', 'show_company_logo', 'show_agent_status'])) {
                    $widget->$key = $value;
                }
            }

            // Set defaults for required fields if tabs are disabled
            if (! $widget->chat_enabled) {
                $widget->whatsapp_number = $widget->whatsapp_number ?: '';
                $widget->chat_welcome_message = $widget->chat_welcome_message ?: 'Chat disabled';
                $widget->chat_button_text = $widget->chat_button_text ?: 'Chat';
            }

            if (! $widget->email_enabled) {
                $widget->email_recipient = $widget->email_recipient ?: auth()->user()->email;
                $widget->email_subject_prefix = $widget->email_subject_prefix ?: 'Contact Form';
                $widget->email_welcome_message = $widget->email_welcome_message ?: 'Email disabled';
                $widget->email_success_message = $widget->email_success_message ?: 'Thank you';
            }

            if (! $widget->help_enabled) {
                $widget->help_welcome_message = $widget->help_welcome_message ?: 'Help disabled';
                $widget->help_articles_limit = $widget->help_articles_limit ?: 5;
            }

            if (! $widget->call_enabled) {
                $widget->call_info_message = $widget->call_info_message ?: 'Please call us during working hours.';
            }

            // Debug: Log the widget data before saving
            \Log::info('Saving WebsupportWidget', [
                'widget_data' => $widget->toArray(),
                'company_id' => $this->getCompany()->id,
                'user_id' => auth()->user()->id,
            ]);

            $result = $widget->save();

            \Log::info('Widget save result', [
                'save_result' => $result,
                'widget_id' => $widget->id,
                'widget_data' => $widget->fresh()->toArray(),
            ]);

            // Handle logo upload
            if ($request->hasFile('logo')) {
                try {
                    $widget->logo = $this->saveImageVersions(
                        'uploads/companies/',
                        $request->logo,
                        [
                            ['name' => 'large'],
                        ]
                    );
                    $widget->update();
                } catch (\Exception $e) {
                    return redirect()->route('websupportwidget.edit')
                        ->with('error', 'Widget saved but logo upload failed: '.$e->getMessage());
                }
            }

            \Log::info('WebsupportWidget saved successfully', ['widget_id' => $widget->id]);

            return redirect()->route('websupportwidget.edit')
                ->with('status', 'Web Support Widget updated successfully');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('websupportwidget.edit')
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->route('websupportwidget.edit')
                ->with('error', 'Failed to save widget: '.$e->getMessage())
                ->withInput();
        }
    }

    /**
     * Generate random string for widget ID
     */
    private function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Get default widget configuration
     */
    private function getDefaultWidgetData()
    {
        return [
            'logo' => '',
            'company_name' => $this->getCompany()->name ?? 'Your Company',
            'welcome_message' => 'Hi there! 👋 How can we help you today?',
            'primary_color' => '#4F46E5',
            'secondary_color' => '#6366F1',
            'position' => 'bottom-right',

            // Chat defaults
            'chat_enabled' => true,
            'whatsapp_number' => '',
            'chat_welcome_message' => 'Hi! Let us know how we can help you.',
            'chat_button_text' => 'Start WhatsApp Chat',

            // Email defaults
            'email_enabled' => true,
            'email_recipient' => auth()->user()->email ?? '',
            'email_subject_prefix' => 'Contact Form',
            'email_welcome_message' => 'Send us a message and we\'ll get back to you as soon as possible.',
            'email_success_message' => 'Thank you! We will get back to you soon.',

            // Help defaults
            'help_enabled' => true,
            'help_welcome_message' => 'Browse our help articles to find answers quickly.',
            'help_articles_limit' => 5,
            'help_show_search' => true,

            // Advanced defaults
            'show_company_logo' => true,
            'show_agent_status' => true,
            'offline_message' => 'We\'re currently offline. Please leave a message!',
            'timezone' => 'UTC',
        ];
    }
}
