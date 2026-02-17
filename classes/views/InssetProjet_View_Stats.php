<?php
/**
 * Vue admin : statistiques des inscriptions (camembert par civilité).
 */

class InssetProjet_View_Stats {

    public function display() {
        // Récupère les effectifs par civilité depuis le CRUD.
        $stats = InssetProjet_Crud_Inscription::get_civilite_stats();

        // Total pour les pourcentages.
        $total = array_sum($stats);
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e('Statistiques des inscriptions', 'inssetprojet'); ?></h1>

            <?php if ($total === 0) : ?>
                <p><?php esc_html_e('Aucune inscription pour le moment, le graphique sera affiché dès qu\'il y aura des données.', 'inssetprojet'); ?></p>
            <?php else : ?>

                <p><?php esc_html_e('Répartition des inscriptions par civilité.', 'inssetprojet'); ?></p>

                <div id="inssetprojet-civilite-chart" style="max-width: 600px; height: 400px;"></div>

                <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
                <script type="text/javascript">
                    // Charge Google Charts.
                    google.charts.load('current', {'packages':['corechart']});
                    google.charts.setOnLoadCallback(drawInssetProjetCiviliteChart);

                    function drawInssetProjetCiviliteChart() {
                        var data = google.visualization.arrayToDataTable([
                            ['<?php echo esc_js(__('Civilité', 'inssetprojet')); ?>', '<?php echo esc_js(__('Nombre', 'inssetprojet')); ?>'],
                            ['<?php echo esc_js(__('Monsieur', 'inssetprojet')); ?>',     <?php echo (int) $stats['Monsieur']; ?>],
                            ['<?php echo esc_js(__('Madame', 'inssetprojet')); ?>',       <?php echo (int) $stats['Madame']; ?>],
                            ['<?php echo esc_js(__('Mademoiselle', 'inssetprojet')); ?>', <?php echo (int) $stats['Mademoiselle']; ?>],
                            ['<?php echo esc_js(__('Autre', 'inssetprojet')); ?>',        <?php echo (int) $stats['Autre']; ?>]
                        ]);

                        var options = {
                            title: '<?php echo esc_js(__('Répartition par civilité', 'inssetprojet')); ?>',
                            pieHole: 0.4,
                            chartArea: { width: '80%', height: '80%' },
                            legend: { position: 'right' }
                        };

                        var chart = new google.visualization.PieChart(document.getElementById('inssetprojet-civilite-chart'));
                        chart.draw(data, options);
                    }
                </script>

            <?php endif; ?>
        </div>
        <?php
    }
}

