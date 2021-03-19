
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
        <script src="{{ asset('js/admin/jquery.js') }}"></script>
        <script src="{{ asset('js/app.js') }}" defer></script>
        <script src="{{ asset('js/all.js') }}"></script>


        <!------ Include the above in your HEAD tag ---------->

        <link href="{{ asset('css/routerLogin.css') }}" rel="stylesheet" media="all">
        <link href="{{ asset('css/admin/pixel.css') }}" rel="stylesheet" media="all">
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
                                                <pay-button :sel_package="{{ $package }}"/>
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
        </div>
    </body>
  
</html>


