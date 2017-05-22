@extends('loyaltycorp-test::sidebar')
@section('title', $member->id ? 'Edit ' . $member->email_address : 'Create member')
@section('active', 'mailchimp')

@section('content')
    <div class="top-row">
        <h1>{{$member->id ? 'Edit ' . $member->email_address : 'Create member'}}</h1>
        <div class="clearfix"></div>
    </div>
    <p class="backlink"><a href="{{route('mailchimp.member.index', ['id' => $list->id])}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to {{$list->name}}</a></p>
    @include('loyaltycorp-test::alerts')
    @if($member->id)
        <form action="{{route('mailchimp.member.update', ['id' => $list->id, 'mid' => $member->id])}}" method="post">
        <input name="_method" type="hidden" value="put">
    @else
        <form action="{{route('mailchimp.member.store', $list->id)}}" method="post">
    @endif
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <p class="help-block">Fields marked with an asterisk are required</p>
        <fieldset>
            <legend>Member settings</legend>
            <div class="form-group">
                <label for="name">Email address *</label>
                @if ($member->id && $member->status != 'subscribed')
                    <p class="form-control-static">{{ $member->email_address }}</p>
                    <input type="hidden" name="email_address" value="{{ $member->email_address }}">
                    <span class="help-block">You can not update the email address for members who aren't subscribed.</span>
                @else
                    <input type="text" class="form-control" name="email_address" id="email_address" placeholder="joe@jojo.com" maxlength="255" value="{{ old('email_address') ?? $member->email_address }}">
                @endif
            </div>
            <div class="row">
                <div class="form-group col-md-4">
                    <label for="contact_country">Email type</label>
                    <select class="form-control" name="email_type" id="email_type">
                        <option value="html"@if(old('email_type') == 'html' || $member->email_type == 'html') selected="selected"@endif>HTML</option>
                        <option value="text"@if(old('email_type') == 'text' || $member->email_type == 'text') selected="selected"@endif>Text</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="status">Status</label>
                    <select class="form-control" name="status" id="status">
                        <option value="subscribed"@if(old('status') == 'subscribed' || $member->status == 'subscribed') selected="selected"@endif>Subscribed</option>
                        <option value="unsubscribed"@if(old('status') != 'subscribed' && $member->status != 'subscribed') selected="selected"@endif>Unsubscribed</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="language">Language</label>
                    <select class="form-control" name="language" id="language">
                        <option value="en"@if(old('language') == 'en' || $member->email_type == 'en') selected="selected"@endif>English</option>
                        <option value="sw"@if(old('language') == 'sw' || $member->email_type == 'sw') selected="selected"@endif>Swahili</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="vip">VIP</label>
                <div class="radio">
                    <label><input type="radio" name="vip" id="vip" value="1"@if(old('vip') == '1' || $member->vip == '1') checked @endif> True</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="vip" value="0"@if($member->vip != '1' && (!old('vip') || old('vip') == '0' || $member->vip == '0')) checked @endif> False</label>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Location</legend>
            <div class="row">
                <div class="form-group col-sm-6">
                    <label for="location_latitude">Latitude</label>
                    <input type="text" class="form-control" name="location_latitude" id="location_latitude" placeholder="37.8136"  maxlength="255" value="{{ old('location_latitude') ?? $member->location_latitude }}">
                </div>
                <div class="form-group col-sm-6">
                    <label for="location_longitude">Longitude</label>
                    <input type="text" class="form-control" name="location_longitude" id="location_longitude" placeholder="144.9631"  maxlength="255" value="{{ old('location_longitude') ?? $member->location_longitude }}">
                </div>
            </div>
        </fieldset>
        @if(@unserialize($member->merge_fields) === 'b:0;' || @unserialize($member->merge_fields) !== false)
            <fieldset>
                <legend>Merge fields</legend>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(@unserialize($member->merge_fields) as $subKey => $subValue)
                            <tr>
                                <td><p class="form-control-static">{{ $subKey }}</p></td>
                                <td><input type="text" class="form-control" name="merge_values[{{$subKey}}]" maxlength="255" value="{{ old('merge_values.' . $subKey) ?? $subValue }}"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <span class="help-block">Read more about merge fields <a href="http://kb.mailchimp.com/merge-tags/use-merge-tags-to-send-personalized-files">here</a>. If some merge fields are missing you should <a href="{{route('mailchimp.member.syncall', $list->id)}}">sync members</a> which will update the merge_fields.</span>
            </fieldset>
        @endif
        <div class="pull-right">
            <input type="submit" class="btn btn-primary" value="{{ $member->id ? 'Update' : 'Create' }} member">
        </div>
        <div class="clearfix"></div>
    </form>
@endsection
