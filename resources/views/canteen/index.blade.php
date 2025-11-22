@extends('layouts.main')

@section('content')
    @include('canteen.partials.navigation')

    <!-- Main Content Area -->
    <div id="main-container" class="flex-1 overflow-hidden relative">
        
        @include('canteen.views.login')
        @include('canteen.views.register')
        @include('canteen.views.home')
        @include('canteen.views.student')
        @include('canteen.views.history')
        @include('canteen.views.admin')

        <!-- GLOBAL COMPONENTS -->
        @include('canteen.components.cart')
        @include('canteen.components.payment-modal')
        @include('canteen.components.confirm-modal')
        @include('canteen.components.product-modal')
        @include('canteen.components.toast')

    </div>
@endsection
