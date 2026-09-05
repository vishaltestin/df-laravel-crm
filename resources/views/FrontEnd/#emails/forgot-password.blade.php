<table bgcolor="#e0e2e4" width="800" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto">
    <tr>
        <td width="75">&nbsp;</td>
        <td width="650" bgcolor="#ffffff"><table width="100%" cellpadding="0" cellspacing="0" border="0" >
           
                <tr>
                    <td width="20" bgcolor="#ffffff"></td>
                    <td width="610"><table width="100%" cellpadding="0" cellspacing="0" border="0" >
                      
                            <tr>
                                <td align="center">&nbsp;</td>
                                <td align="center"><a href="{{ env('APP_URL') }}"><img src="{{ env('APP_URL') }}/images/logo.png" /></a></td>
                            </tr>
                      
                            <tr>
                                <td></td>
                                <td height="25"></td>
                            </tr>
                            <tr>
                                <td style="color:#333132; font-size:15px; font-family:Arial, Helvetica, sans-serif; text-align:left; font-weight:bold;">&nbsp;</td>
                                <td style="color:#333132; font-size:15px; font-family:Arial, Helvetica, sans-serif; text-align:left; font-weight:bold;">Dear {{$user->first_name}} {{$user->last_name}},</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td height="25"></td>
                            </tr>
                            <tr>
                                <td style="color:#333132; font-size:15px; font-family:Arial, Helvetica, sans-serif; text-align:left;">&nbsp;</td>
                                <td style="color:#333132; font-size:15px; font-family:Arial, Helvetica, sans-serif; text-align:left; line-height:24px;"">Forgot your password? Don’t worry, it happens to the best of us. Change your password by hitting the button below. If you did not request to change your password, simply ignore this email.</td>
                            </tr>
                            <tr>
                                <td></td>
                                <td height="5"></td>
                            </tr>
                            <tr>
                                <td style="border-bottom:1px solid #ccc;"></td>
                                <td height="10" style="color:#333132; font-size:16px; font-family:Arial, Helvetica, sans-serif; text-align:center;line-height:22px;"><a href="{{ env('APP_URL') }}/reset/{{$user->email}}/{{$code}}">Reset Password</a></td>
                            </tr>
                            <tr>
                                <td></td>
                                <td height="10"></td>
                            </tr>
                        </table></td>
                    <td width="20" bgcolor="#ffffff"></td>
                </tr>
                <tr>
                    <td colspan="3" height="50" bgcolor="#e0e2e4"></td>
                </tr>
            </table></td>
        <td width="75">&nbsp;</td>
    </tr>
</table>
