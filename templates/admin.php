<div class="wrap">
    <h1>LogonLabs SSO Connect</h1>
    <?php settings_errors() ?>

    <p class="guide">
        <label>To get started, follow the steps in our configuration guide:</label>
        <a href="https://logonlabs.com/articles/wordpress-guide/" target="_blank"> https://logonlabs.com/articles/wordpress-guide/</a>
    </p>

    <p class="callback">
    <h2>Callback URL</h2>
    <div class="callback_url"><?php echo get_home_url() . '/logonauthorize/'; ?></div>
    <div class="copy"></div>
    </p>

    <form method="post" action="options.php">
        <?php
            settings_fields('logon_option_groups');
            do_settings_sections('logon_sso_connect');
            submit_button();
        ?>
    </form>
</div>
