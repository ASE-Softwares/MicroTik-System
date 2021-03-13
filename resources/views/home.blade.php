@extends('admin.sidenav')

@section('section-Head')
    Dashboard
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{\App\Models\Profile::all()}}

                    {{\App\Models\User::all()}}
                    <hr>
                    {{-- {{dd(request()->session()->get('router_session'))}} --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
