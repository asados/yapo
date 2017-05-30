<?php
$alert = (isset($GLOBALS['_GET']['fail']) && $GLOBALS['_GET']['fail'])? '<h1 style="color: red;">Login Fail</h1>':'';
?>
<html>
    <body>
        <?=$alert?>
      <form action="./login" method="POST"> 
        <table>
           <tr>
             <td>Login:</td>
             <td><input type="text" name="username" required="true"></td>
           </tr>
           <tr>
             <td>Password:</td>
             <td><input type="password" name="password" required="true"></td>
           </tr>
           <tr>
               <td colspan="2"><input type="submit"></td>
           </tr>
        </table>
      </form>
    </body>
</html>
