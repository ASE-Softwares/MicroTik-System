@extends('admin.sidenav')

@section('section-Head')
Wired Clients
@endsection

@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">All Wired Clients <a class="btn btn-sm btn-primary float-right" href="{{route('wired_clients.create')}}">New Client</a></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">

            <table class="display nowrap table table-hover table-striped table-bordered dataTable" cellspacing="0" width="100%" role="grid" aria-describedby="example23_info" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Network</th>
                        <th>Comment</th>
                        <th>Created At</th>
                        <th>More</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                </tfoot>
                <tbody>
                    @foreach ($users as $user)

                    <tr>
                        <td class="">
                            {{$user['.id']}}
                        </td>
                        <td>
                            <b>{{$user['network']}}</b>
                        </td>

                        <td>
                            @if (isset($user['comment']))
                            {{$user['comment']}} </a>
                            @endif
                        </td>

                        <td>
                            <code>{{$user['address']}}</code>
                        </td>


                        <td></td>
                        <td class="">
                            <div class="row">
                                <div class="pl-3">
                                    <a href="#">
                                        <i class="fas fa-plus text-success"></i>
                                    </a>
                                </div>

                                <div class="pl-3">
                                    <a href="#">
                                        <i class="fas fa-pen"></i>
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