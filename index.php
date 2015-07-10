<?php 
  require_once 'config.inc.php';
  global $db_config;

  // initial database connection
  $connection = mysql_connect($db_config['host'], $db_config['username'], $db_config['password']) or die('mysql error');
  mysql_select_db($db_config['database']) or die('mysql error');

  // get customers
  $customers = array();
  $customer_query = "SELECT * FROM customers AS Customer";
  $result = mysql_query($customer_query);
  while ($row = mysql_fetch_array($result)) {
    $customers[] = $row;
  }
?>

<!-- Latest compiled and minified CSS & JS -->
<link rel="stylesheet" media="screen" href="//netdna.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">

<style type="text/css">
  [contentEditable="true"] { 
    background-color: #F2F29A;
    outline: black dotted 1px;
  }
</style>

<div class="well">
  <div class="row">
    <div class="col-md-4">
      logo
    </div>
    <div class="col-md-8">
      Customer Management
    </div>
  </div>

</div>
<table class="table table-striped table-hover">
  <thead>
    <tr>
      <th>Name</th>
      <th>Address</th>
      <th>Phone</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($customers as $key => $customer): ?>
      <tr data-id="<?php echo $customer['id']; ?>" class="customer-row">
        <td data-field="name"><span><?php echo $customer['fullname'] ?></span></td>
        <td data-field="address"><span><?php echo $customer['address'] ?></span></td>
        <td data-field="phone"><span><?php echo $customer['phone'] ?></span></td>
        <td data-field="action">
          <a href="#" data-action="delete"><img src="/images/cancel.png"></a>
          <a href="#" data-action="edit"><img src="/images/edit.png"></a>
          <a href="#" data-action="update" style="display:none"><img src="/images/edit-green.png"></a>
        </td>
      </tr>
    <?php endforeach ?>
    <tr class="addRow">
      <td>
        <input type="text" class="form-control" id="fullname" placeholder="Name">
      </td>
      <td>
        <input type="text" class="form-control" id="address" placeholder="Address">
      </td>
      <td>
        <input type="text" class="form-control" id="phone" placeholder="Phone">
      </td>
      <td><a href="#" data-action="create"><img src="/images/plus.png"></a></td>
    </tr>
  </tbody>
</table>

<script src="//code.jquery.com/jquery.js"></script>
<script src="//netdna.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
<script type="text/javascript">
var Control = {
  serviceUrl: 'services.php',

  init: function() {
    this.binds();
  },

  binds: function() {
    var that = this;
    $('[data-action=delete]').click(function(){
      that.deleteRow(this);
    });

    $('[data-action=create]').click(function(){
      that.createRow();
    });

    $('[data-action=update]').click(function(){
      that.updateRow(this);
    });

    $('[data-action=edit]').click(function(){
      that.editRow(this);
    });
  },

  createRow: function() {
    var that = this;

    var row = $('.addRow');
    var fullname = $(row).find('#fullname').val();
    var address = $(row).find('#address').val();
    var phone = $(row).find('#phone').val();

    var url = that.serviceUrl + '/?action=create&fullname='+fullname+'&address='+address+'&phone='+phone;
    $.ajax({
      url: url
    }).done(function(data){
      data = JSON.parse(data);
      if (data.status=='success') {
        // $(row).remove();
        var last_id = data.data.last_id;

        var new_row = $(
          '<tr data-id="'+last_id+'" class="customer-row">' +
            '<td data-field="name">'+fullname+'</td>' +
            '<td data-field="address">'+address+'</td>' +
            '<td data-field="phone">'+phone+'</td>' +
            '<td data-field="action">' +
              '<a href="#" data-action="delete"><img src="/images/cancel.png"></a>' +
              '<a href="#"><img src="/images/edit.png"></a>' +
            '</td>' +
          '</tr>'
        );

        new_row.insertBefore(row);
        new_row.find('[data-action=delete]').click(function(){
          that.deleteRow(this);
        });

        new_row.find('[data-action=create]').click(function(){
          that.createRow(this);
        });

        new_row.find('[data-action=update]').click(function(){
          that.updateRow(this);
        });

        $(row).find('input').val('');

      } else {
        alert(data.description);
      }
    });

  },

  deleteRow: function(dom) {
    var that = this;
    var row = $(dom).closest('.customer-row');
    var customer_id = $(row).attr('data-id');

    if (!confirm('Delete row')) return;

    var url = that.serviceUrl + '/?action=delete&id=' + customer_id;
    $.ajax({
      url: url
    }).done(function(data){
      data = JSON.parse(data);
      if (data.status=='success') {
        $(row).remove();
      } else {
        alert(data.description);
      }
    });
  },

  editRow: function(dom) {
    $(dom).hide();
    $(dom).closest('td').find('[data-action=update]').show();
    $(dom).closest('td').find('[data-action=delete]').hide();
    $(dom).closest('tr').find('td').not('[data-field=action]').attr('contentEditable', 'true');
  },

  updateRow: function(dom) {
    var that = this;

    var row = $(dom).closest('tr');
    var fullname = $(row).find('[data-field=name]').html();
    var address = $(row).find('[data-field=address]').html();
    var phone = $(row).find('[data-field=phone]').html();
    var id = $(row).attr('data-id');

    console.log(fullname);

    var url = that.serviceUrl + '/?action=update&fullname='+fullname+'&address='+address+'&phone='+phone+'&id='+id;
    $.ajax({
      url: url
    }).done(function(data){
      data = JSON.parse(data);
      if (data.status=='success') {
        alert(data.description);
      } else {
        alert(data.description);
      }
    });

    $(dom).hide();
    $(dom).closest('tr').find('td').not('[data-field=action]').attr('contentEditable', 'false');
    $(dom).closest('td').find('[data-action=edit]').show();
    $(dom).closest('td').find('[data-action=delete]').show();
  }
};

Control.init();
</script>