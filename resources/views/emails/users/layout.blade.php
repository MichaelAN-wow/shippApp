<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title></title>
    <style type="text/css">
        body {
            font-family: "Arial", "Helvetica", "sans-serif" !important;
            color: #fff;
        }

        body {
            margin: 0;
        }

        p {
            line-height: 2;
            margin-top: 0;
        }

        .f-24 {
            font-size: 24px !important;
        }

        .f-22 {
            font-size: 22px !important;
        }

        .f-20 {
            font-size: 20px !important;
        }

        .f-18 {
            font-size: 18px !important;
        }

        .f-16 {
            font-size: 16px !important;
        }

        .f-14 {
            font-size: 14px !important;
        }

        .f-12 {
            font-size: 12px !important;
        }

        .f-11 {
            font-size: 11px !important;
        }

        .f-bold {
            font-weight: bold !important;
        }

        tbody td {
            padding: 0 10%;
        }

        .header {
            text-align: center;
            color: white;
            margin-top: 0px;
            background-image: linear-gradient(#121218, #582E30);
            border-bottom-left-radius: 40px;
            border-bottom-right-radius: 40px;
            text-align: center;
            padding: 20px 10%;
        }

        .footer-links {
            background: #4B2B2B;
            padding: 30px 10%;
            text-transform: capitalize;
            border-top-left-radius: 80px;
            border-top-right-radius: 80px;
            text-align: center;
        }

        .footer-links a {
            position: relative;
            padding: 0px 6px;
            display: inline-block;
            text-decoration: none !important;
        }

        .footer-links a:not(:last-child):after {
            content: "";
            width: 2px;
            background: #999;
            height: 100%;
            display: block;
            position: absolute;
            right: -3px;
            top: 0px;
        }

    </style>
</head>

<body>
    <table cellspacing="0" cellpadding="0" style="width: 700px; max-width: 100%; border: none; background: #2B2222; margin: auto;">
        <thead>
            <tr>
                <td>
                    <div class="header">
                       
                    </div>
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 50px;">
                    @yield('content')
                </td>
            </tr>
            <!-- <tr>
                <td>
                    <div class="signature">
                        <h4 class="f-16" style="color: #B2B2B2; margin: 15px 0 10px;">Your regards</h4>
                        <p class="f-12" style="color: #B2B2B2;">Looking forward to your happy use.</p>
                    </div>
                </td>
            </tr> -->
        </tbody>
        <tfoot>
            <tr>
                <td>
                    <div class="footer-links">
                        @php $footer_links = json_decode(get_setting('mail_footer_links'), true); @endphp
                        @foreach ($footer_links as $key => $link_data)
                            <a class="f-11" target="_blank" href="{{ $link_data['url'] }}" style="color: #CECECE;">{{ $link_data['text'] }}</a>
                        @endforeach
                    </div>
                </td>
            </tr>
        </tfoot>
    </table>
</body>

</html>
