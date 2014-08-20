<?php
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
    
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">

    <!-- Latest compiled and minified JavaScript -->
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    
    <title>Shopping list</title>
    
    <script>
        var ShoppingList = {
            add: function(row) {
                $.ajax({
                    type: "POST",
                    url: "/shopping-add.php",
                    data: row.find('input').serialize()
                }).done(function() {
                    
                });
            }
        };
    </script>
</head>
<body>
    <div class="container">
        <!-- Static navbar -->
        <div class="navbar navbar-default" role="navigation">
            <div class="container-fluid">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">Shopping list</a>
                </div>
                <div class="navbar-collapse collapse">
                </div><!--/.nav-collapse -->
            </div><!--/.container-fluid -->
        </div>

        <form action="/shopping-add.php">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $num = 0; ?>
                    <?php foreach ($_SESSION['shopping-list'] as $listItem) { ?>
                        <?php $num++; ?>
                        <tr>
                            <td><?php echo $num; ?></td>
                            <td><?php echo $listItem['title'] ?></td>
                            <td><a href="#" class="btn btn-default btn-danger"><span class="glyphicon glyphicon-remove"></span></a></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <td>Add new</td>
                        <td><input type="text" name="title" value="" class="form-control"></td>
                        <td><a href="#" class="btn btn-default btn-primary" onclick="ShoppingList.add($(this).parent().parent());"><span class="glyphicon glyphicon-ok"></span></a></td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div> <!-- /container -->
</body>
</html>
