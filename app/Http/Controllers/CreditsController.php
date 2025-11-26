<?php

namespace App\Http\Controllers;

use Akaunting\Module\Facade as Module;
use App\Models\Cost;
use App\Models\Credits;
use Illuminate\Http\Request;

class CreditsController extends Controller
{
    public function index()
    {
        //Use costs function to get the costs
        return $this->costs();
        return view('admin.credits.index', compact('costs'));
    }

    //costs
    public function costs()
    {
        //Get all the modules that define the costs per credit
        $modules = Module::all();
        
        $actions = [];
        foreach ($modules as $module) {
            //Check if the module has a costs per credit
            if ($module->get('cost_per_action')) {
                $actions[] = $module->get('cost_per_action');
            }
        }

        //Flatten the array
        $actions = collect($actions)->flatten(1)->toArray();

        //Get all the costs from the database
        $costs = Cost::all()->pluck('cost', 'action')->toArray();

        //-1 - means that the action is charged based on the usage of the action
        //>-1 - means that the action is charged a fixed amount

       


        //Now we need to loop through the actions and set the cost from the database if it exists, otherwise use the default cost from the environment file
        foreach ($actions as &$action) {
            $action['cost'] = $costs[$action['action']] ?? $action['default_cost'];
        }
        return view('credits.costs.costs', compact('actions'));
    }

    public function updateCosts(Request $request)
    {
        $data = $request->all();
        //Remove the _token
        unset($data['_token']);

        // Group the data by action
    $groupedCosts = [];
    foreach ($data as $key => $value) {
        // Split the key into action and field (type/cost)
        $parts = explode('_', $key);
        $field = array_pop($parts); // Get type or cost
        $action = implode('_', $parts); // Rejoin the action name

        $groupedCosts[$action][$field] = $value;
        }

        // Save or update costs in the database
        foreach ($groupedCosts as $action => $values) {
            $cost = Cost::where('action', $action)->first();
            if ($cost) {
                $cost->update([
                    'action' => $action,
                    'cost' => $values['type'] != "-1" ? $values['cost'] : -1,
                ]);
            } else {
                Cost::create([
                    'action' => $action,
                    'cost' => $values['type'] != "-1" ? $values['cost'] : -1,
                ]);
            }
        }

        //Redirect to the costs page
        return redirect()->route('credits.index')->with('success', 'Costs updated successfully');

        
    }
}
