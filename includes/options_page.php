<?php
if ( ! defined( 'ABSPATH' ) )
	die('You Should not be here');
$options = get_option('telegram_posts');
?>
<div class="wrap">

    <div id="icon-options-general" class="icon32"></div>
    <h1><?php esc_attr_e( 'Settings', 'WpAdminStyle' ); ?></h1>

    <div id="poststuff">

        <div id="post-body" class="metabox-holder columns-2">

            <!-- main content -->
            <div id="post-body-content">

                <div class="meta-box-sortables ui-sortable">

                    <div class="postbox">

                        <h2><span><?php esc_attr_e( 'Bot Settings', 'WpAdminStyle' ); ?></span></h2>

                        <div class="inside">
                            <form action="" method="post">
                                <table class="form-table">
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell">Bot Token</label></td>
                                        <td><input name="tgwp_token" id="tgwp_token" type="text" class="regular-text" value="<?php echo isset($options['tgwp_token']) ? $options['tgwp_token'] : "" ?>" autocomplete="false" required placeholder="231231231:abcdefghjayudwl3O1iTD9_kdXAV-anTNt0" /></td>
                                    </tr>
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell">Channel Name</label></td>
                                        <td><textarea name="tgwp_channels" id="tgwp_channels" cols="60" rows="10" placeholder="@Channel1&#10;@Channel2"><?php echo isset($options['tgwp_channels']) ? $options['tgwp_channels'] : "" ?></textarea><br></td>
                                    </tr>
                                    <tr valign="top">
                                        <td scope="row"><label for="tablecell">Read More Caption</label></td>
                                        <td><input name="tgwp_readmore" id="tgwp_readmore" type="text" class="regular-text" value="<?php echo isset($options['tgwp_readmore']) ? $options['tgwp_readmore'] : "[🌐] Read More..." ?>" autocomplete="false" required placeholder="[🌐] Read More..." /></td>
                                    </tr>
                                </table>
                                <input class="button-primary" type="submit" name="tgwp_submit" value="Save" />
                            </form>
                        </div>
                        <!-- .inside -->

                    </div>
                    <!-- .postbox -->

                </div>
                <!-- .meta-box-sortables .ui-sortable -->

            </div>
            <!-- post-body-content -->
        </div>
        <!-- #post-body .metabox-holder .columns-2 -->

        <br class="clear">
    </div>
    <!-- #poststuff -->

</div> <!-- .wrap -->