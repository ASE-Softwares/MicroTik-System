
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Microtik System</title>

        <!-- Styles -->
        <style>
        </style>

        <style>
            body {
                font-family: 'Nunito', sans-serif;
            }
        </style>

        
        {{-- <link rel="stylesheet" type="text/css" href="{{asset('css/app.css')}}"> --}}
        <link rel="stylesheet" type="text/css" href="{{asset('css/normalize.css')}}">
        <script src="{{ asset('js/app.js') }}" defer></script>
        <!------ Include the above in your HEAD tag ---------->

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/css/all.min.css" type="text/css">
        <link rel="stylesheet" href="https://demo.themesberg.com/pixel-pro/css/pixel.css" type="text/css">
        <link href="{{ asset('css/routerLogin.css') }}" rel="stylesheet" media="all">
    </head>
    <body class="antialiased">
        <div id="app" class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
            <div class="section section-md py-5">
                <div class="container">
                    <!-- Title  -->
                    <div class="row">
                        <div class="col-md-4 text-center mx-auto">
                            <div class="mb-5">
                                <a href="#">
                                    <img src="{{ asset("img/wifi.png") }}" alt="Logo Themesberg Light"
                                        class="mb-4" style="width: 75px;">
                                    <h1 class="h3 mb-4">ASE Wireless</h1>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">

                        <div class="col-md-12 mx-auto">
                            <!-- Section title-->
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mt-5 mb-5">
                                        <h4 class="font-weight-bold">Packages</h4>
                                    </div>
                                </div>
                            </div>
                            <!--End section title-->

                            <div class="row">
                                @foreach ($packages as $package)
                                    


                                <div class="col-md-6 col-lg-4">
                                    <!-- Pricing Card -->
                                    <div class="pricing-card mt-3">
                                        <div class="card bg-dark text-center text-white p-4">
                                            <!-- Header -->
                                            <header class="card-header border-0">
                                                <h1 class="h3 text-warning mb-3">{{ $package['name'] }}</h1>
                                                <div class="pricing-value border-white">
                                                    <span class="display-2 font-weight-bold">
                                                        <span class="font-medium">Ksh. </span>{{ $package['price'] }}
                                                    </span>
                                                </div>
                                            </header>
                                            <!-- End Header -->

                                            <!-- Content -->
                                            <div class="card-body">
                                                <ul class="list-group mb-4">
                                                    @if (!isset($package['desription']))
                                                        @foreach (json_decode($package['description']) as $description)
                                                                <li class="list-group-item">{{ isset($description)? $description: "" }}</li>
                                                        @endforeach
                                                    @endif
                                                    
                                                </ul>
                                                <pay-button :sel_package="{{$package}}"/>
                                                {{-- <button id="{{ $package['id'] }}" type="button" class="btn btn-white btn-block text-dark font-weight-bold animate-up-2"
                                                    tabindex="0" data-toggle="modal" data-target="#myModal">
                                                    <span class="fas fa-money-check-alt" ></span> Purchase </button> --}}
                                            </div>

                                            <!-- Modal -->
                                                
                    
                                            <!-- End Content -->
                                        </div>
                                    </div>
                                    <!-- End of Pricing Card -->
                                </div>





                                @endforeach
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
           {{--  <div id="myModal" class="modal fade" role="dialog">
                <div class="modal-dialog">
            
                <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                            {{-- <button type="button" class="close" data-dismiss="modal">&times;</button> --}}
                            {{-- <h4 class="modal-title">Phone Number: </h4>
                            </div>
                            <div class="modal-body">
                                <form action="{{route('purchase')}}" method="post">
                                    
                                    @csrf 
                                    <div class="input-group">
                                        <input class="input--style-2" type="text" placeholder="Enter Your Phone Number: " name="phone-number" value={{ @old('ip') }}>
                                    </div>
                                    
                                    <div class="input-group d-none">
                                        <input class="input--style-2" type="text" placeholder="" name="package-id" value="">
                                    </div>

                                    <div class="">
                                        <button class="btn btn--radius btn--green" type="submit">Proceed</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
            
                </div>
            </div>
            
        </div>
 --}} 
<script type="text/javascript" src="https://demo.themesberg.com/pixel-pro/assets/js/pixel.js"></script>
    </body>

    
</html>


