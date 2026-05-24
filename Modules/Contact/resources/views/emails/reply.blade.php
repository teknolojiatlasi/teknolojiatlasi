<!DOCTYPE html>
<html lang="tr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $contactMessage->contact_subject }}</title>
    </head>
    <body>
        <p>Merhaba {{ $contactMessage->contact_full_name }},</p>
        <p style="white-space: pre-wrap">{{ $replyMessage }}</p>
        <hr>
        <p style="color:#666;font-size:12px">
            Bu e-posta, “{{ $contactMessage->contact_subject }}” konulu iletişiminize yanıt olarak gönderilmiştir.
        </p>
    </body>
</html>

