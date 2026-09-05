@extends('FrontEnd.layouts.master')

@section('title', $page->title)

@section('content')
<div class="container">

    <h3 class="mb-3">Editing Sections for: {{ $page->title }}</h3>

    <form action="{{ route('landing-pages.sections.store', $page->id) }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-4">
                <label>Section Key</label>
                <input type="text" name="section_key" class="form-control" placeholder="hero, about, form" required>
            </div>

            <div class="col-md-4">
                <label>Sort Order</label>
                <input type="number" name="sort_order" class="form-control" required>
            </div>

            <div class="col-md-12 mt-3">
                <label>Content JSON</label>
                <textarea name="content_json" class="form-control" rows="6" required>
                {"title":"Welcome","subtitle":"Best Course","button_text":"Apply Now"}
                </textarea>
            </div>

            <div class="col-md-12 mt-3">
                <button class="btn btn-primary">Save Section</button>
            </div>
        </div>
    </form>

    <hr>

    <h4>Existing Sections</h4>
   <table class="table table-bordered" id="sortableTable">
    <thead>
        <tr>
            <th style="width:40px;">#</th>
            <th>Section Key</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="sortable">
        @foreach ($sections as $sec)
        <tr data-id="{{ $sec->id }}">
            <td class="handle" style="cursor:move;">☰</td>
            <td>{{ $sec->section_key }}</td>
            <td>
                <button class="btn btn-sm btn-warning editBtn"
                        data-id="{{ $sec->id }}"
    data-content="{{ base64_encode($sec->content_json) }}" 
	data-key="{{ $sec->section_key }}">
                    Edit
                </button>

                <form action="{{ route('landing-pages.sections.delete', $sec->id) }}"
                    method="POST" style="display:inline-block;">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="editForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Section</h5>
                </div>

                <div class="modal-body">
                    <label>Section Key</label>
                    <input type="text" name="section_key" id="edit_key" class="form-control">

                    <label class="mt-3">Content JSON</label>
                    <textarea name="content_json" id="edit_json" rows="6" class="form-control"></textarea>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                </div>
            </div>
        </form>
    </div>
</div>

</div>
@endsection


{{-- SCRIPTS --}}
@section('script')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- jQuery UI (drag-drop) --}}
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
$('.editBtn').click(function () {

    let id = $(this).data('id');
    let key = $(this).data('key');
    
    // Base64 decode JSON content
    let encoded = $(this).data('content');
    let jsonString = atob(encoded); // decode
    let jsonPretty = JSON.stringify(JSON.parse(jsonString), null, 4); // pretty print

    // Fill modal fields
    $('#edit_key').val(key);
    $('#edit_json').val(jsonPretty);

    // Dynamic action URL
    let actionUrl = "{{ route('landing-pages.sections.update', ':id') }}".replace(':id', id);
    $('#editForm').attr('action', actionUrl);

    $('#editModal').modal('show');
});

</script>
 
<script> 
$(function() {
    $("#sortable").sortable({
        handle: '.handle',
        update: function(event, ui) {
            let order = [];
            $("#sortable tr").each(function(index, element) {
                order.push({
                    id: $(this).data("id"),
                    sort: index + 1
                });
            });

            $.ajax({
                url: "{{ route('landing-pages.sort.update', $page->id) }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    order: order
                },
                success: function(res) {
                    console.log("Sort order updated");
                }
            });
        }
    });
});
</script>
@endsection
