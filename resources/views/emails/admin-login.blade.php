@extends('emails.layout')
@section('content')
<tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
        Dear {{ $name }},<br><br>
        @if($type)
	        <h5>OHS - admin account just accessed from <span style="font-style: italic; font-weight: bolder;">{{request()->ip()}} at {{$data['browser']}} on {{$data['platform']}}</span>.<br></br><br>
	        <span style="color: #f9bc0b; font-style: italic;">If you did not recognized this, please change your password as soon as possible and make sure make a strong password.</span></h5>
        @else
        	<h5>Someone just tries to access in your {{get_setting('website_title')}} - admin account from <span style="font-style: italic; font-weight: bolder;">{{request()->ip()}} at {{$data['browser']}} on {{$data['platform']}}</span>.<br></br><br>
	        <span style="color: #f9bc0b; font-style: italic;">If you did not recognized this, change your password as soon as possible and make sure make a strong password.</span></h5>
        @endif
    </td>
</tr>
@endsection