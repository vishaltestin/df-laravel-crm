<div class="modal fade" id="ruleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <form id="ruleForm">
                <input type="hidden" id="rule_id" name="rule_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Rule</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- Basic Info --}}
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Rule Name <span class="text-danger">*</span></label>
                            <input type="text" name="rule_name" id="rule_name" class="form-control" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">Distribution Type <span class="text-danger">*</span></label>
                            <select name="distribution_type" id="distribution_type" class="form-select" required>
                                <option value="random">Random (User-wise)</option>
                                <option value="source">Source Based</option>
                            </select>
                        </div>

                        <div class="col-md-2 mb-3 d-flex align-items-end">
                            <div class="form-check mt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>

                    <hr>

                    {{-- RANDOM SECTION --}}
                    <div id="randomSection" class="mb-3">
                        <h5>Random Distribution (Users)</h5>
                        <p class="text-muted mb-2">
                            Select one or more users. New leads will be distributed randomly / round-robin among them.
                        </p>

                        <div class="mb-3">
                            <label class="form-label">Select Users</label>
                            <select class="form-select" name="selected_users[]" id="selected_users" multiple>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Ctrl/Command दबाकर multiple users select कर सकते हैं.</small>
                        </div>
                    </div>

                    {{-- SOURCE SECTION --}}
                    <div id="sourceSection" class="mb-3" style="display:none;">
                        <h5>Source Based Distribution</h5>
                        <p class="text-muted mb-2">
                            हर lead source के लिए, किसको assign करना है (User / Team / Designation) और कितने प्रतिशत, वो define करें.
                        </p>

                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 25%;">Source</th>
                                    <th style="width: 20%;">Assign Type</th>
                                    <th style="width: 30%;">Assign To</th>
                                    <th style="width: 15%;">%</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                                </thead>
                                <tbody id="ldSourceTableBody">
                                {{-- rows JS se add honge --}}
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-sm btn-outline-primary" id="addSourceRowBtn">
                            + Add Source Mapping
                        </button>

                        <small class="d-block mt-2 text-muted">
                            Note: Agar percentage blank छोड़ते हैं, to sab ke beech equal distribution मानी जाएगी.
                        </small>
                    </div>

                </div> {{-- modal-body --}}

                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="button" id="saveRuleBtn" class="btn btn-primary">Save</button>
                </div>

            </form>

        </div>
    </div>
</div>
