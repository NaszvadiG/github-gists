<?php $this->load->view('header'); ?>

<div class="alert">This page reloads every 1 minute</div>

<?php if (isset($search_term) && $search_term != ''): ?>
    <div class="alert alert-error">Gists filtered to those containing '<?php echo $search_term; ?>' in the filename, description or code (<?php echo anchor(base_url(), 'Remove filter'); ?>)</div>
<?php endif; ?>

<?php foreach ($gists as $gist): ?>

    <div class="well">

        <div class="row">
            <div class="span2">

                <?php
                if (isset($gist["user"]["avatar_url"]) && $gist["user"]["avatar_url"] != "")
                {
                    $image_properties = array(
                        'src' => $gist["user"]["avatar_url"],
                        'alt' => (isset($gist["login"]) && $gist["login"] != '') ? $gist["login"] : "Anonymous",
                        'title' => (isset($gist["login"]) && $gist["login"] != '') ? $gist["login"] : "Anonymous",
                        'class' => 'img-polaroid',
                        'width' => '140',
                        'height' => '140'
                    );

                    echo img($image_properties);
                }
                ?>

            </div>
            <div class="span7">

                <?php
                /*
                 * get file information
                 */
                $file_name = key($gist["files"]);
                $file_information_array = $gist["files"][$file_name];
                ?>

                <?php echo date("d/m/Y H:ia", strtotime($gist["created_at"])); ?>

                <strong><?php echo (isset($gist["user"]["login"])) ? $gist["user"]["login"] : "Anonymous"; ?></strong> has created a gist called <strong><?php echo key($gist["files"]); ?></strong>

                <br /><br />

                <span class="label"><?php echo ($file_information_array["language"] != '') ? $file_information_array["language"] : 'Unknown'; ?></span>

                <span class="label"><?php echo $file_information_array["size"]; ?> bytes</span>

                <span class="label"><?php echo $gist["comments"]; ?> comments</span>


                <?php if (isset($gist["user_details"]['location']) || isset($gist["user_details"]['blog']) || isset($gist["user_details"]['hireable'])): ?>

                    <br /><br />

                    <?php if (isset($gist["user_details"]['blog']) && $gist["user_details"]['blog'] != ""): ?>

                        <span class="label"><?php
            if (stristr($gist["user_details"]['blog'], 'twitter') == TRUE)
            {
                echo anchor_popup($gist["user_details"]['blog'], 'Twitter');
            }
            else
            {
                echo anchor_popup($gist["user_details"]['blog'], 'Link');
            }
                        ?></span>

                    <?php endif; ?>

                    <?php if (isset($gist["user_details"]['location']) && $gist["user_details"]['location'] != ""): ?>
                        <span class="label label-success">From <?php echo $gist["user_details"]['location']; ?></span>
                    <?php endif; ?>

                    <?php if (isset($gist["user_details"]['hireable']) && $gist["user_details"]['hireable'] == 1): ?>
                        <span class="label  label-warning">Available to hire!</span>
                    <?php endif; ?>

                <?php endif; ?>

                <br /><br />

                <div class="btn-group">
                    <?php if ($gist["user"]["login"] != ''): ?>
                        <button class="btn"><?php echo anchor_popup('http://github.com/' . $gist["user"]["login"], 'Profile'); ?></button>
                    <?php endif; ?>
                    <button class="btn"><?php echo anchor_popup($gist["html_url"], 'Show Gist'); ?></button>
                </div>

            </div>
        </div>

    </div>

<?php endforeach; ?>

<?php $this->load->view('footer'); ?>