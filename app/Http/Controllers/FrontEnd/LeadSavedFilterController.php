<?php

namespace App\Http\Controllers\FrontEnd;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Sentinel;
use App\LeadSavedFilter;

class LeadSavedFilterController extends Controller
{
    public function list(Request $request)
    {
        $user = Auth::user();

        $filters = LeadSavedFilter::query()
            ->where('institute_id', $user->institute_id)
            ->where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->orderBy('name')
            ->get(['id','name','is_default']);

        return response()->json([
            'status' => true,
            'data' => $filters
        ]);
    }

   public function store(Request $request)
{
    $user = Sentinel::check();
    $reg  = registeruser($user->id);
    $orgId = $reg->organization;

    $request->validate([
        'name' => 'required|string|max:150',
        'filters' => 'nullable|array',
        'columns' => 'nullable|array',
        'sort' => 'nullable|array',
    ]);

   $saved = LeadSavedFilter::create([
    'organization_id' => $orgId,
    'user_id' => $user->id,
    'name' => $request->name,
    'filters_json' => $request->filters ?? [],
    'columns_json' => $request->columns ?? [],
    'sort_json' => $request->sort ?? [],
    'is_default' => 0,
]);


    return response()->json([
        'status' => true,
        'message' => 'Filter saved successfully',
        'data' => $saved
    ]);
}

    public function delete($id)
    {
        $user = Auth::user();

        $filter = LeadSavedFilter::query()
            ->where('institute_id', $user->institute_id)
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        $filter->delete();

        return response()->json([
            'status' => true,
            'message' => 'Filter deleted successfully'
        ]);
    }

    public function makeDefault($id)
    {
        $user = Auth::user();

        // remove old default
        LeadSavedFilter::query()
            ->where('institute_id', $user->institute_id)
            ->where('user_id', $user->id)
            ->update(['is_default' => 0]);

        // set new default
        LeadSavedFilter::query()
            ->where('institute_id', $user->institute_id)
            ->where('user_id', $user->id)
            ->where('id', $id)
            ->update(['is_default' => 1]);

        return response()->json([
            'status' => true,
            'message' => 'Default filter set successfully'
        ]);
    }
}
