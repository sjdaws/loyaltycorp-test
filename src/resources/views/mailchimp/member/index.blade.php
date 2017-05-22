@extends('loyaltycorp-test::sidebar')
@section('title', 'Manage ' . $list->name . ' members')
@section('active', 'mailchimp')

@section('content')
    <div class="top-row">
        <h1>Manage {{$list->name}} members</h1>
        <div class="top-buttons">
            <a href="{{route('mailchimp.member.syncall', $list->id)}}" role="button" class="btn btn-primary btn-lg">Sync members</a>
        </div>
        <div class="clearfix"></div>
    </div>
    <p class="backlink"><a href="{{route('mailchimp.list.show', $list->id)}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to {{$list->name}}</a></p>
    @include('loyaltycorp-test::alerts')
    <form action="{{route('mailchimp.list.bulk', $list->id)}}" method="post">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <table class="table">
            @if(count($members))
                <tr>
                    <th>Email address</th>
                    <th>Open rate</th>
                    <th>Click rate</th>
                    <th>Status</th>
                    <th></th>
                </tr>
                @foreach($members as $member)
                    <tr>
                        <td><a href="{{route('mailchimp.member.show', ['id' => $list->id, 'mid' => $member->id])}}">{{$member->email_address}}</a></td>
                        <td>{{sprintf('%.2f', $member->stats_avg_open_rate ?: 0)}}%</td>
                        <td>{{sprintf('%.2f', $member->stats_avg_click_rate ?: 0)}}%</td>
                        <td>
                            <select name="status[{{$member->email_address}}]" class="form-control">
                                <option value="subscribed"@if($member->status == 'subscribed') selected="selected"@endif>Subscribed</option>
                                <option value="unsubscribed"@if($member->status != 'subscribed') selected="selected"@endif>Unsubscribed</option>
                            </select>
                        </td>
                        <td class="actions">
                            <a href="{{route('mailchimp.member.show', ['id' => $list->id, 'mid' => $member->id])}}" title="View member">
                                <i class="fa fa-search" aria-hidden="true"></i>
                            </a>
                            @if ($member->mailchimp_id)
                                <a href="{{route('mailchimp.member.edit', ['id' => $list->id, 'mid' => $member->id])}}" title="Edit member">
                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                </a>
                                <a href="{{route('mailchimp.member.delete', ['id' => $list->id, 'mid' => $member->id])}}" title="Delete member">
                                    <i class="fa fa-trash-o" aria-hidden="true"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td>You currently have no members in this list :( add a few or <a href="{{route('mailchimp.member.syncall', $list->id)}}">sync members</a> from mailchimp to begin.</td>
                </tr>
            @endif
        </table>
        <fieldset>
            <legend>Bulk subscribe</legend>
            <div class="form-group">
                <label for="subscribe">Email addresses</label>
                <textarea class="form-control" name="subscribe" id="subscribe" placeholder="joe@user.com, ted@test.com">{{ old('subscribe') }}</textarea>
                <span class="help-block">Enter a comma separated list of emails to bulk subscribe users.</span>
            </div>
        </fieldset>
        <div class="pull-right">
            <input type="submit" class="btn btn-primary" value="Bulk update">
        </div>
        <div class="clearfix"></div>
    </form>
@endsection
