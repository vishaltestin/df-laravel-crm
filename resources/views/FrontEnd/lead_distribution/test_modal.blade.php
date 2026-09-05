<div class="modal fade" id="testModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Lead Distribution Simulation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Select Lead Source</label>
                        <select id="sim_source" class="form-select">
                            @foreach($sources as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">No. of Leads</label>
                        <input type="number" id="sim_count" class="form-control" value="100" min="1" max="1000">
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100" id="runSimulationBtn">
                            Run Simulation
                        </button>
                    </div>
                </div>

                <hr>

                <div id="simResultArea" style="display:none;">
                    <h5 class="mb-1">Result</h5>
                    <p class="mb-1"><strong>Rule Used:</strong> <span id="sim_ruleType"></span></p>
                    <p class="mb-2"><strong>Note:</strong> <span id="sim_note"></span></p>
                    <p class="mb-3"><strong>Total Leads Simulated:</strong> <span id="sim_total"></span></p>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered align-middle mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th style="width: 120px;">Leads</th>
                                <th style="width: 120px;">%</th>
                            </tr>
                            </thead>
                            <tbody id="sim_table_body">
                            </tbody>
                        </table>
                    </div>
                </div>

                <div id="sim_loading" style="display:none;" class="text-center my-3">
                    Running simulation...
                </div>

            </div>

        </div>
    </div>
</div>
