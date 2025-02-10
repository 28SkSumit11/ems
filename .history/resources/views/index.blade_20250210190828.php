@include('layouts.header')


    <div class="container">
        <div class="mt-5 row">
            <div class="col-md-6">
                <a href="" id="export" class="btn btn-primary">Export</a>
            </div>
            <div class="col-md-6 justify-content-end">
                <div class="row">
                    <div class="col-md-4">
                        <select id="form_id" class="form-select" aria-label="Default select example">
                            <option value="5" selected>Health</option>
                            <option value="6">Life/Term</option>
                            <option value="8">Motor</option>
                          </select>
                    </div>
                    <div class="col-md-8 position-relative">
                        <div class="row select-box-section">
                            <div class="col-md-12">
                                <select id="dateFilter" class="form-select" aria-label="Date Range">
                                    <option value="0" selected>Today</option>
                                    <option value="1">Yesterday</option>
                                    <option value="2">Last 7 Days</option>
                                    <option value="3">Last 30 Days</option>
                                    <option value="4">This Month</option>
                                    <option value="5">Last Month</option>
                                    <option value="6">This Year</option>
                                    <option value="7">Last Year</option>
                                    <option value="8">Lifetime</option>
                                    <option value="custom">Custom Range</option>
                                </select>
                            </div>
                            <div id="calender" class="col-md-2 d-flex justify-content-center align-items-center d-none"><i class="fa-solid fa-calendar-days"></i></div>
                        </div>
                        <div id="customDateInputs" class="p-3 mb-5 rounded shadow bg-body-tertiary" style="display: none;">
                            <div class="d-flex justify-content-end row">
                                <label for="startDate" class="form-label w-50">Start Date
                                    <input type="date" id="startDate" class="form-control">
                                </label>
                                <label for="endDate" class="form-label w-50">End Date
                                    <input type="date" id="endDate" class="form-control">
                                </label>
                                <button id="applyCustomRange" class="mt-3 btn btn-primary w-50">Apply</button>
                            </div>
                        </div>                      
                    </div>
                </div>
            </div>
        </div>

        <pre>
            <?php //print_r($data); ?>
        </pre>
        <table id="example" class="table table-striped">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Date</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
        
        </table>
    </div>
@include('layouts.footer')
