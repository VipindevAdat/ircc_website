<?php

namespace App\Http\Controllers\Admin;
use App\Exports\ExportRegistration;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    public function exportRegistration(Request $request){
        if($request->type=='abstract'){
            $fileName = 'abstract-' . date('Y-m-d h:m:s') . '.xls';  
        }
        else{
            $fileName = 'registrations-' . date('Y-m-d h:m:s') . '.xls';
        }
        
        return Excel::download(new ExportRegistration($request), $fileName);

    }
}
