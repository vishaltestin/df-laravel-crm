@extends('FrontEnd.layouts.master')
@section('title', 'Lead Distribution Rules')

@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')

    @component('FrontEnd.components.breadcrumb')
        @slot('li_1') CRM @endslot
        @slot('title') Lead Distribution @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="ruleList">
                <div class="card-header border-0">
                    <div class="row g-4 align-items-center">

                        <div class="col-sm-3">
                            <div class="search-box">
                                <input type="text" class="form-control search" placeholder="Search rules...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>

                        <div class="col-sm-auto ms-auto">
                            <div class="hstack gap-2">
                               <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()">
                                    <i class="ri-delete-bin-2-line"></i>
                                </button>

                                <button class="btn btn-info" id="openSimBtn">
                                    Test Lead Assignment
                                </button>

                                <button class="btn btn-primary" id="addRuleBtn">
                                    + Add Rule
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="card-body">
                    <div>
                        <div class="table-responsive table-card">
                            <table class="table align-middle" id="ruleTable">
                                <thead class="table-light">
                                <tr>
                                    <th scope="col" style="width: 50px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll">
                                        </div>
                                    </th>
                                    <th>Rule Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th width="180px">Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($rules as $r)
                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="chk_child">
                                            </div>
                                        </th>

                                        <td>{{ $r->rule_name }}</td>
                                        <td>{{ ucfirst($r->distribution_type) }}</td>
                                        <td>
                                            @if($r->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $r->created_at ? $r->created_at->format('d-m-Y H:i') : '' }}</td>

                                        <td>
                                           <button class="btn btn-warning btn-sm editRule" data-id="{{ $r->id }}">
                                                Edit
                                            </button>

                                            <button class="btn btn-danger btn-sm deleteRule" data-id="{{ $r->id }}">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No rules configured.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>

                            <div class="noresult" style="display: none">
                                <div class="text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                               trigger="loop"
                                               colors="primary:#121331,secondary:#08a88a"
                                               style="width:75px;height:75px">
                                    </lord-icon>
                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                    <p class="text-muted mb-0">We did not find any rule matching your search.</p>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <div class="pagination-wrap hstack gap-2">
                                <a class="page-item pagination-prev disabled" href="#">Previous</a>
                                <ul class="pagination listjs-pagination mb-0"></ul>
                                <a class="page-item pagination-next" href="#">Next</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @include('FrontEnd.lead_distribution.modal')
    @include('FrontEnd.lead_distribution.test_modal')

@endsection
@section('script')
<script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
<script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    // ---------------------------------------------
    // CSRF Token auto for all AJAX
    // ---------------------------------------------
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
        }
    });

    // Backend se aayi lists (simple arrays ban jayenge)
    const LD_SOURCES = @json($sources);
    const LD_TEAMS = @json($teams);
    const LD_USERS = @json($users);
    const LD_DESIGNATIONS = @json($designations);

    let ldRowIndex = 0;

    function ldToggleSections() {
        let type = $("#distribution_type").val();
        if (type === "random") {
            $("#randomSection").show();
            $("#sourceSection").hide();
        } else {
            $("#randomSection").hide();
            $("#sourceSection").show();
        }
    }

    // Assign dropdown (User/Team/Designation) options change
    function ldUpdateAssignOptions($row, assignType, selectedId = null) {
        let list = [];
        if (assignType === 'user') {
            list = LD_USERS;
        } else if (assignType === 'team') {
            list = LD_TEAMS;
        } else if (assignType === 'designation') {
            list = LD_DESIGNATIONS;
        }

        let html = '<option value="">Select</option>';
        list.forEach(function (item) {
            html += `<option value="${item.id}">${item.name}</option>`;
        });

        let $select = $row.find(".ld-assign-id");
        $select.html(html);

        if (selectedId) {
            $select.val(selectedId);
        }
    }

    // Naya source mapping row add karo
    function ldAddSourceRow(item = null) {
        let idx = ldRowIndex++;
        let rowHtml = `
            <tr class="ld-row" data-index="${idx}">
                <td>
                    <select class="form-select ld-source" name="items[${idx}][source_id]">
                        ${LD_SOURCES.map(s => `<option value="${s.id}">${s.name}</option>`).join('')}
                    </select>
                </td>
                <td>
                    <select class="form-select ld-assign-type" name="items[${idx}][assign_type]">
                        <option value="user">User</option>
                        <option value="team">Team</option>
                        <option value="designation">Designation</option>
                    </select>
                </td>
                <td>
                    <select class="form-select ld-assign-id" name="items[${idx}][assign_id]">
                        <option value="">Select</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control" name="items[${idx}][percentage]" min="0" max="100"
                           value="${item && item.percentage ? item.percentage : ''}">
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-link text-danger ld-remove-row">Remove</button>
                </td>
            </tr>
        `;

        $("#ldSourceTableBody").append(rowHtml);
        let $row = $("#ldSourceTableBody").find(`tr[data-index="${idx}"]`);

        // Set defaults from item
        if (item) {
            $row.find(".ld-source").val(item.source_id);
            $row.find(".ld-assign-type").val(item.assign_type);
            ldUpdateAssignOptions($row, item.assign_type, item.assign_id);
        } else {
            ldUpdateAssignOptions($row, 'user', null);
        }
    }

    // Assign type change per row
    $(document).on("change", ".ld-assign-type", function () {
        let $row = $(this).closest("tr.ld-row");
        let type = $(this).val();
        ldUpdateAssignOptions($row, type, null);
    });

    // Remove row
    $(document).on("click", ".ld-remove-row", function () {
        $(this).closest("tr.ld-row").remove();
    });

    // Add source row button
    $("#addSourceRowBtn").on("click", function () {
        ldAddSourceRow();
    });

    // ---------------------------------------------
    // OPEN CREATE MODAL
    // ---------------------------------------------
    $("#addRuleBtn").on("click", function () {
        $("#ruleForm")[0].reset();
        $("#rule_id").val("");
        $("#ldSourceTableBody").html("");
        ldRowIndex = 0;

        $("#modalTitle").text("Add Rule");
        $("#is_active").prop("checked", true);
        $("#distribution_type").val("random");
        ldToggleSections();

        $("#ruleModal").modal("show");
    });

    // Distribution type toggle
    $("#distribution_type").on("change", ldToggleSections);

    // ---------------------------------------------
    // EDIT RULE
    // ---------------------------------------------
    $(document).on("click", ".editRule", function () {
        let id = $(this).data("id");

        let url = "{{ route('lead.distribution.edit', ':id') }}";
        url = url.replace(":id", id);

        $("#ruleForm")[0].reset();
        $("#ldSourceTableBody").html("");
        ldRowIndex = 0;

        $.get(url, function (res) {

            $("#rule_id").val(res.id);
            $("#rule_name").val(res.rule_name);
            $("#distribution_type").val(res.distribution_type);
            $("#is_active").prop("checked", res.is_active ? true : false);

            // Random distribution → selected_users
            if (res.distribution_type === "random") {
                let selectedUserIds = [];
                if (res.items && res.items.length) {
                    res.items.forEach(function (it) {
                        if (it.assign_type === 'user' && it.user_id) {
                            selectedUserIds.push(it.user_id);
                        }
                    });
                }
                $("#selected_users").val(selectedUserIds);
            }

            // Source-based distribution → rows
            if (res.distribution_type === "source") {
                if (res.items && res.items.length) {
                    res.items.forEach(function (it) {
                        let assignId = null;
                        if (it.assign_type === 'user') assignId = it.user_id;
                        if (it.assign_type === 'team') assignId = it.team_id;
                        if (it.assign_type === 'designation') assignId = it.designation_id;

                        ldAddSourceRow({
                            source_id: it.source_id,
                            assign_type: it.assign_type,
                            assign_id: assignId,
                            percentage: it.percentage
                        });
                    });
                }
            }

            ldToggleSections();
            $("#modalTitle").text("Edit Rule");
            $("#ruleModal").modal("show");
        });
    });

    // ---------------------------------------------
    // SAVE (CREATE + UPDATE)
    // ---------------------------------------------
    $("#saveRuleBtn").on("click", function () {

        let id = $("#rule_id").val();
        let url = "";

        if (id) {
            url = "{{ route('lead.distribution.update', ':id') }}";
            url = url.replace(":id", id);
        } else {
            url = "{{ route('lead.distribution.store') }}";
        }

        $.post(url, $("#ruleForm").serialize())
            .done(function (res) {
                if (res.status) {
                    location.reload();
                }
            })
            .fail(function (xhr) {
                console.error(xhr.responseText);
                alert("Error saving rule");
            });
    });

    // ---------------------------------------------
    // DELETE RULE
    // ---------------------------------------------
    $(document).on("click", ".deleteRule", function () {

        if (!confirm("Delete this rule?")) return;

        let id = $(this).data("id");

        let url = "{{ route('lead.distribution.delete', ':id') }}";
        url = url.replace(":id", id);

        $.ajax({
            url: url,
            type: "DELETE",
            success: function (res) {
                if (res.status) {
                    location.reload();
                }
            },
            error: function (xhr) {
                console.error(xhr.responseText);
                alert("Error deleting rule");
            }
        });
    });

    // ---------------------------------------------
    // OPEN SIMULATION MODAL
    // ---------------------------------------------
    $("#openSimBtn").on("click", function () {
        $("#simResultArea").hide();
        $("#sim_loading").hide();
        $("#testModal").modal("show");
    });

    // ---------------------------------------------
    // RUN SIMULATION
    // ---------------------------------------------
    $("#runSimulationBtn").on("click", function () {

        let sourceId = $("#sim_source").val();
        let count    = $("#sim_count").val();

        $("#simResultArea").hide();
        $("#sim_loading").show();

        $.post("{{ route('lead.distribution.simulate') }}", {
            source_id: sourceId,
            count: count
        })
        .done(function (res) {
            $("#sim_loading").hide();

            if (res.status === 200) {
                $("#sim_ruleType").text(res.ruleType);
                $("#sim_note").text(res.note);
                $("#sim_total").text(res.total);

                let html = "";
                res.rows.forEach(function (row) {
                    html += `
                        <tr>
                            <td>${row.user_name}</td>
                            <td>${row.leads}</td>
                            <td>${row.percent}%</td>
                        </tr>`;
                });

                $("#sim_table_body").html(html);
                $("#simResultArea").show();
            }
        })
        .fail(function (xhr) {
            $("#sim_loading").hide();
            console.error(xhr.responseText);
            alert("Simulation error: " + (xhr.responseJSON?.message ?? "Unknown error"));
        });
    });

    // On first load ensure toggle correct (for safety)
    ldToggleSections();

</script>
@endsection
