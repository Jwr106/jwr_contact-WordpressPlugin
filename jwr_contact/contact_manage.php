<?php
if (!defined('WP_ADMIN')) {
    die('Invalid request.');
}
global $wpdb;
$table = 'contact';
$page = isset($_GET['paged']) ? $_GET['paged'] : 1;
$shownums = 20;
$offset = ($page-1)*$shownums;
$sql = "SELECT * FROM {$table} ORDER BY id DESC LIMIT {$offset}, {$shownums}";
$re = $wpdb->get_results($sql);

$sql = "SELECT count(*) as total FROM {$table}";
$count = $wpdb->get_var($sql);
$page_count =  ceil($count / $shownums);
?>

<div class="wrap">
    <h1>Manage Messages</h1>
    <table class="wp-list-table widefat striped posts">
        <thead>
            <tr>
                <th scope="col">Email</th>
                <th scope="col">Phone</th>
                <th scope="col">Message</th>
                <th scope="col">Delete</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($re as $v): ?>
            <tr>
                <td>
                    <?=$v->email?> 
                </td>
                <td>
                    <?=$v->phone?> 
                </td>
                <td>
                    <?=$v->message?> 
                </td>
                <td>
                    <label>
                        <a href="javascript:;" data-id="<?=$v->id?>" class="del_btn">Delete</a>
                    </label>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <div style="margin-top: 15px;">
        <?php
            $args = array(
                'total'=>$page_count,
                'current' =>$page,
                'base' => admin_url('?page=contact&paged=%#%'),
                'prev_text'=>'PREV',
                'next_text'=>'NEXT',
            );
            echo paginate_links($args);
        ?>
    </div>
</div>
<script>
    jQuery(document).ready(function($){
        $('.del_btn').click(function(){
            if(confirm('Are you sure to delete this?')){
                var id = $(this).data('id');
                var url = '<?=admin_url('admin-ajax.php') ?>';
                var data={id: id, action:'contact_del'}
                $.post(url, data, function(){
                    window.location.reload();
                });
            }
        });
    });
</script>