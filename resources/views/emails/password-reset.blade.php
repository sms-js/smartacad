@extends('admin.partials.emails.emails')

@section('title', 'Password Reset')

@section('content')

    <h3>Hi, <strong><i>{{ $req->fullNames() }}</i></strong></h3>
    <div class="lead" style="margin-bottom: 10px; font-weight: normal; font-size:17px; line-height:1.6;">
        <?php $content = explode("\n", $content); ?>
        @foreach($content as $line)
            <p>{!! $line !!}</p>
        @endforeach
        <a href="{{ env('DOMAIN_URL') }}login">Login</a>
    </div>

@endsection