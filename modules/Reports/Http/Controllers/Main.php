<?php

namespace Modules\Reports\Http\Controllers;

use Akaunting\Module\Facade as Module;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class Main extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {

        // Scan all modules for reports
        $availableReports = [];

        foreach (Module::all() as $module) {
            if ($module->get('hasReports') && $module->get('reports')) {
                $reports = $module->get('reports');
                foreach ($reports as $report) {
                    $availableReports[] = [
                        'name' => $report['name'],
                        'view' => $report['view'],
                        'script' => $report['script'],
                        'module' => $module->get('alias'),
                        'module_name' => $module->get('name') ?? ucfirst($module->get('alias')),
                    ];
                }
            }
        }

        return view('reports::index', compact('availableReports'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        return view('reports::create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return view('reports::show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        return view('reports::edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
