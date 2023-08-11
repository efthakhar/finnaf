<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->query('limit') && $request->query('limit') < 100 ? $request->query('limit') : 10;
        $visitors = Visitor::select('ip', 'count')->orderBy('id', 'desc')->paginate($limit);
        $total_site_visit = Visitor::sum('count');

        return response()->json([
            'data' => $visitors,
            'total_site_visit' => $total_site_visit,
        ]);
    }
}
