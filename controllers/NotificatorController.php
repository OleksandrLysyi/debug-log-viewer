<?php

if (!defined('ABSPATH')) {
    exit;
    // Exit if accessed directly
}


class DLV_Notificator
{
    public $action;
    public $options;
    public $email_recurrences;
    public $send_test_email_handler;

    public function __construct($instance)
    {
        $dlv_log_controller = new DLV_LogController();

        if ($instance instanceof $dlv_log_controller) {
            $this->action = DLV_LogController::SCHEDULE_MAIL_SEND;
            $this->send_test_email_handler = [$this, 'dlv_send_log_viewer_test_email'];
        }
        $this->email_recurrences = [
            'hourly'     => __('Hourly', 'debug-log-viewer'),
            'twicedaily' => __('Twice Daily', 'debug-log-viewer'),
            'daily'      => __('Daily',  'debug-log-viewer'),
            'weekly'     => __('Weekly', 'debug-log-viewer'),
        ];
        $this->options = get_option($this->dlv_build_unique_event_name());
    }

    public function dlv_build_unique_event_name()
    {
        return strtoupper($this->action . '_user_' . wp_get_current_user()->ID);
    }

    public function dlv_get_notification_email()
    {
        if ($this->options && array_key_exists('notifications_email', $this->options)) {
            return $this->options['notifications_email'];
        }
    }

    public function dlv_is_notification_enabled()
    {
        return (bool) $this->options;
    }

    public function dlv_get_notification_recurrence()
    {
        foreach ($this->email_recurrences as $key => $value) {
            $selected = $key == $this->dlv_get_notification_recurrences() ? 'selected="selected"' : '';
            // Use esc_attr() for the value and the selected attribute, and esc_html() for the display text
            echo sprintf(
                '<option value="%s" %s>%s</option>',
                esc_attr($key),         // Escape the value for safety
                $selected,              // Do not escape the selected attribute (it is already safe)
                esc_html($value)        // Escape the label for safety
            );
        }        
    }

    private function dlv_get_notification_recurrences()
    {
        if (!$this->options) {
            return null;
        }

        if (array_key_exists('notifications_email_recurrence', $this->options)) {
            return $this->options['notifications_email_recurrence'];
        }
        return null;
    }

    private function dlv_send_log_viewer_test_email($args)
    {
        global $DLV_LOG_VIEWER_EMAIL_LEVELS;

        $email = $args['notifications_email'];

        if (!isset($email)) {
            return;
        }

        $errors = [];
        foreach ($DLV_LOG_VIEWER_EMAIL_LEVELS as $type) {
            $timestamp = new DateTime();
            $datetime = $timestamp->format('Y-m-d H:i:s e');
            $text = $type . ' error test description';
            $hash = md5($text . '::' . $datetime);

            $errors[$type][$hash] = [
                'datetime'    => $datetime,
                'line'        => '1',
                'file'        => 'example.php',
                'type'        => $type,
                'description' => [
                    'text' => $text,
                    'stack_trace' => null,
                ],
                'hits' => 1,
            ];
        }

        dlv_send_log_viewer_email(
            $email,
            __('Debug Log Viewer: Log monitoring test email', 'debug-log-viewer'),
            realpath(__DIR__) . '/../templates/email/log_viewer.tpl',
            [
                'website' => get_site_url(),
                'errors' => $errors,
            ]
        );
    }

    public function dlv_send_test_email($options)
    {
        if (isset($this->send_test_email_handler) && is_callable($this->send_test_email_handler)) {
            call_user_func($this->send_test_email_handler, $options);
        }
    }
}
