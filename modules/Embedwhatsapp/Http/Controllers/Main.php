<?php

namespace Modules\Embedwhatsapp\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Modules\Embedwhatsapp\Models\Whatsappwidget;

class Main extends Controller
{
    public function index(Request $request)
    {
        //Get the widget data
        $widget = Whatsappwidget::where('id', $request->id)->first();
        if (!$widget) {
            return response()->json(['error' => 'Widget not found'], 404);
        } else {
            $imageLink = $widget->getImageLinkAttribute();
            $widget = $widget->toArray();

            $widget['message'] = $widget['widget_text'];
            $widget['url'] = config('app.url') . "/default/wpbox/widget/";
            $widget['logo'] = config('app.url') . $imageLink;
            //if storage type is s3 cloud
            if (config('settings.use_s3_as_storage', false)) {
                $widget['logo'] = $imageLink;
            }
            //Brij Mohan Negi Update on 25Jun
            //chatlink with text
            $widget['chatlink'] = "https://wa.me/" . $widget['phone_number'];
            if ($widget['widget_type'] == 2) {
                $widget_with_text = $widget['input_field_placeholder'];
                $widget['chatlink'] = "https://wa.me/" . $widget['phone_number'] . '?text=' . $widget_with_text;
            }
        }
        return response()->view('embedwhatsapp::dynamic_js', $widget)->header('Content-Type', 'application/javascript');
    }

    public function create()
    {
        return view('embedwhatsapp::create');
    }

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

    public function store(Request $request)
    {

        // Validate the request data
        $validatedData = $request->validate([
            'phone_number' => 'required',
            'header_text' => 'required',
            'header_subtext' => 'required',
            'widget_text' => 'required',
            'button_text' => 'required',
            'widget_type' => 'required',
            'button_color' => 'required',
            'header_color' => 'required'
        ]);

        // Create a new widget
        //Check for existing widget
        $widget = Whatsappwidget::where('company_id', auth()->user()->company_id)->first();
        if (!$widget) {
            $widget = new Whatsappwidget();
            $widget->id = $this->generateRandomString(10);
        }


        // Assign the validated data to the widget
        $widget->phone_number = $validatedData['phone_number'];
        $widget->header_text = $validatedData['header_text'];
        $widget->header_subtext = $validatedData['header_subtext'];
        $widget->widget_text = $validatedData['widget_text'];
        $widget->button_text = $validatedData['button_text'];
        $widget->widget_type = $validatedData['widget_type'];
        $widget->input_field_placeholder = $request->input_field_placeholder;
        $widget->button_color = $validatedData['button_color'];
        $widget->header_color = $validatedData['header_color'];
        $widget->company_id = auth()->user()->company->id;


        // Save the widgets
        $widget->save();

        //save the image
        if ($request->hasFile('logo')) {
            $widget->logo = $this->saveImageVersions(
                'uploads/companies/',
                $request->logo,
                [
                    ['name' => 'large'],
                ]
            );
            $widget->update();
        }

        // Redirect to a success page
        return redirect()->route('embedwhatsapp.edit')->with('status', 'Widget updated successfully');
    }

    public function show($id)
    {
        return view('embedwhatsapp::show');
    }

    public function edit()
    {
        //Find existing widget
        $widget = Whatsappwidget::where('company_id', auth()->user()->company_id)->first();
        if (!$widget) {
            $widget = [
                'logo' => '',
                'phone_number' => '',
                'header_text' => 'Your company or agent name',
                'header_subtext' => 'Online',
                'widget_text' => "Hi there 👋\nHow can I help you?",
                'button_text' => 'Start chat',
                'widget_type' => '1',
                'input_field_placeholder' => 'Enter your message',
                'button_color' => '#14c656',
                'header_color' => '#006654'
            ];
        } else {

            $id = $widget->getAttributes()['id'];
            $imageLink = $widget->getImageLinkAttribute();
            $widget = $widget->toArray();
            $widget['logo'] = $imageLink;
            $widget['url'] = config('app.url') . "/popup/whatsapp?id=" . $id;
        }

        return view('embedwhatsapp::edit', ['widget' => $widget]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
