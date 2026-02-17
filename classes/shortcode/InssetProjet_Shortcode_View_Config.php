<?php

if (is_admin())
    add_shortcode('INSSETPROJET_VIEW_CONFIG', array('InssetProjet_Shortcode_View_Config', 'display'));

class InssetProjet_Shortcode_View_Config {

    public static function display($atts) {

        if (!isset($atts['tablename']) && !isset($atts['basename']) && !isset($atts['plugin']))
            return;

        $msg = false;

        global $wpdb;
        $orderby = self::getNameColOrderBy($atts['tablename']);

        $sql = 'SELECT * FROM  `'. $atts['tablename'] .'` ORDER BY `'. $orderby .'`, `id` ASC';
        $params = $wpdb->get_results($sql, 'ARRAY_A');

        $att_html = '';
        foreach ($atts as $key => $value)
            $att_html .= sprintf('<input type="hidden" id="%s" value="%s" />', $key, $value);

        ?>
            <div class="wrap config" id="inssetprojet_param_update">
                
					<h1 class="wp-heading-inline"><?php print get_admin_page_title(); ?></h1>
                    <?php if (!$msg): $msg = true; ?>
                    <div class="notice notice-info notice-alt is-dismissible hide update-message">
                        <p><?php _e('Successfully updated!'); ?></p>
                    </div>
                <?php endif; ?>
                <table class="wp-list-table widefat fixed striped">
                    <tfoot>
                        <tr>
                            <th colspan="2">
                                <button class="button button-primary left update <?php print $atts['tablename'] ?>" target="_blank">
                                    <i class="fas fa-check"></i>
                                    <?php _e('Update'); ?>
                                </button>
                            </th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php print $att_html; ?>
                        <?php foreach ($params as $param): ?>
								<tr>
									<th class="smallwidth" style="text-transform: capitalize;">
										<?php print $param['id'] ?>
									</th>
									<td>
										<?php if (preg_match('/^id/i', $param['id'])): ?>
											<input id="<?php print $param['id'] ?>" type="text" value="<?php print $param['value'] ?>" />
										<?php elseif (preg_match('/date|clock/i', $param['id'])): ?>
											<input type="datetime-local" id="<?php print $param['id'] ?>" value="<?php print preg_replace('/\s/', 'T', $param['value']) ?>" />
										<?php elseif (preg_match('/^is|^display/i', $param['id'])): ?>
											<input id="<?php print $param['id'] ?>" type="checkbox" <?php print ((preg_match('/on|true/i', (string) $param['value'])) ? 'checked':'') ?> />
										
                                        
                                        <?php elseif (preg_match('/^nb/i', $param['id'])): ?>
											<input id="<?php print $param['id'] ?>" type="number" min="0" value="<?php print $param['value'] ?>" />
										
                                        
                                            <?php else: ?>
											<input id="<?php print $param['id'] ?>" type="text" value="<?php print $param['value'] ?>" />
										<?php endif; ?>
										<span class="helper-text">
											<?php print $param['description'] ?>
										</span>
									</td>
								</tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php

    }

    private static function getNameColOrderBy($tablename) {

        global $wpdb;

        $sql = 'DESC `'. $tablename .'`';
        $cols = $wpdb->get_col($sql);
        foreach ($cols as $col)
            if (preg_match('/order|rank/i', $col))
                return $col;

        return current($cols);

    }

}