<?php

$Betreff = "Hier kommt der Betreff";
$Nachricht = "Die Nachricht";

$Absender_name = "Absendername";
$absender_email = "Emailadresse@email.de";

// Bitte hier die PDF Datei
$datei = "test.pdf";






/////////////// NICHTS DANACH ÄNDERN
$empfaenger = $_GET['mail'];

function mail_att($to, $subject, $message, $sender, $sender_email, $reply_email, $dateien) {
   if(!is_array($dateien)) {
      $dateien = array($dateien);
   }

   $attachments = array();
   foreach($dateien AS $key => $val) {
      if(is_int($key)) {
        $datei = $val;
        $name = basename($datei);
     } else {
        $datei = $key;
        $name = basename($val);
     }

      $size = filesize($datei);
      $data = file_get_contents($datei);
      $type = mime_content_type($datei);

      $attachments[] = array("name"=>$name, "size"=>$size, "type"=>$type, "data"=>$data);
   }

   $mime_boundary = "-----=" . md5(uniqid(microtime(), true));
   $encoding = mb_detect_encoding($message, "utf-8, iso-8859-1, cp-1252");

   $header  = 'From: "'.addslashes($sender).'" <'.$sender_email.">\r\n";
   $header .= "Reply-To: ".$reply_email."\r\n";

   $header .= "MIME-Version: 1.0\r\n";
   $header .= "Content-Type: multipart/mixed; charset=\"$encoding\"\r\n";
   $header .= " boundary=\"".$mime_boundary."\"\r\n";


   $content  = "This is a multi-part message in MIME format.\r\n\r\n";
   $content .= "--".$mime_boundary."\r\n";
   $content .= "Content-Type: text/html; charset=\"$encoding\"\r\n";
   $content .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
   $content .= $message."\r\n";

   //$anhang ist ein Mehrdimensionals Array
   //$anhang enthält mehrere Dateien
   foreach($attachments AS $dat) {
         $data = chunk_split(base64_encode($dat['data']));
         $content.= "--".$mime_boundary."\r\n";
         $content.= "Content-Disposition: attachment;\r\n";
         $content.= "\tfilename=\"".$dat['name']."\";\r\n";
         $content.= "Content-Length: .".$dat['size'].";\r\n";
         $content.= "Content-Type: ".$dat['type']."; name=\"".$dat['name']."\"\r\n";
         $content.= "Content-Transfer-Encoding: base64\r\n\r\n";
         $content.= $data."\r\n";
   }
   $content .= "--".$mime_boundary."--";

   return mail($to, $subject, $content, $header);
}
mail_att($empfaenger, $Betreff, $Nachricht, $Absender_name, $absender_email, $absender_email, $datei);
?>
