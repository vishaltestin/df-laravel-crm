@extends('FrontEnd.layouts.master')

@section('title', 'Landing Pages')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection


@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title')   Landing Pages @endslot
@endcomponent



<div class="row">
    <div class="col-lg-12">
        <div class="card" id="webformsList">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">
                    <div class="col-sm-3">
                        <div class="search-box">
                            <input type="text" class="form-control search" placeholder="Search for...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>

                    <div class="col-sm-auto ms-auto">
                        <div class="hstack gap-2">

        <a href="{{ route('landing-pages.create') }}" class="btn btn-primary">
            <i class="fa fa-plus"></i> Create New
        </a> </div>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive table-card">
                    <table class="table align-middle" id="webformTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="checkAll">
                                    </div>
                                </th>

                                <th class="sort" data-sort="name">Title</th>
                <th>Slug</th>
                <th>Layout</th>
                <th>Status</th>
                <th>Created</th>
                <th width="200">Actions</th>
          </tr>
                        </thead>

                        <tbody class="list form-check-all">
            @forelse($pages as $p)
            <tr data-id="{{ $p->id }}">

                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="chk_child">
                                    </div>
                                </td>

                                <td class="name"> {{ $p->title }}</td>
								<td> <a href="{{ url('/lp/'.$p->slug) }}" target="_blank">  /lp/{{ $p->slug }} </a>  </td>
								<td>{{ $p->layout_name }}</td>
								<td> @if($p->status)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Inactive</span>
                    @endif
                </td>

                <td>{{ $p->created_at->format('d M Y') }}</td>

                <td>

                                    <ul class="list-inline hstack gap-2 mb-0">
                               <li class="list-inline-item">
  <!-- Edit Landing Page (title, slug, layout, etc.) -->
                    <a href="{{ route('landing-pages.edit', $p->id) }}" 
                       class="btn btn-sm btn-warning">
                        <i class="fa fa-edit"></i> Edit
                    </a>
 </li>
                                              <li class="list-inline-item">
     <!-- Edit Page Sections -->
                    <a href="{{ route('landing-pages.sections', $p->id) }}" 
                       class="btn btn-sm btn-info">
                        <i class="fa fa-layer-group"></i> Sections
                    </a>
 </li>
                                              <li class="list-inline-item">
                    <!-- Delete -->
                    <form action="{{ route('landing-pages.destroy', $p->id) }}" 
                          method="POST" 
                          style="display:inline-block"
                          onsubmit="return confirm('Delete this page?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger">
Delete                        </button>
                    </form> </li>
                                              </ul>
                </td>

            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center">No landing pages found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
 <div class="noresult" style="display: none">
                                <div class="text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                        trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                        style="width:75px;height:75px">
                                    </lord-icon>
                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                    <p class="text-muted mb-0">We've searched more than 150+ leads We
                                        did not find any
                                        leads for you search.</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <div class="pagination-wrap hstack gap-2">
                                <a class="page-item pagination-prev disabled" href="#">
                                    Previous
                                </a>
                                <ul class="pagination listjs-pagination mb-0"></ul>
                                <a class="page-item pagination-next" href="#">
                                    Next
                                </a>
                            </div>
                        </div>
                    </div>
</div>


                    </div>
 
                </div>
            </div>

        </div>
        <!--end col-->
    </div>
    <!--end row-->

 @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>

    @endsection
