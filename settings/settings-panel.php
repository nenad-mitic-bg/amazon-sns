<?php
$settings = ASNS_Settings::get_settings();
?>
<div class="wrap">

    <h2>Amazon SNS Settings</h2>

    <form action='options.php' method='post'>
        <?php settings_fields('asns') ?>

        <table class="form-table">
            <tbody>

                <tr>
                    <th scope="row">
                        <label for="amazon_key">Amazon Key</label>
                    </th>

                    <td>
                        <input type="text" 
                               class="regular-text" 
                               value="<?php echo $settings['amazon_key'] ?>" 
                               id="amazon_key" 
                               name="asns_settings[amazon_key]" />
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="amazon_secret">Amazon Secret</label>
                    </th>

                    <td>
                        <input type="text" 
                               class="regular-text" 
                               value="<?php echo $settings['amazon_secret'] ?>" 
                               id="amazon_secret" 
                               name="asns_settings[amazon_secret]" />
                    </td>
                </tr>

                <tr>
                    <th scope="row">
                        <label for="amazon_region">Region</label>
                    </th>

                    <td>
                        <input type="text" 
                               class="regular-text" 
                               value="<?php echo $settings['amazon_region'] ?>" 
                               id="amazon_region" 
                               name="asns_settings[amazon_region]" />
                    </td>
                </tr>

            </tbody>

        </table>

        <h3 class="title">Apps</h3>
        <p>
            <a id="add-app" class="button" href="#">Add App</a>
        </p>

        <table class="form-table">
            <tbody id="apps-parent">

                <?php for ($i = 0; $i < count($settings['app_keys']); $i++) : ?>

                    <tr>
                        <td>
                            <input type="text" 
                                   value="<?php echo $settings['app_keys'][$i] ?>" 
                                   placeholder="App Name"
                                   name="asns_settings[app_keys][]" />

                            <input type="text" 
                                   class="regular-text" 
                                   value="<?php echo $settings['app_arns'][$i] ?>"  
                                   placeholder="App ARN"
                                   name="asns_settings[app_arns][]" />

                            <a href="#" class="remove-row">Remove</a>
                        </td>
                    </tr>

                <?php endfor ?>

            </tbody>
        </table>

        <h3 class="title">Topics</h3>
        <p><a id="add-topic" class="button" href="#">Add Topic</a></p>

        <table class="form-table">
            <tbody id="topics-parent">

                <?php for ($i = 0; $i < count($settings['topic_keys']); $i++) : ?>

                    <tr>
                        <td>
                            <input type="text" 
                                   value="<?php echo $settings['topic_keys'][$i] ?>" 
                                   placeholder="Topic Name"
                                   name="asns_settings[topic_keys][]" />

                            <input type="text" 
                                   class="regular-text" 
                                   value="<?php echo $settings['topic_arns'][$i] ?>"  
                                   placeholder="App ARN"
                                   name="asns_settings[topic_arns][]" />

                            <a href="#" class="remove-row">Remove</a>
                        </td>
                    </tr>

                <?php endfor ?>

            </tbody>
        </table>

        <p class="submit">
            <input type="submit" 
                   value="Save Changes" 
                   class="button button-primary" 
                   id="submit" 
                   name="submit" />
        </p>
    </form>

    <h3 class="title">Example Request for Subscribing</h3>

    <?php if ($settings['app_keys'] && $settings['topic_keys']) : ?>
        <p>Subscribe a mobile application to receive notifications</p>
        <p>
            <code>
                wget <?php echo admin_url('admin-ajax.php'); ?> -q -S -O - --post-data="action=asns_register_device&app=<?php echo $settings['app_keys'][0] ?>&topic=<?php echo $settings['topic_keys'][0] ?>&token=123"
            </code>
        </p>
    <?php else : ?>
        <p>Add an app and a topic to show the example</p>
    <?php endif ?>

</div>