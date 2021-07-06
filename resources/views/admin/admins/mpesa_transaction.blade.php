@extends('admin.sidenav')

@section('customStyle')
<link href="{{ asset('css/routerLogin.css') }}" rel="stylesheet" media="all">
@endsection

@section('section-Head')
+ Raw Transaction To System
@endsection

@section('contentMain')

<div class="page-wrapper p-t-30 p-b-10 font-robo">
    <div class="wrapper wrapper--w960">
        <div class="card card-2">
            <div class="card-body">
                <h2 class="title">Add Transaction</h2>
                @if( \Session::has('success'))
                <h3 class="text-center text-success">{{\Session::get('success')}}</h3>
                @endif
                <p>Only use this Form when you have verified purchase details from the user</p>

                <form method="POST" action="{{ route("admin.raw_purchase") }}">

                    @csrf 
                    <div class="input-group">
                        <input class="input--style-2" type="text" placeholder="Amount" value="{{old('amount')}}" name="amount" required>
                        @error('amount')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <input class="input--style-2" type="text" placeholder="Mpesa Receipt Number"  name="MpesaReceiptNumber" value="{{ @old('MpesaReceiptNumber') }}" required>
                        @error('MpesaReceiptNumber')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <input class="input--style-2" type="date" placeholder="Transaction Date From Mpesa Message" name="TransactionDate" value="{{old('TransactionDate')}}" required>
                        @error('TransactionDate')
                        <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="input-group">
                        <input class="input--style-2" type="text" placeholder="Phone Number in format 0XXXXXXXXX" name="PhoneNumber" required value="{{@old('PhoneNumber')}}">
                        
                    </div>                  

                    <div class="p-t-30">
                        <button class="btn btn--radius btn--green" type="submit">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection