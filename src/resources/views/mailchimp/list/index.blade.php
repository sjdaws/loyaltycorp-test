@extends('loyaltycorp-test::sidebar')
@section('title', 'Manage Mailchimp')
@section('active', 'mailchimp')

@section('content')
    <div class="top-row">
        <h1>Manage Mailchimp</h1>
        <div class="top-buttons">
            <a href="{{route('mailchimp.list.syncall')}}" role="button" class="btn btn-primary btn-lg">Sync lists</a>
            <a href="{{route('mailchimp.list.create')}}" role="button" class="btn btn-success btn-lg">Add new list</a>
        </div>
        <div class="clearfix"></div>
    </div>
    @include('loyaltycorp-test::alerts')
    <table class="table">
        @if(count($lists))
            <tr>
                <th>List name</th>
                <th>Members</th>
                <th>Open rate</th>
                <th>Click rate</th>
                <th>&nbsp;</th>
            </tr>
            @foreach ($lists as $list)
                <tr>
                    <td>
                        <a href="{{route('mailchimp.list.show', $list->id)}}">{{$list->name}}</a>
                    </td>
                    <td>{{$list->stats_member_count ?: 0}}</td>
                    <td>{{sprintf('%.2f', $list->stats_open_rate ?: 0)}}%</td>
                    <td>{{sprintf('%.2f', $list->stats_click_rate ?: 0)}}%</td>
                    <td class="actions">
                        <a href="{{route('mailchimp.list.show', $list->id)}}" title="View list">
                            <i class="fa fa-search" aria-hidden="true"></i>
                        </a>
                        @if ($list->mailchimp_id)
                            <a href="{{route('mailchimp.list.edit', $list->id)}}" title="Edit list">
                                <i class="fa fa-pencil" aria-hidden="true"></i>
                            </a>
                            <a href="{{route('mailchimp.list.delete', $list->id)}}" title="Delete list">
                                <i class="fa fa-trash-o" aria-hidden="true"></i>
                            </a>
                            <a href="{{route('mailchimp.member.index', $list->id)}}" title="Manage members">
                                <i class="fa fa-envelope-o" aria-hidden="true"></i>
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>You currently have no lists, add one or <a href="{{route('mailchimp.list.syncall')}}">sync lists</a> from mailchimp to begin.</td>
            </tr>
        @endif
    </table>
@endsection
