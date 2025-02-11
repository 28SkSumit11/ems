@include('layouts.header')
<div class="d-flex justify-content-center align-items-center" style="height: 100vh">

        <div class="login-section container shadow p-3 mb-5 bg-body-tertiary rounded">
            <h2 class="text-center my-5"><img src="{{ asset('/images/logo.png') }}">
              {{-- <i class="fa-solid fa-user" style="font-size: 100px;"></i> --}}
            </h2>

            @if($errors->any())
                <div>
                    <p>{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-floating mb-3">
                  <input type="email" class="form-control" id="floatingInput" name="email" placeholder="name@example.com">
                  <label for="floatingInput">Email address</label>
                </div>
                <div class="form-floating mb-3">
                  <input type="password" class="form-control" id="floatingPassword" name="password" placeholder="Password">
                  <label for="floatingPassword">Password</label>
                </div>
                <button type="submit" class="btn btn-primary">Sign in</button>
            </form>
        </div>
</div>

@include('layouts.footer')