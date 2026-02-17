<?php
/**
 * Vue admin : timeline des dépôts d'inscriptions par heure.
 */

class InssetProjet_View_Timeline {

    public function display() {
        // Récupère les effectifs par heure (0-23) depuis le CRUD.
        $stats = InssetProjet_Crud_Inscription::get_hourly_stats();
        $total = array_sum($stats);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e('Timeline des dépôts', 'inssetprojet'); ?></h1>

            <?php if ($total === 0) : ?>
                <p><?php esc_html_e('Aucune inscription pour le moment, la timeline sera affichée dès qu\'il y aura des données.', 'inssetprojet'); ?></p>
            <?php else : ?>

                <p><?php esc_html_e('Nombre d\'inscriptions déposées en fonction de l\'heure (0h à 23h).', 'inssetprojet'); ?></p>

                <div id="inssetprojet-timeline-chart" style="max-width: 800px; height: 400px;"></div>

                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                <script type="text/javascript">
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawInssetProjetTimelineChart);

                    function drawInssetProjetTimelineChart() {
                        var data = new google.visualization.DataTable();
                        // Axe des X numérique (0 à 23) avec libellés formatés "8H", etc.
                        data.addColumn('number', '<?php echo esc_js(__('Heure', 'inssetprojet')); ?>');
                        data.addColumn('number', '<?php echo esc_js(__('Nombre d\'inscriptions', 'inssetprojet')); ?>');

                        data.addRows([
                            <?php
                            $rows_js = array();
                            foreach ($stats as $hour => $count) {
                                $h     = (int) $hour;
                                $label = sprintf('%dH', $h);
                                // {v: 8, f: "8H"} permet d'avoir un axe numérique avec un label "8H"
                                $rows_js[] = "[{v: {$h}, f: '" . esc_js($label) . "'}, " . (int) $count . "]";
                            }
                            echo implode(",\n                            ", $rows_js);
                            ?>
                        ]);

                        var options = {
                            title: '<?php echo esc_js(__('Inscriptions par heure de dépôt', 'inssetprojet')); ?>',
                            curveType: 'function',
                            legend: { position: 'bottom' },
                            chartArea: { left: 60, right: 20, top: 40, bottom: 60, width: '80%', height: '60%' },
                            hAxis: {
                                title: '<?php echo esc_js(__('Heure de la journée', 'inssetprojet')); ?>',
                                // Un tick pour chaque heure de 0H à 23H.
                                ticks: [
                                    <?php
                                    $ticks = array();
                                    for ($h = 0; $h < 24; $h++) {
                                        $ticks[] = (string) $h;
                                    }
                                    echo implode(', ', $ticks);
                                    ?>
                                ]
                            },
                            vAxis: {
                                title: '<?php echo esc_js(__('Nombre d\'inscriptions', 'inssetprojet')); ?>',
                                minValue: 0
                            }
                        };

                        var chart = new google.visualization.LineChart(
                            document.getElementById('inssetprojet-timeline-chart')
                        );
                        chart.draw(data, options);
                    }
                </script>

            <?php endif; ?>
        </div>
        <?php
    }
}

