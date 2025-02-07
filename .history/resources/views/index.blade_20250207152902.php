@include('layouts.header')


    <div class="container">
        <div class="mt-5 row">
            <div class="col-md-6">
                <button type="button" id="export" class="btn btn-primary">Export</button>
            </div>
            <div class="col-md-6 justify-content-end">
                <div class="row">
                    <div class="col-md-4">
                        <select class="form-select" aria-label="Default select example">
                            <option value="5" selected>Health</option>
                            <option value="6">Life/Term</option>
                            <option value="8">Motor</option>
                          </select>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" aria-label="Default select example">
                            <option value="0" selected>Today</option>
                            <option value="2">Yesterday</option>
                            <option value="3">Last 7 Days</option>
                            <option value="3">Last 30 Days</option>
                            <option value="3">This Month</option>
                            <option value="3">Last Month</option>
                            <option value="3">This Year</option>
                            <option value="3">Last Year</option>
                            <option value="3">Lifetime</option>
                            <option value="3">Custom Range</option>
                          </select>
                    </div>
                    <div class="col-md-2 justify-content-end d-flex">
                        <button type="button" id="update" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="my-5 row">
            <div class="col-md-6">
                <div class="row align-items-center">
                    <div class="col-md-2">
                        <select class="form-select" id="entries_per_page" aria-label="Default select example">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                    <div class="col-md-8 ps-0">
                        <p class="mb-0">entries per page.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 justify-content-end d-flex">
                <input type="text" class="form-control w-50" placeholder="Search" id="search">
            </div>
        </div>
        <table id="example" class="display " style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr data-arrJs = "{{ json_encode($d) }}">
                    <td>{{ $d->id }}</td>
                    <td>System Architect</td>
                    <td>Edinburgh</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Position</th>
                    <th>Office</th>
                    <th>Age</th>
                    <th>Start date</th>
                    <th>Salary</th>
                </tr>
            </tfoot>
        </table>
        <?php var_dump($data); ?>
        <table class="table table-striped d-none">
            <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Name</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
            </tr>
            <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
            </tr>
            <tr>
                <th scope="row">3</th>
                <td colspan="2">Larry the Bird</td>
                <td>@twitter</td>
            </tr>
            </tbody>
        </table>
        <div class="row">
            <div class="col-md-6">
                <p>Showing <span id="range">1 to 10</span> of <span id="total_entries">118</span> entries</p>
            </div>
            <div class="col-md-6 justify-content-end d-flex">
                <nav aria-label="...">
                <ul class="pagination">
                    <li class="page-item disabled">
                    <span class="page-link">Previous</span>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">1</a></li>
                    <li class="page-item active" aria-current="page">
                    <span class="page-link">2</span>
                    </li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
                </nav>
            </div>
        </div>
    </div>

@include('layouts.footer')
