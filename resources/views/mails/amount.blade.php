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
                                        margin-bottom: 15px;
                                        text-align: center;">{{ $template['title'] }}</p>
                            <p style="font-family: sans-serif;
                                        font-size: 14px;
                                        font-weight: normal;
                                        margin: 0;
                                        margin-bottom: 15px;
                                        text-align: center;">{{ $template['description'] }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
@endsection
