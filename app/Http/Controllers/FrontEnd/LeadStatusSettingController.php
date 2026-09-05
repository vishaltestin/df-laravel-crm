<?php
namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\LeadStatus;
use App\LeadSubStatus;
use Illuminate\Http\Request;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;

class LeadStatusSettingController extends Controller
{
    protected function orgId()
    {
        $user = Sentinel::check();
        $reg  = registeruser($user->id); // आपका helper
        return $reg->organization ?? null;
    }

    // ✅ Main Lead Status LISTING + settings page
    public function index()
    {
        $orgId = $this->orgId();

        $statuses = LeadStatus::where(function($q) use ($orgId) {
                            $q->where('organization_id', $orgId)
                              ->orWhereNull('organization_id'); // global default fallback
                        })
                        ->where('is_active',1)
                        ->with(['subStatuses' => function($q) use ($orgId) {
                            $q->where('is_active',1)->orderBy('sort_order');
                        }])
                        ->orderBy('sort_order')
                        ->get();

        return view('FrontEnd.lead-settings.statuses', compact('statuses'));
    }

    // ✅ Main Lead Status Add/Save
    public function storeStatus(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        LeadStatus::create([
            'organization_id' => $this->orgId(),
            'name'            => $request->name,
            'color'           => $request->color,
            'sort_order'      => $request->sort_order ?? 0,
            'is_active'       => 1,
        ]);

        return back()->with('success','Lead Status added successfully');
    }

    // ✅ Main Lead Status Update
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $status = LeadStatus::findOrFail($id);
        $status->update([
            'name'       => $request->name,
            'color'      => $request->color,
            'sort_order' => $request->sort_order ?? $status->sort_order,
            'is_active'  => $request->is_active ?? 1,
        ]);

        return back()->with('success','Lead Status updated');
    }

    // ✅ Main Lead Status Delete
    public function deleteStatus($id)
    {
        LeadStatus::where('id',$id)->delete();
        return back()->with('success','Lead Status deleted');
    }

    // ✅ Sub Lead Status Add/Save (Under main status)
    public function storeSubStatus(Request $request, $statusId)
    {
        $request->validate(['name' => 'required|string|max:255']);

        LeadSubStatus::create([
            'lead_status_id'  => $statusId,
            'organization_id' => $this->orgId(),
            'name'            => $request->name,
            'sort_order'      => $request->sort_order ?? 0,
            'is_active'       => 1,
        ]);

        return back()->with('success','Lead Sub Status added');
    }

    // ✅ Sub Lead Status Update
    public function updateSubStatus(Request $request, $id)
    {
        $request->validate(['name'=>'required|string|max:255']);
        $sub = LeadSubStatus::findOrFail($id);
        $sub->update([
            'name'        => $request->name,
            'sort_order'  => $request->sort_order ?? $sub->sort_order,
            'is_active'   => $request->is_active ?? 1,
        ]);

        return back()->with('success','Lead Sub Status updated');
    }

    // ✅ Sub Lead Status Delete
    public function deleteSubStatus($id)
    {
        LeadSubStatus::where('id',$id)->delete();
        return back()->with('success','Lead Sub Status deleted');
    }

    // ✅ AJAX – जब Main Status select हो तो उससे जुड़े sub options भेजे
    public function getSubStatusesAjax(Request $request)
    {
        $request->validate(['status_id'=>'required']);

        $subStatuses = LeadSubStatus::where('lead_status_id',$request->status_id)
                        ->where(function($q){
                           $q->where('organization_id',$this->orgId())
                             ->orWhereNull('organization_id');
                        })
                        ->where('is_active',1)
                        ->orderBy('sort_order')
                        ->get();

        $html = '<option value=\"\">Lead Sub Stage</option>';
        foreach($subStatuses as $sub){
            $html .= '<option value=\"'.$sub->id.'\">'.$sub->name.'</option>';
        }

        return response()->json(['options'=>$html]);
    }

	public function subStatusIndex()
	{
		$orgId = $this->orgId();
		$subStatuses = LeadSubStatus::where(function($q) use ($orgId){
							$q->where('organization_id',$orgId)
							  ->orWhereNull('organization_id');
						})
						->with('status')
						->orderBy('sort_order')
						->get();

		$statuses = LeadStatus::where(function($q) use ($orgId){
							$q->where('organization_id',$orgId)
							  ->orWhereNull('organization_id');
						})
						->where('is_active',1)
						->orderBy('sort_order')
						->get();

		return view('FrontEnd.lead-settings.sub-statuses', compact('subStatuses','statuses'));
	}


}
