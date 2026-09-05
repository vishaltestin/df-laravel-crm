<?php

namespace App\Http\Controllers\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Product;
use App\ProductInventory;
use Sentinel;

class ProductController extends Controller
{
    // ============================================
    // GET USER + ROLE + ORG (COMMON HELPER)
    // ============================================
    private function userContext()
    {
        $u = \Sentinel::check();
        $reg = registeruser($u->id);

        return (object)[
            'user'  => $u,
            'role'  => $u->roles()->first()->slug ?? null,
            'orgId' => $reg->organization
        ];
    }

    private function allow(...$allowed)
    {
        $ctx = $this->userContext();

        if (!in_array($ctx->role, $allowed)) {
            abort(403, 'Unauthorized Action');
        }
    }

    // =======================================================
    // INDEX — ROLE BASED VIEW
    // =======================================================
    public function index()
    {
        $ctx = $this->userContext();

        // root / sadmin -> view all products
        if (in_array($ctx->role, ['root', 'sadmin'])) {
            $products = Product::with('program')
                        ->orderBy('id','DESC')
                        ->get();
        } 
        else {
            // admin / manager / operater -> only own org products
            $products = Product::with('program')
                        ->where('organization_id', $ctx->orgId)
                        ->orderBy('id','DESC')
                        ->get();
        }

        return view('FrontEnd.products.index', [
            'products' => $products,
            'orgId'    => $ctx->orgId
        ]);
    }

    // =======================================================
    // CREATE FORM (Only root, sadmin, admin)
    // =======================================================
    public function create()
    {
        $this->allow('root', 'sadmin', 'admin');

        return view('FrontEnd.products.create');
    }

    // =======================================================
    // STORE PRODUCT
    // =======================================================
    public function store(Request $request)
    {
        $ctx = $this->userContext();
        $this->allow('root', 'sadmin', 'admin');

        $request->validate([
            'sku'      => 'required|unique:products',
            'name'     => 'required',
            'price'    => 'required|numeric',
            'quantity' => 'required|numeric',
        ]);

        // root/sadmin → organization_id लगेगा UI से
        // admin → हमेशा अपने org का
        $orgId = ($ctx->role == 'admin') ? $ctx->orgId : ($request->organization_id ?? $ctx->orgId);

        $product = Product::create([
            'sku'             => $request->sku,
            'name'            => $request->name,
            'description'     => $request->description,
            'quantity'        => $request->quantity,
            'price'           => $request->price,
            'organization_id' => $orgId,
            'program_id'      => $request->program_id ?? null,
        ]);

        ProductInventory::create([
            'product_id' => $product->id,
            'in_stock'   => $request->quantity,
            'allocated'  => 0
        ]);

        return response()->json(['status'=>'success','data'=>'Saved Succeffully']);
    }

    // =======================================================
    // EDIT FORM (root, sadmin, admin, manager)
    // =======================================================
    public function edit($id)
    {
        $ctx = $this->userContext();
        $product = Product::findOrFail($id);

        if ($ctx->role === 'root' || $ctx->role === 'sadmin') {
            // full access
        } 
        elseif (in_array($ctx->role, ['admin', 'manager'])) {
            if ($product->organization_id != $ctx->orgId) {
                abort(403, 'Unauthorized Access');
            }
        } 
        else {
            abort(403, 'Unauthorized');
        }

        return view('FrontEnd.products.edit', compact('product'));
    }

    // =======================================================
    // UPDATE PRODUCT (root, sadmin, admin, manager)
    // =======================================================
    public function update(Request $request, $id)
    {
        $ctx = $this->userContext();
        $product = Product::findOrFail($id);

        // manager cannot modify other org
        if ($ctx->role == 'manager' && $product->organization_id != $ctx->orgId) {
            abort(403, 'Unauthorized');
        }

        // operater cannot update
        if ($ctx->role == 'operater') {
            abort(403, 'Unauthorized');
        }

        $oldData = $product->toArray();

        $product->update([
            'sku'          => $request->sku,
            'name'         => $request->name,
            'description'  => $request->description,
            'quantity'     => $request->quantity,
            'price'        => $request->price,
            'program_id'   => $request->program_id ?? null,
        ]);

        $product->inventory->update([
            'in_stock' => $request->quantity,
        ]);

        return response()->json(['status'=>'success','data'=>'Updated Succeffully']);
    }

    // =======================================================
    // DELETE PRODUCT – only root + admin
    // =======================================================
    public function destroy($id)
    {
        $ctx = $this->userContext();
        $product = Product::findOrFail($id);

        // only root or admin (own organization)
        if ($ctx->role === 'root') {
            // allow delete
        } 
        elseif ($ctx->role === 'admin') {
            if ($product->organization_id != $ctx->orgId) {
                abort(403, 'Unauthorized');
            }
        } 
        else {
            abort(403, 'Unauthorized — You cannot delete');
        }

        $product->delete();

        return response()->json(['status' => 'deleted']);
    }
}
