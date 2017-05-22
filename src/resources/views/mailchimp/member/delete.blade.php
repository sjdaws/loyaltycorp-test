@extends('loyaltycorp-test::sidebar')
@section('title', 'Delete list ' . $list->name)
@section('active', 'mailchimp')

@section('content')
    <div class="top-row">
        <h1>Delete {{$member->email_address}} from {{$list->name}}</h1>
        <div class="clearfix"></div>
    </div>
    <p class="backlink"><a href="{{route('mailchimp.member.index', ['id' => $list->id, 'mid' => $member->id])}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to members</a></p>
    @include('loyaltycorp-test::alerts')
    <form action="{{route('mailchimp.member.destroy', ['id' => $list->id, 'mid' => $member->id])}}" method="post">
        <input name="_method" type="hidden" value="delete">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <p>Are you sure you want to delete this member from {{$list->name}}? They will be permanently removed from Mailchimp, you might want to unsubscribe them instead.</p>
        <div class="pull-right">
            <input type="submit" class="btn btn-danger" value="Delete 'em">
            <a href="{{route('mailchimp.member.index', $list->id)}}" class="btn btn-primary">Oops!</a>
        </div>
        <div class="clearfix"></div>
    </form>
@endsection
