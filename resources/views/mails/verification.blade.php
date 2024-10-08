@extends('mails.layouts.main')
@section('content')
<table role="presentation" class="main" style="border-collapse: separate;
                mso-table-lspace: 0pt;
                mso-table-rspace: 0pt;
                background: #ffffff;
                border-radius: 3px;
                width: 100%;" width="100%">
    <tr>
        <td class="wrapper" style="font-family: sans-serif;
                        font-size: 14px;
                        vertical-align: top;
                        box-sizing: border-box;
                        padding: 20px;" valign="top">
            <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;
                            mso-table-lspace: 0pt;
                            mso-table-rspace: 0pt;
                            width: 100%;" width="100%">
                <tr>
                    <td style="font-family: sans-serif;
                                    font-size: 14px;
                                    vertical-align: top;" valign="top">
                        <p style="font-family: sans-serif;
                                        font-size: 24px;
                                        font-weight: bold;
                                        margin: 0;
                                        margin-bottom: 15px;">{{ $template['title'] }}</p>
                        <p style="font-family: sans-serif;
                                        font-size: 14px;
                                        font-weight: normal;
                                        margin: 0;
                                        margin-bottom: 15px;">{{ $template['description'] }}</p>
                        <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate;
                                        mso-table-lspace: 0pt;
                                        mso-table-rspace: 0pt;
                                        box-sizing: border-box;
                                        width: 100%;" width="100%">
                            <tbody>
                            <tr>
                                <td align="left" style="font-family: sans-serif;
                                                font-size: 14px;
                                                vertical-align: top;
                                                padding-bottom: 15px;" valign="top">
                                    <table role="presentation" border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate;
                                                    mso-table-lspace: 0pt;
                                                    mso-table-rspace: 0pt;
                                                    width: auto;">
                                        <tbody>
                                        <tr>
                                            <td style="font-family: sans-serif;
                                                            font-size: 14px;
                                                            vertical-align: top;
                                                            border-radius: 5px;
                                                            text-align: center;
                                                            background-color: #3498db;" valign="top" align="center" bgcolor="#3498db">
                                                <a href="{{ $template['url'] }}"
                                                   target="_blank"
                                                   style="border: solid 1px #3498db;
                                                                    border-radius: 5px;
                                                                    box-sizing: border-box;
                                                                    cursor: pointer;
                                                                    display: inline-block;
                                                                    font-size: 14px;
                                                                    font-weight: bold;
                                                                    margin: 0;
                                                                    padding: 12px 25px;
                                                                    text-decoration: none;
                                                                    text-transform: capitalize;
                                                                    background-color: #3498db;
                                                                    border-color: #3498db;
                                                                    color: #ffffff;">{{ $template['code'] }}</a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <p style="font-family: sans-serif;
                                        font-size: 14px;
                                        font-weight: bold;
                                        margin: 0;
                                        margin-bottom: 15px;color:indianred;">Aşağıdaki ulaşım bilgilerinin sizinkiyle eş olduğuna emin olunuz!</p>
                        <p style="font-family: sans-serif;
                                        font-size: 14px;
                                        font-weight: normal;
                                        margin: 0;
                                        margin-bottom: 15px;">IP Adresi: {{ $template['ip'] }}</p>
                        <p style="font-family: sans-serif;
                                        font-size: 14px;
                                        font-weight: normal;
                                        margin: 0;
                                        margin-bottom: 15px;">Cihaz: {{ $template['device'] }}</p>
                        <p style="font-family: sans-serif;
                                        font-size: 14px;
                                        font-weight: normal;
                                        margin: 0;
                                        margin-bottom: 15px;">Platform: {{ $template['platform'] }}</p>
                        <p style="font-family: sans-serif;
                                        font-size: 14px;
                                        font-weight: normal;
                                        margin: 0;
                                        margin-bottom: 15px;">Tarayıcı: {{ $template['browser'] }}</p>
                        @if($template['locked'])
                            <p style="font-family: sans-serif;
                                        font-size: 14px;
                                        font-weight: normal;
                                        margin: 0;
                                        margin-bottom: 15px;">
                                <a href="{{ $template['locked'] }}" target="_blank">Şüpheli işlem şüphesi duyuyorsanız buraya tıklayarak hesabınızı kilitleyiniz!</a>
                            </p>
                        @endif
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
@endsection
