<?php
class lavaSkinsPage extends lavaSettingsPage
{
    public $multisiteSupport = true;
    public $who = "skins";
    public $toolbarClasses = "toolbar-skins";

    function displayUnderground()
    {
        ?>
        <div class="skin-selector">
            
        </div>
        <?php
    }
}
?>