@extends('loyaltycorp-test::sidebar')
@section('title', 'Delete ' . $list->name)
@section('active', 'mailchimp')

@section('content')
    <div class="top-row">
        <h1>Delete {{$list->name}}</h1>
        <div class="clearfix"></div>
    </div>
    <p class="backlink"><a href="{{route('mailchimp.list.index')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to lists</a></p>
    @include('loyaltycorp-test::alerts')
    <form action="{{route('mailchimp.list.destroy', $list->id)}}" method="post">
        <input name="_method" type="hidden" value="delete">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <p>Are you sure you want to delete {{$list->name}}? It will be permanently removed from Mailchimp.</p>
        <div class="pull-right">
            <input type="submit" class="btn btn-danger" value="Delete list">
            <a href="{{route('mailchimp.list.index')}}" class="btn btn-primary">Oh, nah</a>
        </div>
        <div class="clearfix"></div>
    </form>
@endsection
