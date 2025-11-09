@extends('emails.layout')
@section('content')
<tr style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
        Dear {{ $name }},<br><br>
        We received a request to update your email for OHS account.<br></br>
        Please click on the Confirm button to update your email.<br><br>
        
        <a href="{{$confirm_email_link}}" class="btn-primary" style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #02c0ce; margin: 0; border-color: #02c0ce; border-style: solid; border-width: 8px 16px;">Confirm</a><br><br>

        If you did not make this request, simply ignore this email and everything will be fine.
    </td>
</tr>
@endsection