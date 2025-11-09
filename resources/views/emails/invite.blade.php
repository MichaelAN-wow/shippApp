@extends('emails.layout')

@section('content')
<tr style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
    <td class="content-block" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0; padding: 0 0 20px;" valign="top">
        <p>Hello {{ $name }},</p>
        <br><br>
        <p>You have been invited to join the {{ $company_name }} team! Click the link below to accept the invitation:</p>
        <a href="{{ $invite_link }}">Accept Invitation</a>
        
        Best regards,<br>
        The OHS Team
    </td>
</tr>
@endsection