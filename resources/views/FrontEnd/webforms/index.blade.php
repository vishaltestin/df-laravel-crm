@extends('FrontEnd.layouts.master')

@section('title', 'Web Forms')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection


@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Web Forms @endslot
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

                            <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()">
                                <i class="ri-delete-bin-2-line"></i>
                            </button>

                            <a href="{{ route('webforms.create') }}" class="btn btn-primary">+ Add Web Form</a>
                        </div>
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
                                <th>Form ID</th>
                                <th>Views</th>
                                <th>Submissions</th>
                                <th>Conversion</th>
                                <th>Create Lead</th>
                                <th width="180">Action</th>
                            </tr>
                        </thead>

                        <tbody class="list form-check-all">
                            @foreach($forms as $f)
                            <tr data-id="{{ $f->id }}">

                                <td>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="chk_child">
                                    </div>
                                </td>

                                <td class="name">{{ $f->title }}</td>
                                <td>{{ $f->form_id }}</td>
                                <td>{{ $f->view_count }}</td>
                                <td>{{ $f->submit_count }}</td>

                                <td>
                                    {{ $f->view_count > 0 ? round(($f->submit_count / $f->view_count) * 100) : 0 }}%
                                </td>

                                <td>{{ $f->create_lead ? 'Yes' : 'No' }}</td>

                                <td>

                                    <ul class="list-inline hstack gap-2 mb-0">

                                        {{-- GET EMBED CODE BUTTON --}}
                                        <li class="list-inline-item">
                                            <button class="btn btn-sm btn-info" onclick="getEmbedCode({{ $f->id }})">
                                                Embed Code
                                            </button>
                                        </li>

                                        {{-- VIEW FORM --}}
                                        <li class="list-inline-item">
                                            <a href="{{ route('webform.public', $f->form_id) }}" 
                                               target="_blank" 
                                               class="btn btn-sm">
                                                <i class="ri-eye-fill text-muted"></i>
                                            </a>
                                        </li>

                                        {{-- EDIT --}}
                                        <li class="list-inline-item">
                                            <a href="{{ url('webforms/'.$f->id.'/edit') }}" class="btn btn-sm">
                                                <i class="ri-pencil-ruler-2-line text-muted"></i>
                                            </a>
                                        </li>

                                        {{-- DELETE --}}
                                        <li class="list-inline-item">
                                            <form action="{{ url('webforms/'.$f->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-danger">
                                                    <i class="ri-delete-bin-2-fill"></i>
                                                </button>
                                            </form>
                                        </li>

                                    </ul>

                                </td>

                            </tr>
                            @endforeach
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


{{-- ===========================
     EMBED CODE MODAL
=========================== --}}
<div class="modal fade" id="embedModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title">Embed Code</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <label>Copy & paste this code in any website:</label>

        <textarea id="embedCodeBox" class="form-control" rows="6"></textarea>

        <button class="btn btn-primary mt-3" onclick="copyEmbedCode()">
            Copy Code
        </button>
      </div>

    </div>
  </div>
</div>

   <!--end row-->

 @endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
   		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   	    <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>

    


 
 
<script>
const embedRoute = "{{ url('/webforms') }}";

// ============================
// GET EMBED CODE
// ============================
function getEmbedCode(id) {
    fetch(`${embedRoute}/${id}/embed-code`)
        .then(res => res.json())
        .then(data => {

            document.getElementById("embedCodeBox").value = data.embed_code;

            var modal = new bootstrap.Modal(document.getElementById('embedModal'));
            modal.show();
        });
}

// ============================
// COPY CODE
// ============================
function copyEmbedCode() {
    let box = document.getElementById("embedCodeBox");

    box.select();
    box.setSelectionRange(0, 99999);

    navigator.clipboard.writeText(box.value)
        .then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Embed code copied to clipboard.',
                timer: 1500
            });
        });
}
</script>

@endsection
