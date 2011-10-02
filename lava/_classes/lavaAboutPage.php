<?php
class lavaAboutPage extends lavaPage
{
    public $multisiteSupport = true;

    function displayPage()
    {
        ?>
        <h2><?php _e( "Getting Started", $this->_framework() ) ?></h2>
        <div class="content-box-cntr clearfix getting-started">
            <!-- Plugin Settings-->
            <div class="content-box content-box-3">
                <div class="inner">
                    <img class="lava-img-loader" alt="Settings Screenshot" height="auto" width="100%" src="<?php echo plugins_url( "lava/_static/images/settings_getting_started.png", $this->_file() ) ?>"/>
                    <a style="margin-top:10px;" href="#" class="lava-btn lava-btn-plain"><?php _e( "Configure Settings", $this->_framework() ); ?></a>
                </div>
            </div>
            <!-- Plugin Skin-->
            <div class="content-box content-box-3">
                <div class="inner">
                    <img class="lava-img-loader" alt="Skins Screenshot" height="auto" width="100%" src="<?php echo plugins_url( "lava/_static/images/settings_getting_started.png", $this->_file() ) ?>"/>
                    <a style="margin-top:10px;" href="#" class="lava-btn lava-btn-plain"><?php _e( "Configure Appearance", $this->_framework() ); ?></a>
                </div>
            </div>
            <!-- Plugin Extensions-->
            <div class="content-box content-box-3">
                <div class="inner">
                    <img class="lava-img-loader" alt="Extensions screenshot" height="auto" width="100%" src="<?php echo plugins_url( "lava/_static/images/settings_getting_started.png", $this->_file() ) ?>"/>
                    <a style="margin-top:10px;" href="#" class="lava-btn lava-btn-plain"><?php _e( "Enable extensions", $this->_framework() ); ?></a>
                </div>
            </div>
        </div>
        <?php
    }
}
?>