@extends('loyaltycorp-test::sidebar')
@section('title', 'View ' . $member->email_address)
@section('active', 'mailchimp')

@section('content')
    <div class="top-row">
        <h1>View {{$member->email_address}}</h1>
        <div class="top-buttons">
            @if ($member->mailchimp_id)
                <a href="{{route('mailchimp.member.sync', ['id' => $list->id, 'mid' => $member->id])}}" role="button" class="btn btn-primary btn-lg">Sync member</a>
                <a href="{{route('mailchimp.member.edit', ['id' => $list->id, 'mid' => $member->id])}}" role="button" class="btn btn-success btn-lg">Edit member</a>
            @else
                <button role="button" class="btn btn-primary btn-lg" disabled="disabled">Sync member</button>
                <button role="button" class="btn btn-success btn-lg" disabled="disabled">Edit member</button>
            @endif
        </div>
        <div class="clearfix"></div>
    </div>
    <p class="backlink"><a href="{{route('mailchimp.member.index', $list->id)}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to {{$list->name}} members</a></p>
    @include('loyaltycorp-test::alerts')
    <table class="table">
        <tr>
            <th>Key</th>
            <th>Value</th>
        </tr>
        @foreach($member->getAttributes() as $key => $value)
            <tr>
                <td>{{$key}}</td>
                <td>
                    @if(@unserialize($value) === 'b:0;' || @unserialize($value) !== false)
                        <ul>
                            @foreach(@unserialize($value) as $subKey => $subValue)
                                <li>{{$subKey}}: {{$subValue}}</li>
                            @endforeach
                        <ul>
                    @else
                        {{$value}}
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
@endsection
