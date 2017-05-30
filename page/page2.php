<html>
    <body>
        <table style="width: 100%;">
            <tr>
                <td style="text-align: left;">Hello <?= $GLOBALS['_USER_']->getUsername();?></td>
                <td></td>
                <td style="text-align: right;"><a href="/logout">LogOut</a></td>
            </tr>
        </table>
    </body>
</html>