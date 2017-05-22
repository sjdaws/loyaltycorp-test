@extends('loyaltycorp-test::sidebar')
@section('title', 'View ' . $list->name)
@section('active', 'mailchimp')

@section('content')
    <div class="top-row">
        <h1>{{$list->name}}</h1>
        <div class="top-buttons">
            @if ($list->mailchimp_id)
                <a href="{{route('mailchimp.list.sync', $list->id)}}" role="button" class="btn btn-primary btn-lg">Sync list</a>
                <a href="{{route('mailchimp.member.index', $list->id)}}" role="button" class="btn btn-primary btn-lg">Manage members</a>
                <a href="{{route('mailchimp.list.edit', $list->id)}}" role="button" class="btn btn-success btn-lg">Edit list</a>
            @else
                <button role="button" class="btn btn-primary btn-lg" disabled="disabled">Sync list</button>
                <button role="button" class="btn btn-primary btn-lg" disabled="disabled">Manage members</button>
                <button role="button" class="btn btn-success btn-lg" disabled="disabled">Edit list</button>
            @endif

        </div>
        <div class="clearfix"></div>
    </div>
    <p class="backlink"><a href="{{route('mailchimp.list.index')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to lists</a></p>
    @include('loyaltycorp-test::alerts')
    <table class="table">
        <tr>
            <th>Key</th>
            <th>Value</th>
        </tr>
        @foreach($list->getAttributes() as $key => $value)
            <tr>
                <td>{{$key}}</td>
                <td>{{$value}}</td>
            </tr>
        @endforeach
    </table>
@endsection
