<?php

/**
 * The person who wrote this class: t.me/sadiyuz
 * 
 * If you also need the perfect Telegram bot, get in touch!
 */

class Telegram 
{
    protected string $bot_token;
    public const TELEGRAM_BOT_API = "https://api.telegram.org/bot";

    public function __construct(string $bot_token)
    {
        $this->bot_token = $bot_token;
    }

    public function request($method, $params = []) {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, self::TELEGRAM_BOT_API . $this->bot_token . "/" . $method);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            if (curl_error($ch)) {
                file_put_contents('log', curl_error($ch));
            }
            curl_close($ch);
            return json_decode($response, true);
        } catch (Exception $e) {
            error_log('TelegramAPI: ' . $e->getMessage());
        }
    }

    public function getWebhookUpdate() {
        return json_decode(file_get_contents('php://input'), true);
    }

    public function sendMessage($chatId, $text, $mode = 'html', $msgId = null, $keyboard = null) {
        $data = $this->request('sendmessage', [
            'chat_id' => $chatId, 
            'text' => $text,
            'parse_mode' => $mode,
            'disable_web_page_preview' => true,
            'reply_to_message_id' => $msgId,
            'reply_markup' => $keyboard
        ]);
        return $data;
    }

    public function sendPhoto($chatId, $photo, $caption = '', $mode = 'html', $keyboard = null, $opt = []) {
        $params = [
            'chat_id' => $chatId, 
            'photo' => $photo,
            'caption' => $caption,
            'parse_mode' => $mode,
            'reply_markup' => $keyboard
        ];
        $data = array_merge($params, $opt);
        $response = $this->request('sendPhoto', $data);
        return $response;
    }

    public function sendVideo($chatId, $video, $caption = '', $mode = 'html', $keyboard = null, $opt = []) {
        $params = [
            'chat_id' => $chatId, 
            'video' => $video,
            'caption' => $caption,
            'parse_mode' => $mode,
            'reply_markup' => $keyboard
        ];
        $data = array_merge($params, $opt);
        $response = $this->request('sendVideo', $data);
        return $response;
    }

    public function sendDocument($chatId, $document, $caption = '', $mode = 'html', $keyboard = null, $opt = []) {
        $params = [
            'chat_id' => $chatId, 
            'document' => $document,
            'caption' => $caption,
            'parse_mode' => $mode,
            'reply_markup' => $keyboard
        ];
        $data = array_merge($params, $opt);
        $response = $this->request('sendPhoto', $data);
        return $response;
    }

    public function editMessageText($chatId, $text, $mode, $msgId, $keyboard = null, $opt = []) {
        $params = [
            'chat_id' => $chatId, 
            'text' => $text,
            'parse_mode' => $mode,
            'message_id' => $msgId,
            'reply_markup' => $keyboard
        ];
        $data = array_merge($params, $opt);
        $response = $this->request('editMessageText', $data);
        return $response;
    }

    public function editMessageCaption($chatId, $caption, $mode, $msgId, $keyboard = null, $opt = []) {
        $params = [
            'chat_id' => $chatId, 
            'caption' => $caption,
            'parse_mode' => $mode,
            'message_id' => $msgId,
            'reply_markup' => $keyboard
        ];
        $data = array_merge($params, $opt);
        $response = $this->request('editMessageCaption', $data);
        return $response;
    }

    public function editMessageReplyMarkup($chatId, $msgId, $keyboard = null) {
        $params = [
            'chat_id' => $chatId, 
            'message_id' => $msgId,
            'reply_markup' => $keyboard
        ];
        $response = $this->request('editMessageReplyMarkup', $params);
        return $response;
    }

    public function deleteMessage($chatId, $msgId) {
        $params = [
            'chat_id' => $chatId, 
            'message_id' => $msgId
        ];
        $response = $this->request('deleteMessage', $params);
        return $response;
    }

    public function answerCallbackQuery($callbackQueryId, $text, $show_alert = false) {
        $response = $this->request('answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
            'show_alert' => $show_alert
        ]);
    }

    public function reply($text, $keyboard = null) {
        $update = $this->getWebhookUpdate();
        $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
        $msgId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'];

        return $this->sendMessage($chatId, $text, 'html', $msgId, $keyboard);
    }

    public function replyWithPhoto($photo, $caption = null, $keyboard = null) {
        $update = $this->getWebhookUpdate();
        $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
        $msgId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'];

        $params = ['reply_to_message_id' => $msgId];
        return $this->sendPhoto($chatId, $photo, $caption, 'html', $keyboard, $params);
    }

    public function replyWithVideo($video, $caption = null, $keyboard = null) {
        $update = $this->getWebhookUpdate();
        $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
        $msgId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'];

        $params = ['reply_to_message_id' => $msgId];
        return $this->sendVideo($chatId, $video, $caption, 'html', $keyboard, $params);
    }

    public function replyWithDocument($document, $caption = null, $keyboard = null) {
        $update = $this->getWebhookUpdate();
        $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
        $msgId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'];

        $params = ['reply_to_message_id' => $msgId];
        return $this->sendDocument($chatId, $document, $caption, 'html', $keyboard, $params);
    }


    public function editReplyMessageText($text, $keyboard = null) {
        $update = $this->getWebhookUpdate();
        $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
        $msgId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'];

        return $this->editMessageText($chatId, $text, 'html', $msgId, $keyboard);
    }

    public function editReplyMessageCaption($caption, $keyboard = null) {
        $update = $this->getWebhookUpdate();
        $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
        $msgId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'];

        return $this->editMessageCaption($chatId, $caption, 'html', $msgId, $keyboard);
    }

    public function editReplyMessageKeyboard($keyboard) {
        $update = $this->getWebhookUpdate();
        $chatId = $update['message']['chat']['id'] ?? $update['callback_query']['message']['chat']['id'];
        $msgId = $update['message']['message_id'] ?? $update['callback_query']['message']['message_id'];

        return $this->editMessageReplyMarkup($chatId, $msgId, $keyboard);
    }

    public function replyCallbackQuery($text, $show_alert = false) {
        $update = $this->getWebhookUpdate();
        $callbackQueryId = $update['callback_query']['id'];
        return $this->answerCallbackQuery($callbackQueryId, $text, $show_alert);
    }
}
