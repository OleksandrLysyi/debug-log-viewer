<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DLV_LogView
{
    public static function dlv_render_view()
    {
        $path = DLV_LogController::dlv_get_debug_file_path();
        ?>
        <div class="container dlv-log-viewer">
            <div class="row">
                <div class="col-md-9">

                    <?php if ($path && file_exists($path) && is_file($path)) { ?>
                        <div class="top-section">
                            <div class="log-filepath">
                                Path: <span class=""><?php echo esc_html($path); ?></span>
                            </div>

                            <div class="buttons">
                                <button class="btn btn-primary clear-log" title="<?php esc_attr_e('Clear', 'debug-log-viewer'); ?>"><i class="fa fa-solid fa-trash"></i></button>
                                <button class="btn btn-primary download-log" title="<?php esc_attr_e('Download', 'debug-log-viewer'); ?>"><i class="fa-solid fa-cloud-arrow-down"></i></button>
                                <button class="btn btn-success live-update" title="<?php esc_attr_e('Live log updates is active', 'debug-log-viewer'); ?>"><i class="fa-solid fa-tower-cell"></i></button>
                            </div>
                        </div>
                    <?php } ?>

                    <?php
                    if (!DLV_LogModel::dlv_is_log_file_exists()) {
                        require_once realpath(__DIR__) . '/../components/log-missing-debug-file.tpl.php';
                    } else { ?>
                        <div class="table-wrapper">

                            <div class="modal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <table id="dlv_log-table" class="display" style="width:100%">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Type', 'debug-log-viewer'); ?></th>
                                        <th><?php esc_html_e('Datetime', 'debug-log-viewer'); ?></th>
                                        <th><?php esc_html_e('Description', 'debug-log-viewer'); ?></th>
                                        <th><?php esc_html_e('File', 'debug-log-viewer'); ?></th>
                                        <th><?php esc_html_e('Line', 'debug-log-viewer'); ?></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    <?php } ?>
                </div>

                <div class="col-md-3 sidebar">
                    <div class="settings">
                        <h5><?php esc_html_e('Settings', 'debug-log-viewer') ?></h5>

                        <div class="row log-viewer-row">
                            <div class="log-info-block">
                                <p><?php esc_html_e('Debug mode', 'debug-log-viewer'); ?></p>
                                <input id="dlv_toggle_debug_mode" type="checkbox" <?php checked(WP_DEBUG, true); ?> name="checkbox" class="bootstrap-switch" />
                            </div>
                            <div class="log-info-block">
                                <p><?php esc_html_e('Debug scripts', 'debug-log-viewer'); ?></p>
                                <input id="dlv_toggle_debug_scripts" type="checkbox" <?php checked(SCRIPT_DEBUG, true); ?> name="checkbox" class="bootstrap-switch" />
                            </div>

                            <div class="log-info-block">
                                <p><?php esc_html_e('Log in file', 'debug-log-viewer'); ?></p>
                                <p class="disabled"></p>
                                <input id="dlv_toggle_debug_log_scripts" type="checkbox" <?php checked(WP_DEBUG_LOG, true); ?> name="checkbox" class="bootstrap-switch" />
                            </div>

                            <div class="log-info-block">
                                <p><?php esc_html_e('Display errors', 'debug-log-viewer'); ?></p>
                                <input id="dlv_toggle_display_errors" type="checkbox" <?php checked(WP_DEBUG_DISPLAY, true); ?> name="checkbox" class="bootstrap-switch" />
                            </div>
                        </div>
                    </div>

                    <div class="notifications">
                        <h5><?php esc_html_e('Notifications', 'debug-log-viewer') ?></h5>

                        <?php $notificator = new DLV_Notificator(new DLV_LogController()); ?>
                        <form class="form-group" id="dlv_log_viewer_notifications_form" data-notifications-enabled="<?php echo esc_attr($notificator->dlv_is_notification_enabled() ? 'true' : 'false'); ?>">
                            <p><?php esc_html_e('You will receive an email notification in case a serious problem is detected on the website', 'debug-log-viewer') ?></p>
                            <p><?php esc_html_e('Monitoring tracks database, fatal, deprecated and parse errors', 'debug-log-viewer') ?></p>
                            <label for="email"><?php esc_html_e('Your Email:', 'debug-log-viewer') ?></label>
                            <input type="email" id="email" value="<?php echo esc_attr($notificator->dlv_get_notification_email()); ?>">

                            <label for="recurrence"><?php esc_html_e('Periodicity:', 'debug-log-viewer') ?></label>
                            <select name="recurrence" id="recurrence">
                                <?php $notificator->dlv_get_notification_recurrence(); ?>
                            </select>
                            <?php require_once realpath(__DIR__) . '/../components/send-test-email-checkbox.php'; ?>

                            <input type="submit" value="<?php esc_attr_e('Loading...', 'debug-log-viewer'); ?>" class="btn btn-secondary btn-sm" disabled>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
