@extends('admin.sidenav')

@section('section-Head')
    All Users
@endsection

@section('customStyle')
    <script src="{{ asset('js/admin/datatable.js') }}"></script>
@endsection

@section('contentMain')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">All Users</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Uptime</th>
                        <th>Bytes In</th>
                        <th>Bytes Out</th>
                        <th>Packets In</th>
                        <th>Packets Out</th>
                        <th>Dynamic</th>
                        <th>Disabled</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>Name</th>
                        <th>Uptime</th>
                        <th>Bytes In</th>
                        <th>Bytes Out</th>
                        <th>Packets In</th>
                        <th>Packets Out</th>
                        <th>Dynamic</th>
                        <th>Disabled</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
                <tbody>
                {{--     @php 
                        dd($active_hotspot_users);
                    @endphp --}}


                    @foreach ($active_hotspot_users as $user)
                        
                        <tr>
                            <td class="text-info"> 
                                {{ isset($user["user"])? ucfirst($user["user"]): "" }}
                            </td>
                            <td> {{ isset($user["uptime"])? $user["uptime"]: "" }} </td>
                            <td> {{ isset($user["bytes-in"])? $user["bytes-in"]: "" }} </td>
                            <td> {{ isset($user["bytes-out"])? $user["bytes-out"]: ""  }} </td>
                            <td> {{ isset($user["packets-in"])? $user["packets-in"]: "" }} </td>
                            <td> {{ isset($user["packets-out"])? $user["packets-out"]: "" }} </td>
                            <td> {{ isset($user["dynamic"])? $user["dynamic"]: "" }} </td>
                            <td> {{ isset($user["disabled"])? $user["disabled"]: "" }} </td>
                            <td>
                                <div class="row">
                                    <div class="pl-3">
                                        <a href="">
                                            <i class="fas fa-plus text-success"></i>
                                        </a>
                                    </div>

                                    <div class="pl-3">
                                        <a href="">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </div>

                                    <div class="pl-3">
                                        <a href="">
                                            <i class="fas fa-trash-alt text-danger"></i>
                                        </a>
                                    </div>
                                    
                                </div>
                            </td>
                        </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
    
@endsection