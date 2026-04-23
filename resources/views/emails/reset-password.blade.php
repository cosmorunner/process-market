@extends('emails.layout')

@section('content')
    <tr>
        <td class="wrapper"
            style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
            <table border="0" cellpadding="0" cellspacing="0"
                   style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                <tr>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <p style="font-family: sans-serif; font-size: 14px; font-weight: bold; margin: 0; Margin-bottom: 15px;">
                            <span>Hallo!</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <p style="font-family: sans-serif; font-size: 14px; margin: 0; Margin-bottom: 15px;">
                            <span>Sie erhalten diese E-Mail, weil wir eine Anfrage zum Zurücksetzen des Passworts für Ihr Konto erhalten haben.</span>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                        <a href="{{$url}}" class="button button-primary" target="_blank" rel="noopener"
                           style="box-sizing: border-box;
                       font-family: sans-serif; position: relative; -webkit-text-size-adjust: none;
                       border-radius: 4px; color: #fff; display: inline-block; overflow: hidden; text-decoration: none; background-color: #2d3748;
                       border-bottom: 8px solid #2d3748; border-left: 18px solid #2d3748;
                       border-right: 18px solid #2d3748; border-top: 8px solid #2d3748;">Password zurücksetzen</a>
                    </td>
                </tr>
                <table class="subcopy" role="presentation" style="box-sizing: border-box; font-family: sans-serif;
                position: relative; border-top: 1px solid #e8e5ef; margin-top: 25px; padding-top: 25px;" width="100%"
                       cellspacing="0" cellpadding="0">
                    <tbody>
                    <tr>
                        <td style="box-sizing: border-box; font-family: sans-serif; position: relative;">
                            <p style="box-sizing: border-box; font-family: sans-serif; position: relative; line-height: 1.5em;
                                margin-top: 0; text-align: left; font-size: 12px; color: #606060">Wenn Sie Probleme haben auf die Schaltfläche zu klicken, kopieren Sie die folgende URL und fügen Sie sie in Ihren Webbrowser ein:
                                <span class="break-all" style="box-sizing: border-box; font-family: sans-serif; position:
                                    relative; word-break: break-all;">
                                        <a href="{{$url}}" style="box-sizing: border-box; font-family: sans-serif; position:
                                        relative; color: #3869d4;">{{$url}}</a></span>
                            </p>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </table>
        </td>
    </tr>
@endsection
