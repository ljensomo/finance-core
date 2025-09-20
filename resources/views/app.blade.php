@extends('layouts.app')

@section('content')
    <router-view></router-view>
@endsection

@include('modals.transaction-modals')
@include('modals.category-modals')
@include('modals.wishlist-modals')