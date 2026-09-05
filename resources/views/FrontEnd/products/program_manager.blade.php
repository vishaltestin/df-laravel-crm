@extends('layouts.master')

@section('content')

<div class="container mt-4">
    <h3 class="mb-3">Program Manager</h3>

    <!-- ADD BUTTON -->
    <button class="btn btn-primary mb-3" id="addProgramBtn">+ Add Program</button>

    <!-- PROGRAM TABLE -->
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Institute</th>
                <th>Program Name</th>
                <th>Description</th>
                <th width="120">Actions</th>
            </tr>
        </thead>
        <tbody id="programTable">
            @foreach($programs as $p)
            <tr id="row_{{ $p->id }}">
                <td>{{ $p->institute->name }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->description }}</td>
                <td>
                    <button class="btn btn-sm btn-info editBtn" data-id="{{ $p->id }}">Edit</button>
                    <button class="btn btn-sm btn-danger deleteBtn" data-id="{{ $p->id }}">Delete</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


<!-- ADD/EDIT MODAL -->
<div class="modal fade" id="programModal">
    <div class="modal-dialog">
        <div class="modal-content">

            <form id="programForm">

                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">Add Program</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <input type="hidden" id="program_id">

                    <label>Institute *</label>
                    <select class="form-select" name="institute_id" id="institute_id" required>
                        <option value="">Select Institute</option>
                        @foreach($institutes as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                        @endforeach
                    </select>

                    <label class="mt-3">Program Name *</label>
                    <input type="text" class="form-control" name="name" id="name" required>

                    <label class="mt-3">Description</label>
                    <textarea class="form-control" name="description" id="description"></textarea>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(function(){

    // OPEN ADD MODAL
    $("#addProgramBtn").click(function(){
        $("#programForm")[0].reset();
        $("#program_id").val("");
        $(".modal-title").text("Add Program");
        $("#programModal").modal("show");
    });

    // EDIT PROGRAM
    $(document).on("click",".editBtn",function(){
        let id = $(this).data("id");

        $.get("program/" + id, function(res){
            $("#program_id").val(res.id);
            $("#institute_id").val(res.institute_id);
            $("#name").val(res.name);
            $("#description").val(res.description);

            $(".modal-title").text("Edit Program");
            $("#programModal").modal("show");
        });
    });

    // SAVE PROGRAM (ADD / UPDATE)
    $("#programForm").submit(function(e){
        e.preventDefault();

        let id = $("#program_id").val();
        let url = id ? "program/update/" + id : "program/store";

        $.post(url, $(this).serialize(), function(res){
            location.reload();
        }).fail(function(xhr){
            alert("Error: " + xhr.responseJSON.message);
        });

    });

    // DELETE PROGRAM
    $(document).on("click",".deleteBtn",function(){
        if(!confirm("Delete this program?")) return;

        let id = $(this).data("id");

        $.post("program/delete/" + id, {_token:"{{ csrf_token() }}"}, function(){
            $("#row_" + id).remove();
        });
    });

});
</script>

@endsection
