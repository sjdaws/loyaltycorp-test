@extends('loyaltycorp-test::sidebar')
@section('title', $list->id ? 'Edit ' . $list->name : 'Create list')
@section('active', 'mailchimp')

@section('content')
    <div class="top-row">
        <h1>{{$list->id ? 'Edit ' . $list->name : 'Create list'}}</h1>
        <div class="clearfix"></div>
    </div>
    <p class="backlink"><a href="{{route('mailchimp.list.index')}}"><i class="fa fa-arrow-left" aria-hidden="true"></i> Back to lists</a></p>
    @include('loyaltycorp-test::alerts')
    @if($list->id)
        <form action="{{route('mailchimp.list.update', $list->id)}}" method="post">
        <input name="_method" type="hidden" value="put">
    @else
        <form action="{{route('mailchimp.list.store')}}" method="post">
    @endif
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <p class="help-block">Fields marked with an asterisk are required</p>
        <fieldset>
            <legend>List settings</legend>
            <div class="form-group">
                <label for="name">List name *</label>
                <input type="text" class="form-control" name="name" id="name" placeholder="My list" maxlength="100" maxlength="100" value="{{ old('name') ?? $list->name }}">
            </div>
            <div class="form-group">
                <label for="permission_reminder">Permission reminder *</label>
                <textarea class="form-control" name="permission_reminder" id="permission_reminder" placeholder="You are receiving this email because you signed up on our website">{{ old('permission_reminder') ?? $list->permission_reminder }}</textarea>
                <span class="help-block">Read more about permission reminders <a href="http://kb.mailchimp.com/accounts/compliance-tips/edit-the-permission-reminder?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs">here</a>.</span>
            </div>
            <div class="form-group">
                <label for="use_archive_bar">Use archive bar</label>
                <div class="radio">
                    <label><input type="radio" name="use_archive_bar" id="use_archive_bar" value="1"@if($list->use_archive_bar != '0' && (!old('use_archive_bar') || old('use_archive_bar') == '1' || $list->use_archive_bar == '1')) checked @endif> Enable</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="use_archive_bar" value="0"@if(old('use_archive_bar') == '0' || $list->use_archive_bar == '0') checked @endif> Disable</label>
                </div>
                <span class="help-block">Read more about the archive bar <a href="http://kb.mailchimp.com/campaigns/archives/about-the-archive-bar?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs">here</a>.</span>
            </div>
            <div class="form-group">
                <label for="notify_on_subscribe">Notify on subscribe</label>
                <input type="text" class="form-control" name="notify_on_subscribe" id="notify_on_subscribe" placeholder="bob@abccorp.com" value="{{ old('notify_on_subscribe') ?? $list->notify_on_subscribe }}">
                <span class="help-block">An email address to send <a href="http://kb.mailchimp.com/lists/managing-subscribers/change-subscribe-and-unsubscribe-notifications?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs">subscription notifications</a> to. Not recommended for large lists.</span>
            </div>
            <div class="form-group">
                <label for="notify_on_unsubscribe">Notify on unsubscribe</label>
                <input type="text" class="form-control" name="notify_on_unsubscribe" id="notify_on_unsubscribe" placeholder="bob@abccorp.com" value="{{ old('notify_on_subscribe') ?? $list->notify_on_subscribe }}">
                <span class="help-block">An email address to send <a href="http://kb.mailchimp.com/lists/managing-subscribers/change-subscribe-and-unsubscribe-notifications?utm_source=mc-api&utm_medium=docs&utm_campaign=apidocs">unsubscribe notifications</a> to. Not recommended for large lists.</span>
            </div>
            <div class="form-group">
                <label for="email_type_option">Email type selection</label>
                <div class="radio">
                    <label><input type="radio" name="email_type_option" id="email_type_option" value="1"@if($list->email_type_option != '0' && (!old('email_type_option') || old('email_type_option') == '1' || $list->email_type_option == '1')) checked @endif> Allow selection</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="email_type_option" value="0"@if(old('email_type_option') == '0' || $list->email_type_option == '0') checked @endif> HTML with text backup</label>
                </div>
                <span class="help-block">Allow members to choose the type or email they receive or send HTML with a text backup for every email.</span>
            </div>
            <div class="form-group">
                <label for="visibility">Visibility</label>
                <div class="radio">
                    <label><input type="radio" name="visibility" id="visibility" value="pub"@if($list->visibility != 'prv' && (!old('visibility') || old('visibility') == 'pub' || $list->visibility == 'pub')) checked @endif> Public, allow campaign to be discovered</label>
                </div>
                <div class="radio">
                    <label><input type="radio" name="visibility" value="prv"@if(old('visibility') == 'prv' || $list->visibility == 'prv') checked @endif> Private</label>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>List contact</legend>
            <div class="form-group">
                <label for="contact_company">Company name *</label>
                <input type="text" class="form-control" name="contact_company" id="contact_company" placeholder="ABC Pty Ltd" value="{{ old('contact_company') ?? $list->contact_company }}">
            </div>
            <div class="form-group">
                <label for="contact_address1">Address *</label>
                <input type="text" class="form-control" name="contact_address1" id="contact_address1" placeholder="123 Fake Street" value="{{ old('contact_address1') ?? $list->contact_address1 }}">
            </div>
            <div class="form-group">
                <label for="contact_address2">Address cont.</label>
                <input type="text" class="form-control" name="contact_address2" id="contact_address2" placeholder="" value="{{ old('contact_address2') ?? $list->contact_address2 }}">
            </div>
            <div class="row">
                <div class="form-group col-sm-4">
                    <label for="contact_city">City *</label>
                    <input type="text" class="form-control" name="contact_city" id="contact_city" placeholder="Springfield" value="{{ old('contact_city') ?? $list->contact_city }}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="contact_state">State *</label>
                    <input type="text" class="form-control" name="contact_state" id="contact_state" placeholder="NSW" value="{{ old('contact_state') ?? $list->contact_state }}">
                </div>
                <div class="form-group col-sm-4">
                    <label for="contact_zip">Postcode *</label>
                    <input type="text" class="form-control" name="contact_zip" id="contact_zip" placeholder="2008" value="{{ old('contact_zip') ?? $list->contact_zip }}">
                </div>
            </div>
            <div class="form-group">
                <label for="contact_country">Country *</label>
                <select class="form-control" name="contact_country" id="contact_country">
                    <option value="AU"@if(old('contact_country') == 'AU' || $list->contact_country == 'AU') selected="selected"@endif>Australia</option>
                    <option value="NZ"@if(old('contact_country') == 'NZ' || $list->contact_country == 'NZ') selected="selected"@endif>New Zealand</option>
                    <option value="US"@if(old('contact_country') == 'US' || $list->contact_country == 'US') selected="selected"@endif>United States</option>
                </select>
            </div>
        </fieldset>
        <fieldset>
            <legend>Campaign defaults</legend>
            <div class="form-group">
                <label for="campaign_defaults_from_name">Sender name *</label>
                <input type="text" class="form-control" name="campaign_defaults_from_name" id="campaign_defaults_from_name" maxlength="100" value="{{ old('campaign_defaults_from_name') ?? $list->campaign_defaults_from_name }}">
            </div>
            <div class="form-group">
                <label for="campaign_defaults_from_email">Sender email address *</label>
                <input type="text" class="form-control" name="campaign_defaults_from_email" id="campaign_defaults_from_email" maxlength="100" value="{{ old('campaign_defaults_from_email') ?? $list->campaign_defaults_from_email }}">
            </div>
            <div class="form-group">
                <label for="campaign_defaults_subject">Subject *</label>
                <input type="text" class="form-control" name="campaign_defaults_subject" id="campaign_defaults_subject" maxlength="150" value="{{ old('campaign_defaults_subject') ?? $list->campaign_defaults_subject }}">
            </div>
            <div class="form-group">
                <label for="campaign_defaults_language">Language *</label>
                <select class="form-control" name="campaign_defaults_language" id="campaign_defaults_language">
                    <option value="en"@if(old('campaign_defaults_language') == 'en' || $list->campaign_defaults_language == 'en') selected="selected"@endif>English</option>
                    <option value="sw"@if(old('campaign_defaults_language') == 'sw' || $list->campaign_defaults_language == 'sw') selected="selected"@endif>Swahili</option>
                </select>
            </div>
        </fieldset>
        <div class="pull-right">
            <input type="submit" class="btn btn-primary" value="{{ $list->id ? 'Update' : 'Create' }} list">
        </div>
        <div class="clearfix"></div>
    </form>
@endsection
