<?php
return array (
  'meta' => 
  array (
    'title' => 
    array (
      'type' => 'string',
      'length' => 127,
      'value' => '',
    ),
    'email' => 
    array (
      'type' => 'string',
      'length' => 127,
      'value' => '',
    ),
    'subject' => 
    array (
      'type' => 'string',
      'length' => 127,
      'value' => '',
    ),
    'from' => 
    array (
      'type' => 'string',
      'length' => 127,
      'value' => '',
    ),
    'template' => 
    array (
      'type' => 'string',
      'length' => 16383,
      'value' => '',
    ),
    'fields' => 
    array (
      'type' => 'array',
    ),
    'form_fields' => 
    array (
      'type' => 'string',
      'length' => 16383,
      'value' => '',
    ),
    'form_action' => 
    array (
      'type' => 'string',
      'length' => 2047,
      'value' => '',
    ),
    'selector' => 
    array (
      'type' => 'string',
      'length' => 1023,
      'value' => '',
    ),
    'msg_success' => 
    array (
      'type' => 'string',
      'length' => 127,
      'value' => '',
    ),
    'async_headers' => 
    array (
      'type' => 'array',
      'value' => 
      array (
      ),
    ),
    'async_response' => 
    array (
      'type' => 'string',
      'length' => 16383,
      'value' => '',
    ),
  ),
  'data' => 
  array (
    0 => 
    array (
      'title' => 'Новый обработчик',
      'email' => 'maxiesystems@gmail.com',
      'subject' => 'Сообщение с сайта',
      'from' => 'admin@dolly.local',
      'template' => 'Информация о заказе:
Ваше имя: {callback_form_name}
Телефон: {callback_form_phone_num}
Удобное время: {callback_form_text}

Время отправления: {__dolly_timestamp}
IP-адрес отправителя: {__dolly_ip}',
      'fields' => 
      array (
        0 => 
        array (
          0 => 'callback_form_name',
          1 => 'Ваше имя',
          2 => 
          array (
            'required' => true,
          ),
        ),
        1 => 
        array (
          0 => 'callback_form_phone_num',
          1 => 'Телефон',
          2 => 
          array (
            'required' => true,
          ),
        ),
        2 => 
        array (
          0 => 'callback_form_text',
          1 => 'Удобное время',
          2 => 
          array (
          ),
        ),
      ),
      'form_fields' => '[{"node":"input","name":"callback_form_name","type":"text"},{"node":"input","name":"callback_form_phone_num","type":"text"},{"node":"input","name":"callback_form_text","type":"text"}]',
      'form_action' => '/handle.php',
      'selector' => '',
      'msg_success' => 'Ваше сообщение отправлено.',
      'async_headers' => 
      array (
        0 => 'HTTP/1.1 200 OK',
        1 => 'Content-Type: application/json',
        2 => 'Expires: Thu, 19 Nov 1981 08:52:00 GMT',
        3 => 'Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0',
        4 => 'Pragma: no-cache',
      ),
      'async_response' => '{"__status":"success","__message":"\\u041c\\u044b \\u043e\\u0431\\u044f\\u0437\\u0430\\u0442\\u0435\\u043b\\u044c\\u043d\\u043e \\u043f\\u0435\\u0440\\u0435\\u0437\\u0432\\u043e\\u043d\\u0438\\u043c \\u0412\\u0430\\u043c!","__invalid":[]}',
    ),
  ),
  'keys' => 
  array (
  ),
);
?>