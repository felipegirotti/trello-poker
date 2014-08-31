<?
$ch = curl_init('http://www.codezine.com.br/pmeter/calc.php?dificuldade=3&linhas=2&impacto=1');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$resultado = curl_exec($ch);
// Encerra a conexão com o site
curl_close($ch);
echo '<pre>';
var_dump($resultado);
echo '</pre>';
die('final');
?>

<!DOCTYPE>
<html>
    <head>
        <meta charset="utf-8">
        <title>Code Challenger</title>
        <link href="assets/css/bootstrap.min.css" rel="stylesheet" />       
        
    </head>
    <body>
        <div id="page">
            <div class="container" id="container">
                <header>
                    <h1><a href="/">Code Challenger</a></h1>
                </header>
                <div class="alert alert-danger" id="error"></div>
                <div class="row">
                    <div class="col-lg-6">                        
                        <div class="alert alert-default" id="label-run">
                            <div class="row">                              
                                <div class="col-lg-10">Clique no botão ao lado para enviar</div>
                                <div class="col-lg-2">
                                    <div id="btn-run" class="btn btn-success ">Run</div>
                                </div>                                
                            </div>
                        </div>
                        <pre id="editor"></pre>
                    </div>
                    <div class="col-lg-6">
                        <div class="alert alert-default" id="label-result">
                            
                        </div>
                        <div id="container-result" class="alert" >
                            <iframe src="" name="preview" id="preview" frameborder="0" scrolling="no" ></iframe>
                        </div>
                        
                    </div>
                </div>
            </div>
            <div class="btn" id="notificacao">Noti</div>
        </div>  
        <form name="form-code" id="form-code" method="post" target="preview">
            <input type="hidden" name="value-code" id="value-code">
            <input type="hidden" name="type" id="type">
        </form>
        <script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
        
        <script>
            function resizeWindow() {
                var width = 600;
                var height = 400;
                window.resizeTo(width, height);
                window.moveTo(((screen.width - width) / 2), ((screen.height - height) / 2));
             }
             function Notifier() {}

    // Returns "true" if this browser supports notifications.
    Notifier.prototype.HasSupport = function() {
      if (window.webkitNotifications) {
        return true;
      } else {
        return false;
      }
    }

    // Request permission for this page to send notifications. If allowed,
    // calls function "cb" with true.
    Notifier.prototype.RequestPermission = function(cb) {
      window.webkitNotifications.requestPermission(function() {
        if (cb) { cb(window.webkitNotifications.checkPermission() == 0); }
      });
    }

    // Popup a notification with icon, title, and body. Returns false if
    // permission was not granted.
    Notifier.prototype.Notify = function(icon, title, body) {
      if (window.webkitNotifications.checkPermission() == 0) {
        var popup = window.webkitNotifications.createNotification(
        icon, title, body);
        popup.show();
        setTimeout(function(){ popup.cancel(); }, '11000');
        return true;
      }

      return false;
    }
    var notifier = new Notifier();
            $(function(){
               resizeWindow();
               $('#btn-run').click(function() {
                  notifier.RequestPermission();
                  console.log('request');
               });
               $('#notificacao').click(function(){
                  if (!notifier.Notify('assets/img/admin/exit.png', 'Título', 'Minha mensagem')) {
                      console.log('Não rolou', window.webkitNotifications.checkPermission());
                  } 
               });
            });
            
        </script>
        
    </body>
</html>