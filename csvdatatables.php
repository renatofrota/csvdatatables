<?php
/**
 * Plugin Name:     CSV to DataTables Converter
 * Plugin URI:      https://www.renatofrota.com/
 * Description:     Creates a shortcode 'csvdatatables' that displays a filterable DataTables from a CSV file. <a href='https://github.com/renatofrota/csvdatatables'>Instructions</a>. Donate in <a href='https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=R58RLRMM8YM6U'>USD</a> or <a href='https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=9JMBDY5QA8X5A'>BRL</a>.
 * Author:          Renato Frota
 * Author URI:      https://www.renatofrota.com/
 * Text Domain:     csvdatatables
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Csvdatatables
 */

// Add Shortcode
function csvdt_display_table( $atts ) {

    // Attributes
    $atts = shortcode_atts(
        array(
            'file' => 'datatables.csv',
            'lang' => 'en',
            'name' => '-csv',
            'info' => 'true',
            'striplines' => '',
            'stripheaders' => '',
            'headers' => '',
            'search' => '',
            'replace' => '',
            'ordercol' => "0",
            'order' => "asc",
            'nosortcols' => "",
            'filter' => "true",
            'filtercol' => "0",
            'profit' => '',
            'minprofit' => '',
            'maxprofit' => '',
            'precision' => '',
            'paging' => "true",
        ),
        $atts,
        'csvdatatables'
    );

    $csvdt_abspath = wp_upload_dir(NULL, NULL)[basedir]."/";

    wp_enqueue_script('jquery-datatables', plugins_url('/assets/js/jquery.dataTables.min.js',__FILE__), array('jquery'), FALSE);
    wp_enqueue_script('datatables-bootstrap', plugins_url('/assets/js/dataTables.bootstrap.min.js',__FILE__), array(), FALSE);
    wp_enqueue_script('datatables-responsive', plugins_url('/assets/js/dataTables.responsive.min.js',__FILE__), array(), FALSE);
    wp_enqueue_style('datatables-bootstrap', plugins_url('/assets/css/dataTables.bootstrap.css',__FILE__), array(), NULL);
    wp_enqueue_style('datatables-responsive', plugins_url('/assets/css/dataTables.responsive.css',__FILE__), array(), NULL);
    wp_enqueue_style('datatables-base', plugins_url('/assets/css/dataTables.base.css',__FILE__), array(), NULL);

    if (file_exists(__DIR__ . "/assets/css/dataTables.custom.css")) {
        wp_enqueue_style('datatables-custom', plugins_url('/assets/css/dataTables.custom.css',__FILE__), array(), NULL);
    }

    if ($atts['filter']) {

        $csvdt_return = '
<script type="text/javascript">

if (typeof(buildFilterRegex) !== "function") {
    function buildFilterRegex(filterValue) {

        if (filterValue.indexOf(\'&\') === -1) {
            return \'[~>]\\\\s*\' + jQuery.fn.dataTable.util.escapeRegex(filterValue) + \'\\\\s*[<~]\';
        } else {
            var tempDiv = document.createElement(\'div\');
            tempDiv.innerHTML = filterValue;

            return \'\\\\s*\' + jQuery.fn.dataTable.util.escapeRegex(tempDiv.innerText) + \'\\\\s*\';
        }
    }
}

jQuery(".view-filter-btns a").click(function(e) {
    var filterValue = jQuery(this).find("span").not(\'.badge\').html().trim();
    var dataTable = jQuery(\'#table'.$atts['name'].'\').DataTable();
    var filterValueRegex;
    if (jQuery(this).hasClass(\'active\')) {
            jQuery(this).removeClass(\'active\');
            jQuery(this).find("i.fa.fa-dot-circle-o").removeClass(\'fa-dot-circle-o\').addClass(\'fa-circle-o\');
        dataTable.column('.$atts['name'].').search(\'\').draw();
    } else {
            jQuery(\'.view-filter-btns .list-group-item\').removeClass(\'active\');
            jQuery(\'i.fa.fa-dot-circle-o\').removeClass(\'fa-dot-circle-o\').addClass(\'fa-circle-o\');
            jQuery(this).addClass(\'active\');
            jQuery(this).find(jQuery("i.fa.fa-circle-o")).removeClass(\'fa-circle-o\').addClass(\'fa-dot-circle-o\');
        filterValueRegex = buildFilterRegex(filterValue);
        dataTable.column('.$atts['filtercol'].')
            .search(filterValueRegex, true, false, false)
            .draw();
    }

    // Prevent jumping to the top of the page
    // when no matching tag is found.
    e.preventDefault();
});
</script>';
    }

    switch ($atts['lang']) {
        case 'pt':
            $csvdt_lang['norecordsfound'] = 'Nenhum Resultado';
            $csvdt_lang['tableshowing'] = 'Mostrando _START_ a _END_ de _TOTAL_ registros';
            $csvdt_lang['tableempty'] = 'Nenhum registro atende os critérios';
            $csvdt_lang['tablefiltered'] = '(filtrados de um total de _MAX_)';
            $csvdt_lang['tablelength'] = 'Mostrar _MENU_ registros';
            $csvdt_lang['tableloading'] = 'Carregando...';
            $csvdt_lang['tableprocessing'] = 'Processando...';
            $csvdt_lang['tablepagesfirst'] = 'Primeira';
            $csvdt_lang['tablepageslast'] = 'Última';
            $csvdt_lang['tablepagesnext'] = 'Próxima';
            $csvdt_lang['tablepagesprevious'] = 'Anterior';
            $csvdt_lang['tableviewall'] = 'Todos';
            break;
        
        default:
            $csvdt_lang['norecordsfound'] = 'No Records Found';
            $csvdt_lang['tableshowing'] = 'Showing _START_ to _END_ of _TOTAL_ entries';
            $csvdt_lang['tableempty'] = 'No entry match the criteria';
            $csvdt_lang['tablefiltered'] = '(filtered from _MAX_ total entries)';
            $csvdt_lang['tablelength'] = 'Show _MENU_ entries';
            $csvdt_lang['tableloading'] = 'Loading...';
            $csvdt_lang['tableprocessing'] = 'Processing...';
            $csvdt_lang['tablepagesfirst'] = 'First';
            $csvdt_lang['tablepageslast'] = 'Last';
            $csvdt_lang['tablepagesnext'] = 'Next';
            $csvdt_lang['tablepagesprevious'] = 'Previous';
            $csvdt_lang['tableviewall'] = 'All';
            break;
    }

    $csvdt_return .= '
<script type="text/javascript">
var alreadyReady = false; // The ready function is being called twice on page load.
jQuery(document).ready( function () {
    var table = jQuery("#table'.$atts['name'].'").DataTable({
        "dom": \'<"listtable"fit>pl\',
        "paging": '.$atts['paging'].',
        "info": '.$atts['info'].',
        "filter": '.$atts['filter'].',
        "responsive": true,
        "oLanguage": {
            "sEmptyTable":     "'.$csvdt_lang['norecordsfound'].'",
            "sInfo":           "'.$csvdt_lang['tableshowing'].'",
            "sInfoEmpty":      "'.$csvdt_lang['tableempty'].'",
            "sInfoFiltered":   "'.$csvdt_lang['tablefiltered'].'",
            "sInfoPostFix":    "",
            "sInfoThousands":  ",",
            "sLengthMenu":     "'.$csvdt_lang['tablelength'].'",
            "sLoadingRecords": "'.$csvdt_lang['tableloading'].'",
            "sProcessing":     "'.$csvdt_lang['tableprocessing'].'",
            "sSearch":         "",
            "sZeroRecords":    "'.$csvdt_lang['norecordsfound'].'",
            "oPaginate": {
                "sFirst":    "'.$csvdt_lang['tablepagesfirst'].'",
                "sLast":     "'.$csvdt_lang['tablepageslast'].'",
                "sNext":     "'.$csvdt_lang['tablepagesnext'].'",
                "sPrevious": "'.$csvdt_lang['tablepagesprevious'].'"
            }
        },
        "pageLength": 10,
        "order": [
            [ '.$atts['ordercol'].', "'.$atts['order'].'" ]
        ],
        "lengthMenu": [
            [10, 25, 50, -1],
            [10, 25, 50, "'.$csvdt_lang['tableviewall'].'"]
        ],
        "aoColumnDefs": [
            {
                "bSortable": false,
                "aTargets": [ '.$atts['nosortcols'].' ]
            },
            {
                "sType": "string",
                "aTargets": [ '.$atts['filtercol'].' ]
            }
        ],
        "stateSave": true
    });';

    if ($atts['filter']) {
        $csvdt_return .= '
    // highlight remembered filter on page re-load
    var rememberedFilterTerm = table.state().columns['.$atts['filtercol'].'].search.search;
    if (rememberedFilterTerm && !alreadyReady) {
        // This should only run on the first "ready" event.
        jQuery(".view-filter-btns a span").each(function(index) {
            if (buildFilterRegex(jQuery(this).text().trim()) == rememberedFilterTerm) {
                jQuery(this).parent(\'a\').addClass(\'active\');
                jQuery(this).parent(\'a\').find(\'i\').removeClass(\'fa-circle-o\').addClass(\'fa-dot-circle-o\');
            }
        });
    }
';
    }

    $csvdt_return .= '
alreadyReady = true;
} );
</script>';

    $csvdt_return .= '
<script type="text/javascript">
    jQuery(document).ready( function ()
    {
        var table = jQuery(\'#table'.$atts['name'].'\').removeClass(\'hidden\').DataTable();
        table.order('.$atts['ordercol'].', \''.$atts['order'].'\');
        table.draw();
        jQuery(\'#tableLoading\').addClass(\'hidden\');
    });
</script>

<div class="table-container clearfix">
    <table id="table'.$atts['name'].'" class="table table-list hidden">
        <thead>
            <tr>';

    $csvdt_ratelist = file_get_contents( ((!preg_match("/^\//",$atts['file'])) ? "$csvdt_abspath" : '') . $atts['file']);

    // strip lines containing the strings passed in $striplines arg
    $csvdt_strips = ($atts['striplines']) ? explode(",",$atts['striplines']) : array();
    foreach ($csvdt_strips as $csvdt_strip) {
        $csvdt_ratelist = preg_replace("/.*".$csvdt_strip."[^\n]*\n/","",$csvdt_ratelist);
    }

    // replace strings passed in $search arg with strings passed in $replace arg
    if ($atts['search']) {
        $csvdt_ratelist = str_replace(explode(",",$atts['search']),explode(",",$atts['replace']),$csvdt_ratelist);
    }

    // explode rates to an array of items
    $csvdt_ratelist = explode("\n",$csvdt_ratelist);

    // strip headers (first line) of csv file if $stripheaders arg is set true
    if ($atts['stripheaders']) {
        array_shift($csvdt_ratelist);
    }

    // push new headers if $headers arg has been provided
    if ($atts['headers']) {
        array_unshift($csvdt_ratelist, $atts['headers']);
    }

    // print headers from first array element
    foreach(explode(",",$csvdt_ratelist[0]) as $csvdt_header) {
        $csvdt_return .= '
                <th>'.$csvdt_header.'</th>';
    }

    // remove headers from array of elements
    array_shift($csvdt_ratelist);

    $csvdt_return .= '
            </tr>
        </thead>
        <tbody>';

    foreach ($csvdt_ratelist as $field) {
        if ($field) {
            $csvdt_return .= '
                <tr>';
            $csvdt_align = "left";
            foreach (explode(",",$field) as $td) {
                $csvdt_return .= '
                    <td style="text-align:'.$csvdt_align.'">'.$td.'</td>';
                $csvdt_align = "center";
            }
            $csvdt_return .= '
                </tr>';
        }
    }

    $csvdt_return .= '
        </tbody>
    </table>
    <div class="text-center" id="tableLoading">
        <p><i class="fa fa-spinner fa-spin"></i> '.$csvdt_lang['tableloading'].'</p>
    </div>
</div>';

    return $csvdt_return;

}
add_shortcode( 'csvdatatables', 'csvdt_display_table' );
