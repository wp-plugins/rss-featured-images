<?php
abstract class PageBase {
    abstract public function Name();
}

class MainPage extends PageBase{
    private $m_OptionsGroup = "";
    private $m_sAlignmentOptionName = "";
    private $m_sSizeOptionName = "";
    private $m_sBeginWrapperOptionName = "";
    private $m_sEndWrapperOptionName = "";
    private $m_sEnableAlignmentOptionName = "";

    private $m_sAlignmentOptionValue = null;
    private $m_sSizeOptionValue = null;
    private $m_sBeginWrapperOptionValue = null;
    private $m_sEndWrapperOptionValue = null;
    private $m_sEnableAlignmentOptionValue = null;


    function __construct($optionsGroup, $sAlignmentOptionName, $sSizeOptionName,
                         $sBeginWrapperOptionName, $sEndWrapperOptionName, $sEnableAlignmentOptionName) {
        $this->m_sBeginWrapperOptionName = $sBeginWrapperOptionName;
        $this->m_sEndWrapperOptionName = $sEndWrapperOptionName;
        $this->m_sEnableAlignmentOptionName = $sEnableAlignmentOptionName;
        $this->m_OptionsGroup = $optionsGroup;
        $this->m_sAlignmentOptionName = $sAlignmentOptionName;
        $this->m_sSizeOptionName = $sSizeOptionName;
        $this->m_sAlignmentOptionValue = PxrUtils::Instance()->GetOptionValue($this->m_sAlignmentOptionName);
        $this->m_sSizeOptionValue = PxrUtils::Instance()->GetOptionValue($this->m_sSizeOptionName);
        $this->m_sEnableAlignmentOptionValue = PxrUtils::Instance()->GetOptionValue($this->m_sEnableAlignmentOptionName);
        $this->m_sBeginWrapperOptionValue = PxrUtils::Instance()->GetOptionValue($this->m_sBeginWrapperOptionName);
        $this->m_sEndWrapperOptionValue = PxrUtils::Instance()->GetOptionValue($this->m_sEndWrapperOptionName);
    }
    public function Name() {
        return "Main";
    }
    private function invokeInitScript() {
        ?>

        <script>
            <?php
                if(function_exists("site_url"))
                    print("var siteRoot = '" . site_url(). "';\n");
                else
                    print("var siteRoot = '/';\n");
            ?>
            //Invoke document ready...
            jQuery(document).ready(function ($) {
                initMainScreen(siteRoot, $);
            });
        </script>

    <?php
    }
    public function DisplayPage() {
        $imageSizes = get_intermediate_image_sizes();
    ?>
    <div id="fi-main-page">
        <div class="container-fluid">
            <div class="row" id="plugin-main-content-area">
                <div class="col-lg-5">
                    <div style="padding-top: 0px;">
                        <h1>RSS Featured Images</h1>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div style="margin-top: -45px; margin-bottom: 0px;">
                        <img src="<?php print(plugins_url('/images/phxr-twitter-small.jpg',__FILE__)); ?>" style="width:360px; height:180px;"/>
                    </div>

                </div>
                <div class="col-lg-4"></div>
            </div>
            <div class="row" id="plugin-main-content-area">
                <div class="col-lg-6"></div>
                <div class="col-lg-3">
                    <div style="padding: 5px 0px 20px; 0px">Plugin by <a href="http://www.phoenixroberts.com">Phoenix Roberts</a></div>
                </div>
                <div class="col-lg-3"></div>
            </div>
        </div>
    </div>
    <form method="post" action="options.php">
        <?php settings_fields( 'rss_fi_group' ); ?>
        <div class="container-fluid">
            <div class="row" id="plugin-main-content-area">
                <div class="col-lg-4">
                    <div id="fi-plugin-settings" style="padding-top: 0px;">
                        <div style="float:left; margin-right: 3px;">
                            Image Size:
                        </div>
                        <img class="help-icon" data-toggle="tooltip" data-placement="right" data-original-title="Select a Wordpress generated featured image size" src="<?php print(plugins_url('/images/Ambox_question.png',__FILE__)); ?>" />
                        <div style="padding: 5px 0px 40px 40px;">
                            <select name="<?php print($this->m_sSizeOptionName); ?>">
                                <?php
                                foreach ($imageSizes as $sizeType) {
                                    $optSettings = 'value="' . $sizeType . '"';
                                    if($this->m_sSizeOptionValue == $sizeType)
                                        $optSettings .= ' selected="selected"';
                                    ?>
                                    <option <?php print($optSettings); ?>>
                                        <?php echo $sizeType; ?>
                                    </option>
                                <?php
                                }
                                ?>
                            </select>
                        </div>
                        <div style="float: left; margin-right: 6px;">
                            <input type="radio"
                                   name="<?php print($this->m_sEnableAlignmentOptionName); ?>" value="true"
                                <?php $this->m_sEnableAlignmentOptionValue == "true" ? print('checked') : print('')?> >
                        </div>
                        <div style="padding: 4px; 0px 0px 0px; float:left; margin-right: 3px;">
                            Image Alignment:
                        </div>
                        <img class="help-icon" data-toggle="tooltip" data-placement="right" data-original-title="Select from one of four preformatted styles" src="<?php print(plugins_url('/images/Ambox_question.png',__FILE__)); ?>" />
                        <div style="clear: both; padding: 5px 0px 30px 40px;">
                            <select name="<?php print($this->m_sAlignmentOptionName); ?>">
                                <option value="centered" <?php echo $this->m_sAlignmentOptionValue == 'centered'?'selected="selected"':''; ?>>Image Centered - Above Text</option>
                                <option value="left" <?php echo $this->m_sAlignmentOptionValue == 'left'?'selected="selected"':''; ?>>Image Left - Above Text</option>
                                <option value="left-wrap" <?php echo $this->m_sAlignmentOptionValue == 'left-wrap'?'selected="selected"':''; ?>>Image Left - Text Wraps</option>
                                <option value="right-wrap" <?php echo $this->m_sAlignmentOptionValue == 'right-wrap'?'selected="selected"':''; ?>>Image Right - Text Wraps</option>
                            </select>
                        </div>
                        <div style="float: left; margin-right: 6px;">
                            <input type="radio"
                                   name="<?php print($this->m_sEnableAlignmentOptionName); ?>" value="false"
                                <?php $this->m_sEnableAlignmentOptionValue == "false" ? print('checked') : print('')?> >
                        </div>
                        <div style="padding: 4px; 0px 0px 0px; float:left; margin-right: 3px;">
                            Wrapper HTML:
                        </div>
                        <img class="help-icon" data-toggle="tooltip" data-placement="right" data-original-title="Create your own image format by specifying prefix and postfix HTML to wrap the image with." src="<?php print(plugins_url('/images/Ambox_question.png',__FILE__)); ?>" />
                        <div style="clear: both; padding: 5px 0px 25px 40px;">
                            Prefix:
                            <br/>
                            <textarea rows="2" cols="40" name="<?php print($this->m_sBeginWrapperOptionName); ?>"><?php print($this->m_sBeginWrapperOptionValue); ?></textarea>
                        </div>
                        <div style="padding: 0px 0px 25px 40px;">
                            Postfix:
                            <br/>
                            <textarea rows="2" cols="40" name="<?php print($this->m_sEndWrapperOptionName); ?>"><?php print($this->m_sEndWrapperOptionValue); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4" >

                </div>
                <div class="col-lg-4"></div>
            </div>
            <div class="row" id="plugin-main-content-area">
                <div class="col-lg-4"></div>
                <div class="col-lg-4">

                </div>
                <div class="col-lg-4"></div>
            </div>
        </div>
        <p class="submit" style="padding-top: 0px;">
            <input type="submit" name="submit-bpu" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
    <hr/>
    <div class="container-fluid" style="padding-top: 0px;">
        <div class="row" id="plugin-main-content-area">
            <div class="col-lg-1">

            </div>
            <div class="col-lg-4">
                <div style="padding-bottom: 25px;">
                    Feature request?
                    <br/>
                    Questions or Suggestions?
                    <br/>
                    Problems with this plugin?
                    <br/><br/>
                    We value your feed back!
                    <br/>
                    Contact us <a href="http://www.phoenixroberts.com/">here</a> or email us at contact@phoenixroberts.com
                </div>
            </div>

            <div class="col-lg-5" style="font-size: 20px;">
                <strong>
                    Need Wordpress development done? <a href="http://www.phoenixroberts.com/">Contact us.</a>
                    <br/>
                    We'd love to work with you!
                </strong>
            </div>
            <div class="col-lg-3">

            </div>
        </div>
    </div>

        <?php
        $this->invokeInitScript();
    }
}
?>