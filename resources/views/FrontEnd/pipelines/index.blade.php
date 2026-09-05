@extends('FrontEnd.layouts.master')
@section('title', 'Pipelines')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

{{-- TRENDY UI COLORS & ANIMATIONS --}}
<style>
    /* Stage Card */
    .stage-block {
        transition: 0.2s ease-in-out;
        border-radius: 12px;
        border-left: 6px solid #5b99ff;
        background: #ffffff;
    }

    .stage-block:hover {
        transform: scale(1.03);
        box-shadow: 0 4px 15px rgba(0,0,0,0.18);
    }

    .cursor-move {
        cursor: grab;
        color: #2a60ff;
        font-size: 22px;
    }

    .ui-sortable-helper {
        background: #e8f1ff !important;
        transform: rotate(1deg);
    }

    .ui-state-highlight {
        height: 55px;
        border-radius: 12px;
        border: 2px dashed #0d6efd;
        background: #f1f7ff;
    }

    /* Add Stage Button */
    #addStageBtn {
        border-radius: 25px;
        padding: 8px 20px;
    }

    /* Modal Stylish Header */
    .modal-header {
        border-bottom: 0;
        background: #f0f6ff;
    }
</style>

@endsection

@section('content')

@component('FrontEnd.components.breadcrumb')
    @slot('li_1') CRM @endslot
    @slot('title') Pipelines @endslot
@endcomponent


<div class="row">
    <div class="col-lg-12">

        <div class="card">
            <div class="card-header border-0">
                <div class="row g-4 align-items-center">

                    <div class="col-sm-3">
                        <div class="search-box">
                            <input type="text" class="form-control search" placeholder="Search Pipeline...">
                            <i class="ri-search-line search-icon"></i>
                        </div>
                    </div>

                    <div class="col-sm-auto ms-auto">
                        <button type="button" class="btn btn-success" id="addNewPipelines">
                            <i class="ri-add-line me-1"></i> Add Pipeline
                        </button>
                    </div>

                </div>
            </div>

            <div class="card-body">

                <div class="table-responsive table-card">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Rotten Days</th>
                                <th>Default</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody class="list">

                            @foreach($pipelines as $pipeline)
                            <tr>
                                <td>{{ $pipeline->name }}</td>
                                <td>{{ $pipeline->rotten_days }}</td>
                                <td>{{ $pipeline->is_default ? 'Yes' : 'No' }}</td>
                                <td>{{ $pipeline->created_at }}</td>

                                <td>
                                    <button class="btn btn-primary btn-sm editPipelineBtn"
                                            data-id="{{ $pipeline->id }}">
                                        <i class="ri-pencil-fill"></i>
                                    </button>

                                    <button class="btn btn-danger btn-sm deletePipelineBtn"
                                            data-id="{{ $pipeline->id }}">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>
</div>


{{-- -------- ADD NEW PIPELINE MODAL -------- --}}
<div class="modal fade" id="addPipelineModal" tabindex="-1">
  <div class="modal-dialog">
      <div class="modal-content">

          <div class="modal-header">
              <h5 class="modal-title">Add New Pipeline</h5>
              <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">

              <label class="form-label">Pipeline Name</label>
              <input type="text" id="pipeline_name" class="form-control mb-3">

              <label class="form-label">Rotten Days</label>
              <input type="number" id="rotten_days" class="form-control" value="15">

          </div>

          <div class="modal-footer">
              <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
              <button class="btn btn-success" id="savePipeline">Save</button>
          </div>

      </div>
  </div>
</div>



{{-- -------- MODERN PIPELINE BUILDER POPUP -------- --}}
<div class="modal fade" id="pipelineModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Pipeline Builder</h4>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <input type="hidden" id="edit_pipeline_id">

                <div class="mb-3 p-3 bg-light rounded">
                    <label class="form-label">Fixed First Stage</label>
                    <input type="text" class="form-control" value="New Lead" disabled>
                </div>

                <hr>

                <h5 class="mb-2">Pipeline Stages</h5>

                <div id="sortableStages"></div>

                <button class="btn btn-primary mt-2" id="addStageBtn">
                    <i class="ri-add-line"></i> Add Stage
                </button>

                <hr>

                <label class="form-label">Final Stage</label>
                <input type="text" id="final_stage" class="form-control">

            </div>

            <div class="modal-footer">
                <button class="btn btn-success" id="savePipelineBtn">
                    <i class="ri-check-fill"></i> Save Changes
                </button>
            </div>

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


<script>
$(document).ready(function(){

    // ---------------- ADD NEW PIPELINE ----------------
    $("#addNewPipelines").click(() => $("#addPipelineModal").modal('show'));

    $("#savePipeline").click(function(){

        let name = $("#pipeline_name").val().trim();
        let rotten = $("#rotten_days").val();

        if(name === "") return Swal.fire("Error", "Enter Pipeline Name", "error");

        $.post("{{ route('pipelines.store') }}", {
            name: name,
            rotten_days: rotten,
            _token: "{{ csrf_token() }}"
        }, function(res){
            Swal.fire("Saved!", "Pipeline created!", "success");
            setTimeout(()=> location.reload(), 700);
        });

    });



    // ---------------- EDIT PIPELINE ----------------
    $(document).on("click", ".editPipelineBtn", function(){

        let id = $(this).data("id");
        $("#edit_pipeline_id").val(id);

        $("#sortableStages").html("");

        let url = "{{ route('pipelines.editData', ':id') }}".replace(':id', id);

        $.get(url, function(res){

            res.stages.forEach(stage => {

                $("#sortableStages").append(`
                    <div class="stage-block p-3 mb-3" data-id="${stage.id}">
                        <div class="row">
                            <div class="col-md-1 cursor-move text-center">
                                <i class="ri-draggable"></i>
                            </div>
                            <div class="col-md-8">
                                <input type="text" class="form-control stage-name" value="${stage.name}">
                            </div>
                            <div class="col-md-3 text-end">
                                <button class="btn btn-danger removeStageBtn" data-id="${stage.id}">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                `);

            });

            $("#final_stage").val(res.final_stage);

            setTimeout(()=>{
                $("#sortableStages").sortable({
                    handle: ".cursor-move",
                    placeholder: "ui-state-highlight"
                });
            },150);

            $("#pipelineModal").modal("show");

        });

    });



    // ---------------- ADD STAGE ----------------
    $("#addStageBtn").click(function(){

        $("#sortableStages").append(`
            <div class="stage-block p-3 mb-3">
                <div class="row">
                    <div class="col-md-1 cursor-move text-center">
                        <i class="ri-draggable"></i>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control stage-name" placeholder="Stage Name">
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-danger removeStageBtn">Remove</button>
                    </div>
                </div>
            </div>
        `);

    });



    // ---------------- REMOVE STAGE ----------------
    $(document).on("click", ".removeStageBtn", function(){

        let id = $(this).data("id");

        if(id){
            let delURL = "{{ route('pipeline-stages.delete', ':id') }}".replace(':id', id);
            $.ajax({ url: delURL, method: "DELETE", data:{ _token:"{{ csrf_token() }}" } });
        }

        $(this).closest(".stage-block").remove();
    });



    // ---------------- SAVE PIPELINE BUILDER ----------------
    $("#savePipelineBtn").click(function(){

        let id = $("#edit_pipeline_id").val();
        let final = $("#final_stage").val();

        let stages = [];

        $("#sortableStages .stage-block").each(function(){
            stages.push({
                id: $(this).data("id"),
                name: $(this).find(".stage-name").val()
            });
        });

        let updateURL = "{{ route('pipelines.update', ':id') }}".replace(':id', id);

        $.post(updateURL, {
            stages: stages,
            final_stage: final,
            _token: "{{ csrf_token() }}"
        }, function(){
            Swal.fire("Updated!", "Pipeline saved successfully!", "success");
            setTimeout(()=> location.reload(), 700);
        });

    });



    // ---------------- DELETE PIPELINE ----------------
    $(document).on("click", ".deletePipelineBtn", function(){

        let id = $(this).data("id");

        Swal.fire({
            title: "Delete?",
            text: "Pipeline will be removed completely!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#e3342f",
            confirmButtonText: "Delete"
        })
        .then(result=>{
            if(result.isConfirmed){

                let url = "{{ route('pipelines.delete', ':id') }}".replace(':id', id);

                $.ajax({
                    url: url,
                    method: "DELETE",
                    data: { _token:"{{ csrf_token() }}" },
                    success:()=>{
                        Swal.fire("Deleted!", "Pipeline removed.", "success");
                        setTimeout(()=> location.reload(), 600);
                    }
                });

            }
        });

    });


});
</script>

@endsection
