@extends('admin.sidenav')

@section('section-Head')
Wired Clients
@endsection

@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Create a new Client <a class="btn btn-sm btn-primary float-right" href="{{route('wired_clients.index')}}">View Client <i class="fa fa-eye" aria-hidden="true"></i> </a></h6>
    </div>
    <div class="card-body">
        <div class="well">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="{{route('wired_clients.store')}}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="ip">IP Adress</label>
                            <input type="text" name="ip" value="{{old('ip') ?? $suggested}}" id="ip" class="form-control" placeholder="Enter Client IP Adress" aria-labelledby="helpId" required>
                            <small id="helpId" class="text-muted">Use Suggested : {{$suggested}} NB: All IPS will be prefixed with /30</small>
                            @error('ip')
                            <br />
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="client_name">Client Name</label>
                            <input type="text" value="{{old('client_name')}}" name="client_name" id="client_name" class="form-control" placeholder="Enter Name">
                            @error('client_name')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="text" value="{{old('email')}}" name="email" id="email" class="form-control" placeholder="Provide an Email Address" required>
                            @error('email')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="wan">Select Your Wan Interface</label>
                            <select class="custom-select" name="wan" id="wan">
                                @foreach ($interfaces as $i)
                                <option value="{{$i['name']}}">{{$i['name']}}</option>
                                @endforeach
                            </select>
                            @error('wan')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>



                        <div class="form-group">
                            <label for="lan">Select Your LAN Port</label>
                            <select class="custom-select" name="lan" id="lan">
                                @foreach ($interfaces as $i)
                                <option value="{{$i['name']}}">{{$i['name']}}</option>
                                @endforeach
                            </select>
                            @error('lan')
                            <small class="text-danger">{{$message}}</small>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="package_id">Select Client Subscription</label>
                            <select name="package_id" id="package_id" class="form-control" placeholder="Select Subscription">
                                @foreach ($packages as $package)
                                <option value="{{$package->id}}">{{$package->name}} @ Ksh. {{$package->amount}} for speeds upto {{$package->rate}}</option>
                                @endforeach
                            </select>

                        </div>


                        <div class="form-group">
                            <button type="submit" name="" id="" class="btn btn-primary" btn-lg btn-block">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection