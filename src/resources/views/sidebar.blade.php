@extends('loyaltycorp-test::layout')

@section('sidebar')
    <ul>
        <li>
            <a href="{{route('mailchimp.list.index')}}"@if ($__env->yieldContent('active') == 'mailchimp') class="active"@endif>
                <i class="fa fa-mailchimp" aria-hidden="true"></i>
            </a>
        </li>
        <div class="clearfix"></div>
    </ul>
@endsection
