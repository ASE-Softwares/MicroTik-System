@extends('admin.sidenav')

@section('customStyle')
<link href="{{ asset('css/routerLogin.css') }}" rel="stylesheet" media="all">
@endsection

@section('section-Head')
Package Section
@endsection

@section('contentMain')

<div class="page-wrapper p-t-30 p-b-10 font-robo">
    <div class="wrapper wrapper--w960">
        <div class="card card-2">
            <div class="card-body">
                <h2 class="title">Create New Package</h2>

                <form method="POST" action="{{ route("newProfile") }}">

                    @csrf
                    <div class="input-group">
                        <input class="input--style-2" type="text" placeholder="Package Name" name="name" value={{ @old('name') }}>
                        @error('name')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <input class="input--style-2" type="text" placeholder="Shared Users" name="shared-users" value={{ @old('shared-users') }}>
                        @error('shared-users')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group row">
                        <label class="col-form-label text-md-right col-md-4" for="KeepAliveTimeout">Select How Long The Package should run</label>

                        <div class="col-md-8" id="KeepAliveTimeout">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="days">Days</label>
                                        <select type="text" name="days" id="days" class="custom-select" placeholder="">
                                            <option value="0" selected>0</option>
                                            @for($t=1; $t<=30; $t++) <option {{ old('days') == $t ? 'selected' : '' }} value="{{ $t }}">
                                                {{ $t}}
                                                </option>
                                                @endfor
                                        </select>
                                        @error('days')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="hours">Hours </label>
                                        <select type="text" name="hours" id="hours" class="custom-select" placeholder="">
                                            <option value=""></option>
                                            @for($t=1; $t<=24; $t++) <option {{ old('hours') == $t ? 'selected' : '' }} value="{{ $t }}">
                                                {{ $t}}
                                                </option>
                                                @endfor
                                        </select>
                                        @error('hours')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="minutes">Minutes</label>
                                        <select type="text" name="minutes" id="minutes" class="custom-select" placeholder="">
                                            <option value=""></option>
                                            <option value="00">00</option>
                                            <option value="05">05</option>
                                            @for($t=10; $t<=55; $t+=5) <option {{ old('minutes') == $t ? 'selected' : '' }} value="{{ $t }}">
                                                {{ $t}}</option>
                                                @endfor
                                        </select>
                                        @error('minutes')
                                        <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="input-group">
                        <input class="input--style-2" type="text" placeholder="Rate Limit" name="rate-limit" value={{ @old('rate-limit') }}>
                        @error('rate-limit')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <input class="input--style-2" type="number" placeholder="Price" name="price" value={{ @old('price') }}>
                        @error('price')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <input class="input--style-2" type="text" placeholder="Description" name="description" }}>

                        @error('description')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="p-t-30">
                        <button class="btn btn--radius btn--green" type="submit">CREATE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection