@extends('FrontEnd.layouts.master')
@section('title') Edit -> {{ $pipeline->name }} @endsection
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />

@endsection
@section('css')
@endsection
@section('content')



@component('FrontEnd.components.breadcrumb') ✅

@slot('li_1') pipeline @endslot
        @slot('title') {{ $pipeline->name }} Details  @endslot
    @endcomponent

<div class="row">
    <div class="col-lg-10 mx-auto">

        <div class="card">
            <div class="card-header">
                <h4>Edit Pipeline – {{ $pipeline->name }}</h4>
            </div>

            <div class="card-body">

                <!-- FIXED FIRST BLOCK -->
                <div class="mb-3 p-3 border bg-light">
                    <label>New Lead (Fixed)</label>
                    <input type="text" class="form-control" value="New Lead" disabled>
                </div>

                <hr>

                <!-- ALL STAGES (SORTABLE) -->
                <h5>Pipeline Stages</h5>

                <div id="sortableStages">

                    @foreach($stages as $s)
                    <div class="stage-block mb-3 p-3 border bg-white" data-id="{{ $s->id }}">
                        <div class="row align-items-center">

                            <!-- Drag -->
                            <div class="col-md-1 text-center cursor-move">
                                <i class="ri-draggable" style="font-size: 22px;"></i>
                            </div>

                            <!-- Editable Name -->
                            <div class="col-md-8">
                                <input type="text" class="form-control stage-name"
                                       value="{{ $s->name }}">
                            </div>

                            <!-- Remove -->
                            <div class="col-md-3">
                                <button type="button" class="btn btn-danger removeStageBtn"
                                        data-id="{{ $s->id }}">
                                    Remove
                                </button>
                            </div>

                        </div>
                    </div>
                    @endforeach

                </div>

                <button class="btn btn-primary mt-2" id="addStageBtn">+ Add Stage</button>

                <hr>

                <!-- FINAL STAGE -->
                <div class="p-3 border bg-light">
                    <label>Final Stage</label>
                    <input type="text" id="final_stage" class="form-control"
                           value="{{ $finalStage->name ?? 'Closed' }}">
                </div>

                <button class="btn btn-success mt-4" id="savePipelineBtn">
                    Save Pipeline
                </button>

            </div>

        </div>

    </div>
</div>

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

@endsection
@section('script')
  

<script>
$(document).ready(function () {

    /////////////////////////////
    // SORTABLE
    /////////////////////////////
    if ($("#sortableStages").length) {
        $("#sortableStages").sortable({
            handle: ".cursor-move",
            placeholder: "ui-state-highlight"
        });
    }

    /////////////////////////////
    // ADD NEW STAGE
    /////////////////////////////
    $("#addStageBtn").click(function () {

        let block = `
            <div class="stage-block mb-3 p-3 border bg-white">
                <div class="row align-items-center">
                    <div class="col-md-1 text-center cursor-move">
                        <i class="ri-draggable" style="font-size: 22px;"></i>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control stage-name" placeholder="Stage Name">
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-danger removeStageBtn">Remove</button>
                    </div>
                </div>
            </div>
        `;

        $("#sortableStages").append(block);
    });

    /////////////////////////////
    // REMOVE STAGE
    /////////////////////////////
    $(document).on("click", ".removeStageBtn", function () {

        let id = $(this).data("id");

        if (id) {
            $.ajax({
                url: "/pipelines/" + id + "/stages",
                method: "DELETE",
                data: { _token: "{{ csrf_token() }}" },
            });
        }

        $(this).closest('.stage-block').remove();
    });

    /////////////////////////////
    // SAVE PIPELINE (AJAX)
    /////////////////////////////
    $("#savePipelineBtn").click(function () {

        let finalName = $("#final_stage").val();
        let stages = [];

        $("#sortableStages .stage-block").each(function () {
            stages.push({
                id: $(this).data("id"),
                name: $(this).find(".stage-name").val()
            });
        });

        $.ajax({
            url: "{{ route('pipelines.update', $pipeline->id) }}",
            method: "POST",
            data: {
                stages: stages,
                final_stage: finalName,
                _token: "{{ csrf_token() }}"
            },
            success: function () {
                Swal.fire("Success", "Pipeline saved!", "success");
            }
        });

    });

});
</script>


@endsection
