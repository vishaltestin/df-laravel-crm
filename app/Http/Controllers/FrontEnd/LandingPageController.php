<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use App\LandingPage;
use App\LandingPageSection;
use Illuminate\Http\Request;
use Sentinel;

class LandingPageController extends Controller
{
  public function edit($id)
{
    // role check
    if ($this->isOperator()) abort(403, 'Not allowed.');

    $page = LandingPage::findOrFail($id);

    return view('FrontEnd.landing.admin.edit', compact('page'));
}
public function updateSort(Request $req, $id)
{
    foreach ($req->order as $item) {
        LandingPageSection::where('id', $item['id'])
            ->update(['sort_order' => $item['sort']]);
    }

    return response()->json(['status' => 'success']);
}
public function sectionUpdate(Request $req, $id)
{
    LandingPageSection::where('id', $id)->update([
        'section_key' => $req->section_key,
        'content_json' => $req->content_json
    ]);

    return back()->with('success', 'Section updated');
}

public function update(Request $req, $id)
{
    $req->validate([
        'title' => 'required',
        'slug' => 'required',
        'layout_name' => 'required',
        'status' => 'required'
    ]);

    $page = LandingPage::findOrFail($id);

    $page->update([
        'title' => $req->title,
        'slug' => $req->slug,
        'layout_name' => $req->layout_name,
        'status' => $req->status
    ]);

    return redirect()->route('landing-pages.index')
        ->with('success', 'Landing Page Updated');
}
  // ---------------------------------------------------
    // ROLE HELPERS
    // ---------------------------------------------------

    private function getRole()
    {
        return Sentinel::check()->roles()->first()->slug ?? null;
    }

    private function getOrgId()
    {
        $user = Sentinel::check();
        $reg = registeruser($user->id);
        return $reg->organization;
    }

    private function isRoot()      { return $this->getRole() === 'root'; }
    private function isSadmin()    { return $this->getRole() === 'sadmin'; }
    private function isAdmin()     { return $this->getRole() === 'admin'; }
    private function isManager()   { return $this->getRole() === 'manager'; }
    private function isOperator()  { return $this->getRole() === 'operater'; }



    // ---------------------------------------------------
    // SHOW LANDING PAGE PUBLIC VIEW (NO restrictions)
    // ---------------------------------------------------
  public function render($slug)
{
    $page = LandingPage::where('slug', $slug)->firstOrFail();

    $sections = LandingPageSection::where('page_id', $page->id)
        ->orderBy('sort_order')
        ->get()
        ->map(function ($sec) {
            return [
                'key'     => $sec->section_key,
                'content' => json_decode($sec->content_json, true) ?? []
            ];
        })
        ->toArray();

    return view('FrontEnd.landing.design1', compact('page', 'sections'));
}





    // ---------------------------------------------------
    // ADMIN LANDING PAGE LISTING
    // ---------------------------------------------------
    public function index()
    {
        // role check
        if ($this->isOperator()) {
            abort(403, "Operators cannot access landing pages.");
        }

        $role  = $this->getRole();
        $orgId = $this->getOrgId();

        // ROOT / SADMIN → all pages
        if ($this->isRoot() || $this->isSadmin()) {
            $pages = LandingPage::orderBy('id', 'DESC')->get();
        }
        // ADMIN / MANAGER → only own organization
        elseif ($this->isAdmin() || $this->isManager()) {
            $pages = LandingPage::where('organization_id', $orgId)
                    ->orderBy('id', 'DESC')
                    ->get();
        }
        else {
            abort(403);
        }

		return view('FrontEnd.landing.admin.index', compact('pages'));
    }



    // ---------------------------------------------------
    // CREATE LANDING PAGE
    // ---------------------------------------------------
    public function store(Request $req)
    {
        $role  = $this->getRole();
        $orgId = $this->getOrgId();

        // permission rules:
        if ($this->isOperator()) abort(403, "Not allowed.");
        if ($this->isManager())  abort(403, "Managers cannot create landing pages.");

        // ROOT, SADMIN, ADMIN → can create
        if (!($this->isRoot() || $this->isSadmin() || $this->isAdmin())) {
            abort(403);
        }

        $req->validate([
            'title'       => 'required',
            'slug'        => 'required|unique:landing_pages',
            'layout_name' => 'required'
        ]);

        LandingPage::create([
            'organization_id' => $this->isRoot() || $this->isSadmin() ? ($req->organization_id ?? $orgId) : $orgId,
            'title'           => $req->title,
            'slug'            => $req->slug,
            'layout_name'     => $req->layout_name,
            'status'          => 1
        ]);

        return redirect()->route('landing-pages.index')
            ->with('success', 'Landing Page Created');
    }

	// ---------------------------------------------------
	// SHOW SECTION BUILDER PAGE
	// ---------------------------------------------------
	public function sectionList($id)
	{
		$page = LandingPage::findOrFail($id);
		$sections = $page->sections;

		return view('FrontEnd.landing.admin.sections', compact('page','sections'));
	}


	// ---------------------------------------------------
	// SAVE / UPDATE SECTION
	// ---------------------------------------------------
	public function sectionStore(Request $req, $id)
	{
		$req->validate([
			'section_key' => 'required',
			'content_json' => 'required',
			'sort_order' => 'required|numeric'
		]);

		LandingPageSection::updateOrCreate(
			[
				'page_id' => $id,
				'section_key' => $req->section_key
			],
			[
				'content_json' => $req->content_json,
				'sort_order' => $req->sort_order
			]
		);

		return back()->with('success', 'Section Saved');
	}


	// ---------------------------------------------------
	// DELETE A SECTION
	// ---------------------------------------------------
	public function sectionDelete($sid)
	{
		LandingPageSection::findOrFail($sid)->delete();
		return back()->with('success', 'Section Deleted');
	}

  public function create()
{
        return view('FrontEnd.landing.admin.create');
		}

}
